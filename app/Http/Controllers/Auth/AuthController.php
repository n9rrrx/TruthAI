<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => ['nullable', 'file', 'max:2048'], // 2MB max (PHP limit)
            'plan' => ['nullable', 'string', 'in:free,pro'],
            'payment_method' => ['nullable', 'string'],
        ]);

        // Handle avatar upload
        $avatarPath = null;
        \Log::info('Avatar upload check', [
            'hasFile' => $request->hasFile('avatar'),
            'allFiles' => $request->allFiles(),
        ]);
        
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            \Log::info('Avatar file details', [
                'isValid' => $avatar->isValid(),
                'error' => $avatar->getError(),
                'originalName' => $avatar->getClientOriginalName(),
                'size' => $avatar->getSize(),
                'mimeType' => $avatar->getMimeType(),
            ]);
            
            if ($avatar->isValid()) {
                try {
                    $extension = $avatar->getClientOriginalExtension() ?: 'jpg';
                    $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;
                    
                    // Ensure directory exists
                    Storage::disk('public')->makeDirectory('avatars');
                    
                    // Store the file
                    $stored = $avatar->storeAs('avatars', $filename, 'public');
                    \Log::info('Avatar stored', ['stored' => $stored, 'filename' => $filename]);
                    
                    if ($stored) {
                        $avatarPath = '/storage/avatars/' . $filename;
                    }
                } catch (\Exception $e) {
                    \Log::error('Avatar upload failed: ' . $e->getMessage());
                }
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provider' => 'email',
            'avatar' => $avatarPath,
        ]);

        Auth::login($user);

        // Handle Pro plan subscription
        if ($request->plan === 'pro' && $request->payment_method) {
            try {
                $priceId = config('services.stripe.pro_price_id');
                
                if ($priceId && $priceId !== 'price_XXXXXX') {
                    // Create Stripe customer and subscription
                    $user->createOrGetStripeCustomer();
                    $user->updateDefaultPaymentMethod($request->payment_method);
                    $user->newSubscription('default', $priceId)->create($request->payment_method);
                    
                    // Create welcome notification for Pro user
                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'billing',
                        'title' => 'Welcome to TruthAI Pro! 🎉',
                        'message' => 'Your Pro subscription is now active. Enjoy 10,000 scans/day, all detection types, and humanization!',
                        'icon' => '💎',
                        'link' => '/dashboard/billing',
                    ]);

                    return redirect()->route('dashboard')->with('success', 'Welcome to TruthAI Pro! Your subscription is active.');
                }
            } catch (\Exception $e) {
                \Log::error('Pro subscription failed: ' . $e->getMessage());
                // Continue with free account even if subscription fails
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'billing',
                    'title' => 'Subscription Issue',
                    'message' => 'There was an issue processing your payment. Please try upgrading from the billing page.',
                    'icon' => '⚠️',
                    'link' => '/dashboard/billing',
                ]);
            }
        }

        // Create welcome notification for free user
        Notification::create([
            'user_id' => $user->id,
            'type' => 'system',
            'title' => 'Welcome to TruthAI! 🎉',
            'message' => 'Start by pasting text to detect AI-generated content. You have 100 free scans per day!',
            'icon' => '👋',
            'link' => '/dashboard/detector',
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Check if redirect to billing was requested
            if ($request->get('redirect') === 'billing') {
                return redirect()->route('billing');
            }
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
