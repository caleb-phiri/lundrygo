<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Mail\OrderConfirmation;
use App\Mail\OrderStatusUpdate;
use App\Mail\PaymentReceived;
use App\Notifications\PushNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Twilio\Rest\Client as TwilioClient;
use Kreait\Firebase\Factory as FirebaseFactory;
use Illuminate\Support\Str;

class NotificationService
{
    protected $twilio;
    protected $firebase;
    protected $mapService;
    protected $channels;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
        $this->initializeChannels();
        $this->initializeTwilio();
        $this->initializeFirebase();
    }

    /**
     * Initialize notification channels
     */
    protected function initializeChannels(): void
    {
        $this->channels = [
            'email' => config('notifications.channels.email', true),
            'sms' => config('notifications.channels.sms', false),
            'push' => config('notifications.channels.push', true),
            'database' => config('notifications.channels.database', true),
            'webhook' => config('notifications.channels.webhook', false),
        ];
    }

    /**
     * Initialize Twilio for SMS
     */
    protected function initializeTwilio(): void
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        
        if ($sid && $token && $this->channels['sms']) {
            $this->twilio = new TwilioClient($sid, $token);
        }
    }

    /**
     * Initialize Firebase for Push Notifications
     */
    protected function initializeFirebase(): void
    {
        $firebaseConfig = config('services.firebase');
        
        if ($firebaseConfig['enabled'] ?? false) {
            try {
                $this->firebase = (new FirebaseFactory())
                    ->withServiceAccount($firebaseConfig['credentials_path'])
                    ->createMessaging();
            } catch (\Exception $e) {
                Log::warning('Firebase initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Send order confirmation notification
     */
    public function sendOrderConfirmation(Order $order, array $options = []): array
    {
        $user = $order->user;
        $data = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'total' => $order->total,
            'status' => $order->status,
        ];

        return $this->send(
            $user,
            'order_confirmation',
            'Order Confirmed',
            "Your order #{$order->order_number} has been placed successfully.",
            $data,
            ['action_url' => "/orders/{$order->id}", 'action_text' => 'View Order']
        );
    }

    /**
     * Send order status update notification
     */
    public function sendOrderStatusUpdate(Order $order, string $oldStatus, string $newStatus, array $options = []): array
    {
        $user = $order->user;
        
        $statusMessages = [
            'confirmed' => 'Your order has been confirmed',
            'processing' => 'Your order is now being processed',
            'rider_assigned' => 'A rider has been assigned to your order',
            'rider_en_route_pickup' => 'Rider is on the way to pick up your laundry',
            'items_collected' => 'Your items have been collected',
            'washing' => 'Your laundry is being washed',
            'drying' => 'Your laundry is being dried',
            'quality_check' => 'Your items are undergoing quality check',
            'ready_for_delivery' => 'Your order is ready for delivery',
            'out_for_delivery' => 'Your order is out for delivery',
            'delivered' => 'Your order has been delivered',
            'cancelled' => 'Your order has been cancelled',
        ];
        
        $title = "Order Status Update: " . ucfirst(str_replace('_', ' ', $newStatus));
        $message = $statusMessages[$newStatus] ?? "Your order status has been updated to " . str_replace('_', ' ', $newStatus);
        
        $data = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'tracking_url' => "/orders/{$order->id}/tracking",
        ];
        
        // Add ETA if available
        if ($newStatus === 'rider_en_route_pickup' || $newStatus === 'out_for_delivery') {
            $eta = $options['eta'] ?? null;
            if ($eta) {
                $message .= ". Estimated arrival: {$eta} minutes";
                $data['eta_minutes'] = $eta;
            }
        }
        
        return $this->send(
            $user,
            'order_status_update',
            $title,
            $message,
            $data,
            ['action_url' => "/orders/{$order->id}", 'action_text' => 'Track Order', 'priority' => 'high']
        );
    }

    /**
     * Send payment confirmation notification
     */
    public function sendPaymentConfirmation(Order $order, array $paymentDetails): array
    {
        $user = $order->user;
        
        return $this->send(
            $user,
            'payment_confirmation',
            'Payment Received',
            "We have received your payment of \${$order->total} for order #{$order->order_number}",
            [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total,
                'payment_method' => $paymentDetails['method'] ?? 'card',
                'transaction_id' => $paymentDetails['transaction_id'] ?? null,
            ],
            ['action_url' => "/orders/{$order->id}", 'priority' => 'high']
        );
    }

    /**
     * Send rider assignment notification
     */
    public function sendRiderAssignment(Order $order, User $rider): array
    {
        // Send to customer
        $customerNotification = $this->send(
            $order->user,
            'rider_assigned',
            'Rider Assigned',
            "A rider has been assigned to pick up your order #{$order->order_number}",
            [
                'order_id' => $order->id,
                'rider_name' => $rider->name,
                'rider_phone' => $rider->phone,
                'tracking_url' => "/orders/{$order->id}/tracking",
            ]
        );
        
        // Send to rider
        $riderNotification = $this->send(
            $rider,
            'new_pickup_assignment',
            'New Pickup Assignment',
            "You have been assigned to pick up order #{$order->order_number}",
            [
                'order_id' => $order->id,
                'pickup_address' => $order->pickupLocation->full_address,
                'customer_name' => $order->user->name,
                'customer_phone' => $order->user->phone,
            ],
            ['action_url' => "/rider/orders/{$order->id}", 'priority' => 'high']
        );
        
        return [
            'customer' => $customerNotification,
            'rider' => $riderNotification,
        ];
    }

    /**
     * Send promotion notification
     */
    public function sendPromotion(User $user, array $promotion, array $options = []): array
    {
        return $this->send(
            $user,
            'promotion',
            $promotion['title'] ?? 'Special Offer Just for You!',
            $promotion['message'],
            [
                'promotion_code' => $promotion['code'] ?? null,
                'discount' => $promotion['discount'] ?? null,
                'expires_at' => $promotion['expires_at'] ?? null,
            ],
            ['action_url' => $promotion['url'] ?? '/promotions', 'action_text' => 'Claim Offer']
        );
    }

    /**
     * Send reminder notification
     */
    public function sendReminder(User $user, string $type, array $data = []): array
    {
        $reminders = [
            'pending_payment' => [
                'title' => 'Payment Reminder',
                'message' => 'Your order requires payment to proceed',
                'action_url' => '/checkout',
            ],
            'pending_review' => [
                'title' => 'Share Your Experience',
                'message' => 'How was your laundry service? Leave a review!',
                'action_url' => "/orders/{$data['order_id']}/review",
            ],
            'reorder' => [
                'title' => 'Time for Laundry?',
                'message' => 'Reorder your favorite laundry services with one click',
                'action_url' => '/reorder',
            ],
        ];
        
        $reminder = $reminders[$type] ?? $reminders['pending_payment'];
        
        return $this->send(
            $user,
            'reminder',
            $reminder['title'],
            $reminder['message'],
            $data,
            ['action_url' => $reminder['action_url'], 'priority' => 'normal']
        );
    }

    /**
     * Send bulk notification to multiple users
     */
    public function sendBulk(array $users, string $type, string $title, string $message, array $data = [], array $options = []): array
    {
        $results = [];
        
        // Process in chunks to avoid memory issues
        $chunks = array_chunk($users, 100);
        
        foreach ($chunks as $chunk) {
            foreach ($chunk as $user) {
                if ($user instanceof User) {
                    $results[] = $this->send($user, $type, $title, $message, $data, $options);
                }
            }
            
            // Rate limiting for external APIs
            if ($this->channels['email'] || $this->channels['sms']) {
                sleep(1);
            }
        }
        
        return [
            'total' => count($results),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
        ];
    }

    /**
     * Main send method with multi-channel support
     */
    public function send(User $user, string $type, string $title, string $message, array $data = [], array $options = []): array
    {
        // Check user preferences
        $preferences = $this->getUserPreferences($user);
        
        if ($preferences && $preferences->is_unsubscribed) {
            return ['success' => false, 'reason' => 'User unsubscribed'];
        }
        
        $results = [];
        $notification = null;
        
        // Create database notification first
        if ($this->channels['database'] && $this->shouldSend($preferences, 'database', $type)) {
            $notification = $this->sendDatabaseNotification($user, $type, $title, $message, $data, $options);
            $results['database'] = ['success' => true, 'id' => $notification->id];
        }
        
        // Send email
        if ($this->channels['email'] && $this->shouldSend($preferences, 'email', $type)) {
            $results['email'] = $this->sendEmailNotification($user, $type, $title, $message, $data, $options);
        }
        
        // Send SMS
        if ($this->channels['sms'] && $this->shouldSend($preferences, 'sms', $type) && $user->phone) {
            $results['sms'] = $this->sendSmsNotification($user, $message, $options);
        }
        
        // Send Push Notification
        if ($this->channels['push'] && $this->shouldSend($preferences, 'push', $type)) {
            $results['push'] = $this->sendPushNotification($user, $title, $message, $data, $options);
        }
        
        // Send Webhook
        if ($this->channels['webhook'] && ($options['send_webhook'] ?? false)) {
            $results['webhook'] = $this->sendWebhookNotification($user, $type, $title, $message, $data);
        }
        
        // Update notification with delivery status
        if ($notification) {
            $notification->update([
                'email_status' => $results['email']['status'] ?? 'pending',
                'sms_status' => $results['sms']['status'] ?? 'pending',
                'push_status' => $results['push']['status'] ?? 'pending',
                'email_sent_at' => $results['email']['sent_at'] ?? null,
                'sms_sent_at' => $results['sms']['sent_at'] ?? null,
                'push_sent_at' => $results['push']['sent_at'] ?? null,
            ]);
        }
        
        return [
            'success' => true,
            'notification_id' => $notification->id ?? null,
            'channels' => $results,
            'user_id' => $user->id,
        ];
    }

    /**
     * Send database notification
     */
    protected function sendDatabaseNotification(User $user, string $type, string $title, string $message, array $data = [], array $options = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'user_type' => $user->user_type ?? 'customer',
            'notification_uuid' => (string) Str::uuid(),
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'short_message' => substr($message, 0, 100),
            'data' => $data,
            'action_text' => $options['action_text'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'action_type' => $options['action_type'] ?? 'deeplink',
            'send_email' => $options['send_email'] ?? false,
            'send_sms' => $options['send_sms'] ?? false,
            'send_push' => $options['send_push'] ?? false,
            'send_in_app' => true,
            'priority' => $options['priority'] ?? 'normal',
            'is_critical' => $options['is_critical'] ?? false,
            'template_id' => $options['template_id'] ?? null,
            'source' => $options['source'] ?? 'system',
            'triggered_by' => $options['triggered_by'] ?? null,
            'sent_at' => now(),
        ]);
    }

    /**
     * Send email notification
     */
    protected function sendEmailNotification(User $user, string $type, string $title, string $message, array $data = [], array $options = []): array
    {
        if (!$user->email) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'No email address'];
        }
        
        try {
            $mailable = null;
            
            switch ($type) {
                case 'order_confirmation':
                    $mailable = new OrderConfirmation(Order::find($data['order_id']));
                    break;
                case 'order_status_update':
                    $mailable = new OrderStatusUpdate(Order::find($data['order_id']), $message);
                    break;
                case 'payment_confirmation':
                    $mailable = new PaymentReceived(Order::find($data['order_id']));
                    break;
                default:
                    // Use generic email
                    $mailable = new \App\Mail\GenericNotification($title, $message, $data);
            }
            
            if ($options['queue'] ?? true) {
                Mail::to($user->email)->queue($mailable);
            } else {
                Mail::to($user->email)->send($mailable);
            }
            
            return [
                'success' => true,
                'status' => 'sent',
                'sent_at' => now(),
            ];
            
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS notification via Twilio
     */
    protected function sendSmsNotification(User $user, string $message, array $options = []): array
    {
        if (!$this->twilio) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'Twilio not configured'];
        }
        
        if (!$user->phone) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'No phone number'];
        }
        
        try {
            // Truncate message to SMS limit
            $message = substr($message, 0, 160);
            
            $sms = $this->twilio->messages->create(
                $user->phone,
                [
                    'from' => config('services.twilio.from_number'),
                    'body' => $message,
                ]
            );
            
            return [
                'success' => true,
                'status' => 'sent',
                'message_id' => $sms->sid,
                'sent_at' => now(),
            ];
            
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send push notification via Firebase
     */
    protected function sendPushNotification(User $user, string $title, string $body, array $data = [], array $options = []): array
    {
        if (!$this->firebase) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'Firebase not configured'];
        }
        
        // Get user's device tokens
        $preferences = $this->getUserPreferences($user);
        $tokens = $preferences ? ($preferences->device_tokens ?? []) : [];
        
        if (empty($tokens)) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'No device tokens'];
        }
        
        try {
            $message = \Kreait\Firebase\Messaging\CloudMessage::new()
                ->withNotification([
                    'title' => $title,
                    'body' => $body,
                ])
                ->withData(array_merge($data, [
                    'click_action' => $options['action_url'] ?? 'FLUTTER_NOTIFICATION_CLICK',
                    'type' => $options['type'] ?? 'general',
                ]))
                ->withAndroidConfig([
                    'priority' => 'high',
                ])
                ->withApnsConfig([
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                ]);
            
            $result = $this->firebase->sendMulticast($message, $tokens);
            
            return [
                'success' => true,
                'status' => 'sent',
                'success_count' => $result->successes()->count(),
                'failure_count' => $result->failures()->count(),
                'sent_at' => now(),
            ];
            
        } catch (\Exception $e) {
            Log::error('Push notification failed: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send webhook notification
     */
    protected function sendWebhookNotification(User $user, string $type, string $title, string $message, array $data = []): array
    {
        $webhookUrl = config('notifications.webhook_url');
        
        if (!$webhookUrl) {
            return ['success' => false, 'status' => 'failed', 'reason' => 'No webhook URL'];
        }
        
        try {
            $payload = [
                'event' => $type,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'timestamp' => now()->toIso8601String(),
            ];
            
            $response = \Illuminate\Support\Facades\Http::post($webhookUrl, $payload);
            
            return [
                'success' => $response->successful(),
                'status' => $response->successful() ? 'sent' : 'failed',
                'status_code' => $response->status(),
                'sent_at' => now(),
            ];
            
        } catch (\Exception $e) {
            Log::error('Webhook failed: ' . $e->getMessage());
            return [
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get user notification preferences
     */
    protected function getUserPreferences(User $user): ?NotificationPreference
    {
        return Cache::remember("notification_preferences_{$user->id}", 3600, function () use ($user) {
            return NotificationPreference::where('user_id', $user->id)->first();
        });
    }

    /**
     * Check if notification should be sent based on user preferences
     */
    protected function shouldSend(?NotificationPreference $preferences, string $channel, string $type): bool
    {
        if (!$preferences) {
            return true;
        }
        
        // Check channel enabled
        $channelEnabled = "{$channel}_enabled";
        if (property_exists($preferences, $channelEnabled) && !$preferences->$channelEnabled) {
            return false;
        }
        
        // Check type-specific preferences
        $typeField = "{$type}_notifications";
        if (property_exists($preferences, $typeField) && $preferences->$typeField) {
            $prefs = $preferences->$typeField;
            if (isset($prefs[$channel]) && $prefs[$channel] === false) {
                return false;
            }
        }
        
        // Check quiet hours
        if ($preferences->quiet_hours_enabled && $this->isInQuietHours($preferences)) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if current time is within quiet hours
     */
    protected function isInQuietHours(NotificationPreference $preferences): bool
    {
        $currentHour = (int) now()->format('H');
        $startHour = (int) ($preferences->quiet_hours_start?->format('H') ?? 22);
        $endHour = (int) ($preferences->quiet_hours_end?->format('H') ?? 8);
        
        if ($startHour > $endHour) {
            return $currentHour >= $startHour || $currentHour <= $endHour;
        }
        
        return $currentHour >= $startHour && $currentHour <= $endHour;
    }

    /**
     * Send admin alert for critical issues
     */
    public function sendAdminAlert(string $title, string $message, array $data = [], array $options = []): array
    {
        $admins = User::where('user_type', 'admin')
            ->orWhere('is_admin', true)
            ->get();
        
        $results = [];
        
        foreach ($admins as $admin) {
            $results[] = $this->send(
                $admin,
                'admin_alert',
                $title,
                $message,
                $data,
                array_merge($options, ['priority' => 'high', 'is_critical' => true])
            );
        }
        
        return $results;
    }

    /**
     * Send daily digest
     */
    public function sendDailyDigest(User $user, array $notifications): array
    {
        if (empty($notifications)) {
            return ['success' => true, 'message' => 'No notifications to digest'];
        }
        
        $digest = [
            'date' => now()->toDateString(),
            'total_count' => count($notifications),
            'notifications' => $notifications,
        ];
        
        return $this->send(
            $user,
            'daily_digest',
            "Your Daily Digest - " . now()->format('F j, Y'),
            "You have {$digest['total_count']} notifications today",
            $digest,
            ['priority' => 'low']
        );
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
        
        if ($notification && !$notification->is_read) {
            $notification->markAsRead();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all user notifications as read
     */
    public function markAllAsRead(int $userId): int
    {
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        return $count;
    }

    /**
     * Get unread notifications for user
     */
    public function getUnreadNotifications(int $userId, int $limit = 50): array
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('priority')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Clean old notifications
     */
    public function cleanOldNotifications(int $daysOld = 30): int
    {
        $deleted = Notification::where('created_at', '<', now()->subDays($daysOld))
            ->where('is_read', true)
            ->delete();
        
        return $deleted;
    }
}