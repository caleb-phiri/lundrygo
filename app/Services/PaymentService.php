<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class PaymentService
{
    private ?StripeClient $stripe = null;
    private ?string $provider;
    private array $config;
    private MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
        $this->provider = Config::get('payment.default_provider', 'stripe');
        $this->config = Config::get('payment.providers.' . $this->provider, []);
        
        $this->initializeProvider();
    }

    /**
     * Initialize the payment provider
     */
    private function initializeProvider(): void
    {
        switch ($this->provider) {
            case 'stripe':
                $secretKey = $this->config['secret_key'] ?? env('STRIPE_SECRET_KEY');
                if ($secretKey) {
                    $this->stripe = new StripeClient($secretKey);
                }
                break;
            case 'paypal':
                // Initialize PayPal client
                break;
            case 'razorpay':
                // Initialize Razorpay client
                break;
        }
    }

    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, array $paymentDetails): array
    {
        DB::beginTransaction();
        
        try {
            // Create payment record first
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'payment_method' => $paymentDetails['method'] ?? 'card',
                'payment_method_type' => $paymentDetails['type'] ?? 'card',
                'transaction_id' => $this->generateTransactionId(),
                'amount' => $order->total,
                'currency' => $paymentDetails['currency'] ?? 'USD',
                'status' => 'processing',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            // Process with selected provider
            $result = $this->processWithProvider($order, $payment, $paymentDetails);
            
            if ($result['success']) {
                $payment->markAsCompleted();
                $this->updateOrderAfterPayment($order, $payment);
                DB::commit();
            } else {
                $payment->markAsFailed($result['message'] ?? 'Payment failed');
                DB::rollBack();
            }
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage(),
                'error_code' => $e->getCode()
            ];
        }
    }

    /**
     * Process payment with the configured provider
     */
    private function processWithProvider(Order $order, Payment $payment, array $paymentDetails): array
    {
        switch ($this->provider) {
            case 'stripe':
                return $this->processStripePayment($order, $payment, $paymentDetails);
            case 'paypal':
                return $this->processPayPalPayment($order, $payment, $paymentDetails);
            case 'razorpay':
                return $this->processRazorpayPayment($order, $payment, $paymentDetails);
            default:
                // Cash on delivery
                return $this->processCashPayment($order, $payment);
        }
    }

    /**
     * Process Stripe payment
     */
    private function processStripePayment(Order $order, Payment $payment, array $details): array
    {
        if (!$this->stripe) {
            return [
                'success' => false,
                'message' => 'Stripe is not configured properly'
            ];
        }

        try {
            $paymentIntentData = [
                'amount' => (int)($order->total * 100),
                'currency' => strtolower($payment->currency),
                'metadata' => [
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    'payment_id' => $payment->id,
                ],
                'description' => "Order #{$order->order_number} - Laundry Service",
            ];

            // Handle different payment methods
            if (isset($details['payment_method_id'])) {
                $paymentIntentData['payment_method'] = $details['payment_method_id'];
                $paymentIntentData['confirmation_method'] = 'manual';
                $paymentIntentData['confirm'] = true;
            } elseif (isset($details['payment_intent_id'])) {
                // For 3D Secure or additional authentication
                $paymentIntent = $this->stripe->paymentIntents->retrieve($details['payment_intent_id']);
                $paymentIntent = $this->stripe->paymentIntents->confirm($paymentIntent->id);
                return $this->handleStripePaymentResponse($paymentIntent, $payment);
            } elseif (isset($details['setup_intent_id'])) {
                // For saved payment methods
                $paymentIntentData['setup_future_usage'] = 'off_session';
                $paymentIntentData['customer'] = $this->getOrCreateStripeCustomer($order->user);
            }

            $paymentIntent = $this->stripe->paymentIntents->create($paymentIntentData);
            
            // Handle 3D Secure requirement
            if ($paymentIntent->status === 'requires_action' && 
                $paymentIntent->next_action->type === 'use_stripe_sdk') {
                return [
                    'success' => false,
                    'requires_action' => true,
                    'payment_intent_client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'message' => 'Additional authentication required'
                ];
            }
            
            return $this->handleStripePaymentResponse($paymentIntent, $payment);
            
        } catch (\Stripe\Exception\CardException $e) {
            return $this->handleStripeError($e, $payment);
        } catch (\Exception $e) {
            return $this->handleStripeError($e, $payment);
        }
    }

    /**
     * Handle Stripe payment response
     */
    private function handleStripePaymentResponse($paymentIntent, Payment $payment): array
    {
        if ($paymentIntent->status === 'succeeded') {
            $payment->update([
                'transaction_id' => $paymentIntent->id,
                'gateway_payment_id' => $paymentIntent->id,
                'gateway_response' => $paymentIntent->toArray(),
                'paid_at' => now(),
                'status' => 'completed',
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
            ];
        }
        
        if ($paymentIntent->status === 'processing') {
            $payment->update([
                'transaction_id' => $paymentIntent->id,
                'status' => 'processing',
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'status' => 'processing',
                'message' => 'Payment is being processed'
            ];
        }
        
        return [
            'success' => false,
            'message' => "Payment failed with status: {$paymentIntent->status}",
            'status' => $paymentIntent->status,
        ];
    }

    /**
     * Process Cash on Delivery payment
     */
    private function processCashPayment(Order $order, Payment $payment): array
    {
        $payment->update([
            'status' => 'pending',
            'payment_method' => 'cash',
            'paid_at' => null,
        ]);
        
        return [
            'success' => true,
            'transaction_id' => $payment->transaction_id,
            'message' => 'Cash on delivery selected',
            'requires_cash' => true,
        ];
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment(Order $order, Payment $payment, array $details): array
    {
        // Implement PayPal integration
        return [
            'success' => false,
            'message' => 'PayPal integration coming soon'
        ];
    }

    /**
     * Process Razorpay payment
     */
    private function processRazorpayPayment(Order $order, Payment $payment, array $details): array
    {
        // Implement Razorpay integration
        return [
            'success' => false,
            'message' => 'Razorpay integration coming soon'
        ];
    }

    /**
     * Process refund for an order
     */
    public function processRefund(Order $order, float $amount, string $reason, ?string $notes = null): array
    {
        DB::beginTransaction();
        
        try {
            $payment = Payment::where('order_id', $order->id)
                ->where('status', 'completed')
                ->latest()
                ->first();
            
            if (!$payment) {
                return [
                    'success' => false,
                    'message' => 'No completed payment found for this order'
                ];
            }
            
            if ($amount > ($payment->amount - $payment->refunded_amount)) {
                return [
                    'success' => false,
                    'message' => 'Refund amount exceeds available balance'
                ];
            }
            
            // Create refund record
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'refund_transaction_id' => $this->generateTransactionId('REF'),
                'amount' => $amount,
                'reason' => $reason,
                'notes' => $notes,
                'status' => 'processing',
            ]);
            
            // Process refund with provider
            $result = $this->processRefundWithProvider($payment, $amount);
            
            if ($result['success']) {
                $refund->markAsCompleted();
                $this->updateOrderAfterRefund($order, $payment, $amount);
                DB::commit();
                
                return [
                    'success' => true,
                    'refund_id' => $refund->id,
                    'amount' => $amount,
                    'transaction_id' => $refund->refund_transaction_id,
                ];
            } else {
                $refund->markAsFailed($result['message'] ?? 'Refund failed');
                DB::rollBack();
                
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Refund processing failed'
                ];
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process refund with payment provider
     */
    private function processRefundWithProvider(Payment $payment, float $amount): array
    {
        switch ($this->provider) {
            case 'stripe':
                return $this->processStripeRefund($payment, $amount);
            default:
                return [
                    'success' => false,
                    'message' => 'Refunds not supported for this payment method'
                ];
        }
    }

    /**
     * Process Stripe refund
     */
    private function processStripeRefund(Payment $payment, float $amount): array
    {
        if (!$this->stripe) {
            return ['success' => false, 'message' => 'Stripe not configured'];
        }
        
        try {
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $payment->gateway_payment_id,
                'amount' => (int)($amount * 100),
                'metadata' => [
                    'order_number' => $payment->order->order_number,
                    'refund_reason' => $payment->refund_reason ?? 'Customer requested',
                ],
            ]);
            
            $payment->update([
                'refunded_amount' => $payment->refunded_amount + $amount,
                'refunded_at' => now(),
            ]);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $amount,
            ];
            
        } catch (\Exception $e) {
            Log::error('Stripe refund failed: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Create payment intent for additional authentication
     */
    public function createPaymentIntent(Order $order, array $options = []): array
    {
        if (!$this->stripe) {
            return ['success' => false, 'message' => 'Stripe not configured'];
        }
        
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => (int)($order->total * 100),
                'currency' => 'usd',
                'metadata' => [
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                ],
                'description' => "Order #{$order->order_number} - Laundry Service",
            ]);
            
            return [
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to create payment intent: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to initialize payment'
            ];
        }
    }

    /**
     * Confirm payment intent after 3D Secure
     */
    public function confirmPaymentIntent(string $paymentIntentId): array
    {
        if (!$this->stripe) {
            return ['success' => false, 'message' => 'Stripe not configured'];
        }
        
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            
            if ($paymentIntent->status === 'succeeded') {
                $payment = Payment::where('gateway_payment_id', $paymentIntentId)->first();
                if ($payment) {
                    $payment->markAsCompleted();
                    $this->updateOrderAfterPayment($payment->order, $payment);
                }
                
                return [
                    'success' => true,
                    'status' => 'succeeded',
                    'transaction_id' => $paymentIntentId,
                ];
            }
            
            return [
                'success' => false,
                'status' => $paymentIntent->status,
                'message' => "Payment status: {$paymentIntent->status}"
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to confirm payment: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to confirm payment'
            ];
        }
    }

    /**
     * Get or create Stripe customer
     */
    private function getOrCreateStripeCustomer($user): ?string
    {
        if (!$this->stripe) {
            return null;
        }
        
        try {
            if ($user->stripe_customer_id) {
                return $user->stripe_customer_id;
            }
            
            $customer = $this->stripe->customers->create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);
            
            $user->update(['stripe_customer_id' => $customer->id]);
            
            return $customer->id;
            
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe customer: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId(string $prefix = 'TXN'): string
    {
        return $prefix . '_' . strtoupper(uniqid()) . '_' . date('YmdHis');
    }

    /**
     * Update order after successful payment
     */
    private function updateOrderAfterPayment(Order $order, Payment $payment): void
    {
        $order->update([
            'payment_status' => 'paid',
            'paid_amount' => $order->paid_amount + $payment->amount,
            'due_amount' => max(0, $order->total - ($order->paid_amount + $payment->amount)),
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'payment_confirmed_at' => now(),
        ]);
        
        // If order is fully paid, update status
        if ($order->due_amount <= 0) {
            $order->update([
                'status' => 'confirmed',
                'order_confirmed_at' => now(),
            ]);
        }
    }

    /**
     * Update order after refund
     */
    private function updateOrderAfterRefund(Order $order, Payment $payment, float $amount): void
    {
        $order->update([
            'paid_amount' => max(0, $order->paid_amount - $amount),
            'due_amount' => $order->total - $order->paid_amount,
        ]);
        
        // If full refund, update order status
        if ($order->paid_amount <= 0) {
            $order->update([
                'payment_status' => 'refunded',
                'status' => 'refunded',
            ]);
        } else {
            $order->update([
                'payment_status' => 'partial',
            ]);
        }
    }

    /**
     * Handle Stripe errors
     */
    private function handleStripeError($exception, Payment $payment): array
    {
        $errorMessage = $exception->getMessage();
        
        $payment->addLog('error', 'failed', $errorMessage, [
            'response' => ['error' => $errorMessage]
        ]);
        
        $payment->update([
            'status' => 'failed',
            'failure_reason' => $errorMessage,
            'failed_at' => now(),
        ]);
        
        // Handle specific Stripe errors
        if (method_exists($exception, 'getError')) {
            $stripeError = $exception->getError();
            
            switch ($stripeError->code ?? null) {
                case 'card_declined':
                    return ['success' => false, 'message' => 'Your card was declined.'];
                case 'insufficient_funds':
                    return ['success' => false, 'message' => 'Insufficient funds on card.'];
                case 'expired_card':
                    return ['success' => false, 'message' => 'Your card has expired.'];
                default:
                    return ['success' => false, 'message' => $errorMessage];
            }
        }
        
        return ['success' => false, 'message' => $errorMessage];
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Payment $payment): array
    {
        if (!$this->stripe || $payment->gateway !== 'stripe') {
            return [
                'success' => false,
                'message' => 'Cannot fetch status for this payment method'
            ];
        }
        
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($payment->gateway_payment_id);
            
            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'currency' => $paymentIntent->currency,
                'last_error' => $paymentIntent->last_payment_error ?? null,
            ];
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment status: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to fetch payment status'
            ];
        }
    }

    /**
     * Calculate split payment for multiple parties
     */
    public function calculateSplitPayment(Order $order, array $parties): array
    {
        $total = $order->total;
        $remaining = $total;
        $splits = [];
        
        foreach ($parties as $index => $party) {
            $amount = 0;
            
            if ($party['type'] === 'percentage') {
                $amount = ($total * $party['value']) / 100;
            } elseif ($party['type'] === 'fixed') {
                $amount = min($party['value'], $remaining);
            }
            
            $amount = round($amount, 2);
            $remaining -= $amount;
            
            $splits[] = [
                'party' => $party['name'],
                'amount' => $amount,
                'type' => $party['type'],
            ];
            
            // Last party gets the remaining amount
            if ($index === count($parties) - 1 && $remaining > 0) {
                $splits[$index]['amount'] += round($remaining, 2);
            }
        }
        
        return [
            'success' => true,
            'total' => $total,
            'splits' => $splits,
        ];
    }

    /**
     * Validate webhook signature
     */
    public function validateWebhookSignature(string $payload, string $signature, string $webhookSecret): bool
    {
        if ($this->provider !== 'stripe') {
            return false;
        }
        
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
            return true;
        } catch (\Exception $e) {
            Log::error('Webhook signature validation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(array $payload): void
    {
        $eventType = $payload['type'] ?? null;
        
        switch ($eventType) {
            case 'payment_intent.succeeded':
                $paymentIntent = $payload['data']['object'];
                $this->handlePaymentSuccess($paymentIntent);
                break;
                
            case 'payment_intent.payment_failed':
                $paymentIntent = $payload['data']['object'];
                $this->handlePaymentFailure($paymentIntent);
                break;
                
            case 'charge.refunded':
                $charge = $payload['data']['object'];
                $this->handleRefundSuccess($charge);
                break;
        }
    }

    /**
     * Handle successful payment webhook
     */
    private function handlePaymentSuccess($paymentIntent): void
    {
        $payment = Payment::where('gateway_payment_id', $paymentIntent['id'])->first();
        
        if ($payment && $payment->status !== 'completed') {
            $payment->markAsCompleted();
            $this->updateOrderAfterPayment($payment->order, $payment);
        }
    }

    /**
     * Handle payment failure webhook
     */
    private function handlePaymentFailure($paymentIntent): void
    {
        $payment = Payment::where('gateway_payment_id', $paymentIntent['id'])->first();
        
        if ($payment) {
            $payment->markAsFailed($paymentIntent['last_payment_error']['message'] ?? 'Payment failed');
        }
    }

    /**
     * Handle refund success webhook
     */
    private function handleRefundSuccess($charge): void
    {
        $payment = Payment::where('gateway_payment_id', $charge['payment_intent'])->first();
        
        if ($payment) {
            $refund = Refund::where('payment_id', $payment->id)
                ->where('status', 'processing')
                ->first();
                
            if ($refund) {
                $refund->markAsCompleted();
                $this->updateOrderAfterRefund($payment->order, $payment, $refund->amount);
            }
        }
    }
}