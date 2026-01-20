<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Exceptions\IncompletePayment;

class BillingController extends Controller
{
    /**
     * Show billing/pricing page
     */
    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');
        
        return view('dashboard.billing', [
            'user' => $user,
            'subscription' => $subscription,
            'isPro' => $user->isPro(),
        ]);
    }

    /**
     * Show checkout page with embedded payment form
     */
    public function showCheckout()
    {
        $user = Auth::user();
        
        // If already subscribed, redirect to billing
        if ($user->isPro()) {
            return redirect()->route('billing')->with('info', 'You already have a Pro subscription.');
        }

        return view('dashboard.checkout');
    }

    /**
     * Handle subscription creation with payment method
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        // If already subscribed, return error
        if ($user->isPro()) {
            return response()->json(['error' => 'You already have a Pro subscription.'], 400);
        }

        $priceId = config('services.stripe.pro_price_id');
        
        if (!$priceId || $priceId === 'price_XXXXXX') {
            return response()->json(['error' => 'Stripe price ID not configured.'], 500);
        }

        try {
            $paymentMethod = $request->input('payment_method');
            
            // Create or get Stripe customer
            $user->createOrGetStripeCustomer();
            
            // Update default payment method
            $user->updateDefaultPaymentMethod($paymentMethod);
            
            // Create subscription
            $subscription = $user->newSubscription('default', $priceId)->create($paymentMethod);
            
            // Create notification for successful upgrade
            $user->notifications()->create([
                'type' => 'billing',
                'title' => 'Welcome to Pro! 🎉',
                'message' => 'Your subscription is now active. Enjoy 10,000 scans/day, all detection types, and humanization!',
                'icon' => '💎',
                'link' => '/dashboard/billing',
            ]);

            return response()->json(['success' => true]);

        } catch (IncompletePayment $e) {
            // Payment requires additional action (3D Secure)
            return response()->json([
                'requires_action' => true,
                'client_secret' => $e->payment->client_secret,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle successful payment (redirect from embedded form)
     */
    public function success(Request $request)
    {
        return redirect()->route('billing')
            ->with('success', 'Welcome to Pro! Your subscription is now active.');
    }

    /**
     * Handle cancelled payment
     */
    public function cancel()
    {
        return redirect()->route('billing')
            ->with('info', 'Checkout was cancelled. You can upgrade anytime.');
    }

    /**
     * Redirect to Stripe Customer Portal
     */
    public function portal(Request $request)
    {
        return $request->user()->redirectToBillingPortal(route('billing'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request)
    {
        // Laravel Cashier handles webhooks automatically
        // This is a placeholder for custom webhook handling if needed
        
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            return response('Webhook secret not configured', 400);
        }

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sigHeader, $secret
            );
        } catch (\Exception $e) {
            return response('Webhook error: ' . $e->getMessage(), 400);
        }

        // Handle specific events
        switch ($event->type) {
            case 'customer.subscription.created':
                // Subscription created
                break;
            case 'customer.subscription.deleted':
                // Subscription cancelled
                break;
        }

        return response('Webhook handled', 200);
    }
}
