@extends('layouts.dashboard')

@section('title', 'Upgrade to Pro')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-brand-primary to-brand-accent mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Upgrade to Pro</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2">Unlock unlimited potential with TruthAI Pro</p>
    </div>

    <!-- Plan Summary -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">TruthAI Pro</h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm">Monthly subscription</p>
            </div>
            <div class="text-right">
                <span class="text-3xl font-bold text-slate-900 dark:text-white">$29</span>
                <span class="text-slate-500 dark:text-slate-400">/month</span>
            </div>
        </div>
        <div class="border-t border-slate-200 dark:border-white/10 pt-4">
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    10,000 scans per day
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    All detection types (Text, Image, Video)
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Text humanization
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    API access
                </li>
                <li class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Priority support
                </li>
            </ul>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Payment Information</h3>
        
        <form id="payment-form">
            @csrf
            
            <!-- Card Element -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Card Details</label>
                <div id="card-element" class="p-4 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10">
                    <!-- Stripe Element will be inserted here -->
                </div>
                <div id="card-errors" class="text-red-500 text-sm mt-2" role="alert"></div>
            </div>

            <!-- Cardholder Name -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cardholder Name</label>
                <input type="text" id="cardholder-name" placeholder="Name on card" required
                    class="w-full p-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 focus:border-brand-primary outline-none text-slate-900 dark:text-white">
            </div>

            <!-- Error Display -->
            <div id="payment-error" class="hidden mb-4 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm"></div>

            <!-- Submit Button -->
            <button type="submit" id="submit-button" 
                class="w-full py-4 rounded-xl bg-gradient-to-r from-brand-primary to-brand-accent text-white font-bold text-lg hover:opacity-90 transition-opacity flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                <span id="button-text">Pay $29 and Subscribe</span>
                <svg id="spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>

            <!-- Security Note -->
            <div class="flex items-center justify-center gap-2 mt-4 text-xs text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <span>Secured by Stripe. Cancel anytime.</span>
            </div>
        </form>
    </div>

    <!-- Back Link -->
    <div class="text-center mt-6">
        <a href="{{ route('billing') }}" class="text-slate-500 dark:text-slate-400 hover:text-brand-primary">
            ← Back to billing
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    
    // Custom styling for the card element
    const style = {
        base: {
            color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#1e293b',
            fontFamily: 'Inter, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: document.documentElement.classList.contains('dark') ? '#64748b' : '#94a3b8'
            }
        },
        invalid: {
            color: '#ef4444',
            iconColor: '#ef4444'
        }
    };

    const cardElement = elements.create('card', { style: style });
    cardElement.mount('#card-element');

    // Handle card errors
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const spinner = document.getElementById('spinner');
    const paymentError = document.getElementById('payment-error');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        // Disable button and show spinner
        submitButton.disabled = true;
        buttonText.textContent = 'Processing...';
        spinner.classList.remove('hidden');
        paymentError.classList.add('hidden');

        const cardholderName = document.getElementById('cardholder-name').value;

        try {
            // Create payment method
            const { paymentMethod, error: pmError } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: cardholderName
                }
            });

            if (pmError) {
                throw new Error(pmError.message);
            }

            // Send to server to create subscription
            const response = await fetch('{{ route("billing.subscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    payment_method: paymentMethod.id
                })
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            if (result.requires_action) {
                // 3D Secure authentication required
                const { error: confirmError } = await stripe.confirmCardPayment(result.client_secret);
                
                if (confirmError) {
                    throw new Error(confirmError.message);
                }
            }

            // Success! Redirect to billing page
            window.location.href = '{{ route("billing.success") }}?subscribed=1';

        } catch (error) {
            paymentError.textContent = error.message;
            paymentError.classList.remove('hidden');
            submitButton.disabled = false;
            buttonText.textContent = 'Pay $29 and Subscribe';
            spinner.classList.add('hidden');
        }
    });
</script>
@endsection
