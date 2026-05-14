<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Tracking;
use App\Models\TrackingAlert;
use App\Models\TrackingSummary;
use App\Events\OrderStatusUpdated;
use App\Events\RiderLocationUpdated;
use App\Notifications\OrderStatusNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\MapService;
use App\Services\NotificationService;

class TrackingService
{
    protected MapService $mapService;
    protected NotificationService $notificationService;

    public function __construct(
        MapService $mapService,
        NotificationService $notificationService
    ) {
        $this->mapService = $mapService;
        $this->notificationService = $notificationService;
    }

    /**
     * Create a new tracking record for an order
     */
    public function createTracking(
        Order $order, 
        string $status, 
        string $title, 
        ?string $description = null, 
        $location = null,
        array $options = []
    ): Tracking {
        DB::beginTransaction();
        
        try {
            $previousStatus = $order->status;
            
            // Create tracking record
            $tracking = Tracking::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'rider_id' => $order->rider_id,
                'status' => $status,
                'previous_status' => $previousStatus,
                'status_code' => $this->getStatusCode($status),
                'status_type' => $this->getStatusType($status),
                'title' => $title,
                'description' => $description,
                'short_description' => $options['short_description'] ?? substr($description ?? $title, 0, 100),
                'location_name' => $location->full_address ?? $location->address ?? $location->location_name ?? null,
                'address' => $location->full_address ?? null,
                'latitude' => $location->latitude ?? null,
                'longitude' => $location->longitude ?? null,
                'place_id' => $location->place_id ?? null,
                'tracked_at' => now(),
                'expected_at' => $options['expected_at'] ?? null,
                'duration_minutes' => $this->calculateDuration($order, $status),
                'is_automatic' => $options['is_automatic'] ?? false,
                'is_critical' => $this->isCriticalStatus($status),
                'requires_action' => $this->requiresAction($status),
                'action_url' => $options['action_url'] ?? null,
                'metadata' => $options['metadata'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            // Update order status
            $order->update([
                'status' => $status,
                $this->getStatusTimestampField($status) => now(),
            ]);
            
            // Update tracking summary
            $this->updateTrackingSummary($order, $status);
            
            // Send notifications based on priority
            $this->sendStatusNotifications($order, $tracking, $options);
            
            // Check for anomalies and create alerts if needed
            $this->checkForAnomalies($order, $tracking);
            
            // Broadcast real-time event
            event(new OrderStatusUpdated($order, $tracking));
            
            DB::commit();
            
            // Cache the latest tracking for quick access
            $this->cacheLatestTracking($order, $tracking);
            
            return $tracking;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create tracking: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'status' => $status
            ]);
            throw $e;
        }
    }

    /**
     * Update rider location in real-time
     */
    public function updateRiderLocation(
        Order $order, 
        float $latitude, 
        float $longitude, 
        array $metadata = []
    ): ?Tracking {
        DB::beginTransaction();
        
        try {
            // Calculate distance from last location
            $lastTracking = Tracking::where('order_id', $order->id)
                ->whereNotNull('latitude')
                ->latest()
                ->first();
            
            $distance = 0;
            if ($lastTracking && $lastTracking->latitude && $lastTracking->longitude) {
                $distance = $this->mapService->calculateDistance(
                    $lastTracking->latitude, $lastTracking->longitude,
                    $latitude, $longitude
                );
            }
            
            // Create location update tracking
            $tracking = Tracking::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'rider_id' => $order->rider_id,
                'status' => $order->status,
                'status_type' => 'transport',
                'title' => 'Rider Location Updated',
                'short_description' => "Rider is {$distance}km away",
                'latitude' => $latitude,
                'longitude' => $longitude,
                'speed' => $metadata['speed'] ?? null,
                'bearing' => $metadata['bearing'] ?? null,
                'accuracy' => $metadata['accuracy'] ?? null,
                'tracked_at' => now(),
                'duration_minutes' => $distance > 0 ? ($distance / 30 * 60) : null,
                'remaining_minutes' => $this->calculateRemainingTime($order, $latitude, $longitude),
                'is_automatic' => true,
                'metadata' => $metadata,
            ]);
            
            // Broadcast rider location
            event(new RiderLocationUpdated($order, $tracking));
            
            // Check if rider is near destination
            $this->checkProximityAlerts($order, $latitude, $longitude);
            
            DB::commit();
            
            return $tracking;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update rider location: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate remaining time based on current location
     */
    protected function calculateRemainingTime(Order $order, float $latitude, float $longitude): ?int
    {
        $destination = null;
        
        if ($order->status === 'rider_en_route_pickup') {
            $destination = $order->pickupLocation;
        } elseif (in_array($order->status, ['out_for_delivery', 'rider_en_route_delivery'])) {
            $destination = $order->deliveryLocation;
        }
        
        if ($destination && $destination->latitude && $destination->longitude) {
            $distance = $this->mapService->calculateDistance(
                $latitude, $longitude,
                $destination->latitude, $destination->longitude
            );
            
            // Assume average speed of 30 km/h
            $minutes = ($distance / 30) * 60;
            return (int) ceil($minutes);
        }
        
        return null;
    }

    /**
     * Get estimated time of arrival
     */
    public function getETA(Order $order): ?array
    {
        $lastLocation = Tracking::where('order_id', $order->id)
            ->whereNotNull('latitude')
            ->latest()
            ->first();
        
        if (!$lastLocation) {
            return null;
        }
        
        $remainingMinutes = $this->calculateRemainingTime(
            $order, 
            $lastLocation->latitude, 
            $lastLocation->longitude
        );
        
        if ($remainingMinutes) {
            return [
                'remaining_minutes' => $remainingMinutes,
                'estimated_arrival' => now()->addMinutes($remainingMinutes),
                'last_location' => [
                    'lat' => $lastLocation->latitude,
                    'lng' => $lastLocation->longitude,
                    'updated_at' => $lastLocation->tracked_at,
                ],
            ];
        }
        
        return null;
    }

    /**
     * Get order timeline
     */
    public function getOrderTimeline(Order $order): array
    {
        $trackings = Tracking::where('order_id', $order->id)
            ->orderBy('tracked_at')
            ->get()
            ->map(function ($tracking) {
                return [
                    'id' => $tracking->id,
                    'status' => $tracking->status,
                    'title' => $tracking->title,
                    'description' => $tracking->description,
                    'location' => $tracking->location_name,
                    'coordinates' => $tracking->latitude ? [
                        'lat' => $tracking->latitude,
                        'lng' => $tracking->longitude,
                    ] : null,
                    'time' => $tracking->tracked_at->toIso8601String(),
                    'formatted_time' => $tracking->tracked_at->diffForHumans(),
                    'is_critical' => $tracking->is_critical,
                    'requires_action' => $tracking->requires_action,
                ];
            });
        
        // Calculate time gaps between statuses
        $timeline = [];
        $previous = null;
        
        foreach ($trackings as $tracking) {
            $item = $tracking;
            if ($previous) {
                $gap = $tracking['time'] - $previous['time'];
                $item['time_since_previous'] = $gap->format('%H:%I:%S');
                $item['minutes_since_previous'] = $gap->i;
            }
            $timeline[] = $item;
            $previous = $tracking;
        }
        
        return $timeline;
    }

    /**
     * Check for anomalies and create alerts
     */
    protected function checkForAnomalies(Order $order, Tracking $tracking): void
    {
        // Check if status is taking too long
        $expectedDuration = $this->getExpectedDuration($tracking->status);
        
        if ($expectedDuration && $tracking->duration_minutes > $expectedDuration) {
            $this->createAlert(
                $tracking,
                'delay',
                'high',
                "Status '{$tracking->status}' is taking longer than expected",
                ['expected_minutes' => $expectedDuration, 'actual_minutes' => $tracking->duration_minutes]
            );
        }
        
        // Check for route deviation (if we have coordinates)
        if ($tracking->latitude && $tracking->longitude && $order->rider_id) {
            $this->checkRouteDeviation($order, $tracking);
        }
    }

    /**
     * Check for route deviation
     */
    protected function checkRouteDeviation(Order $order, Tracking $tracking): void
    {
        $optimalRoute = Cache::get("optimal_route_{$order->id}");
        
        if ($optimalRoute && isset($optimalRoute['polyline'])) {
            // Calculate deviation from optimal route
            $deviation = $this->calculateRouteDeviation(
                $tracking->latitude, 
                $tracking->longitude,
                $optimalRoute['polyline']
            );
            
            if ($deviation > 500) { // More than 500 meters deviation
                $this->createAlert(
                    $tracking,
                    'route_deviation',
                    'medium',
                    "Rider deviated from optimal route by {$deviation}m",
                    ['deviation_meters' => $deviation]
                );
            }
        }
    }

    /**
     * Check proximity alerts for geofencing
     */
    protected function checkProximityAlerts(Order $order, float $latitude, float $longitude): void
    {
        // Check proximity to pickup location
        if ($order->pickupLocation && $order->pickupLocation->latitude) {
            $distanceToPickup = $this->mapService->calculateDistance(
                $latitude, $longitude,
                $order->pickupLocation->latitude,
                $order->pickupLocation->longitude
            );
            
            if ($distanceToPickup <= 0.1 && $order->status === 'rider_en_route_pickup') { // 100 meters
                $this->createAlert(
                    null,
                    'geofence_entry',
                    'low',
                    'Rider is approaching pickup location',
                    ['distance_km' => $distanceToPickup],
                    $order
                );
            }
        }
        
        // Check proximity to delivery location
        if ($order->deliveryLocation && $order->deliveryLocation->latitude) {
            $distanceToDelivery = $this->mapService->calculateDistance(
                $latitude, $longitude,
                $order->deliveryLocation->latitude,
                $order->deliveryLocation->longitude
            );
            
            if ($distanceToDelivery <= 0.1 && $order->status === 'out_for_delivery') { // 100 meters
                $this->createAlert(
                    null,
                    'geofence_entry',
                    'low',
                    'Rider is approaching delivery location',
                    ['distance_km' => $distanceToDelivery],
                    $order
                );
            }
        }
    }

    /**
     * Create an alert for tracking
     */
    protected function createAlert(
        ?Tracking $tracking, 
        string $type, 
        string $severity, 
        string $message, 
        array $data = [],
        ?Order $order = null
    ): void {
        $alertData = [
            'alert_type' => $type,
            'severity' => $severity,
            'message' => $message,
            'data' => $data,
        ];
        
        if ($tracking) {
            $alertData['tracking_id'] = $tracking->id;
            $alertData['order_id'] = $tracking->order_id;
        } elseif ($order) {
            $alertData['order_id'] = $order->id;
        }
        
        TrackingAlert::create($alertData);
        
        // Send notification for critical alerts
        if ($severity === 'high' || $severity === 'critical') {
            $this->notificationService->sendAdminAlert(
                'Tracking Alert',
                $message,
                $alertData
            );
        }
    }

    /**
     * Update tracking summary for analytics
     */
    protected function updateTrackingSummary(Order $order, string $status): void
    {
        $summary = TrackingSummary::firstOrCreate(['order_id' => $order->id]);
        
        switch ($status) {
            case 'pending':
                $summary->order_placed_at = now();
                break;
            case 'confirmed':
                $summary->confirmed_at = now();
                break;
            case 'rider_assigned':
                $summary->pickup_assigned_at = now();
                break;
            case 'items_collected':
                $summary->picked_up_at = now();
                break;
            case 'arrived_at_laundry':
                $summary->at_laundry_at = now();
                break;
            case 'processing_started':
                $summary->processing_started_at = now();
                break;
            case 'processing_completed':
                $summary->processing_completed_at = now();
                $summary->calculateDurations();
                break;
            case 'ready_for_delivery':
                $summary->ready_for_delivery_at = now();
                break;
            case 'out_for_delivery':
                $summary->out_for_delivery_at = now();
                break;
            case 'delivered':
                $summary->delivered_at = now();
                $summary->calculateDurations();
                break;
            case 'completed':
                $summary->completed_at = now();
                $summary->calculateDurations();
                break;
        }
        
        $summary->updateStatusTiming($status);
        $summary->save();
    }

    /**
     * Send notifications for status update
     */
    protected function sendStatusNotifications(Order $order, Tracking $tracking, array $options): void
    {
        $channels = ['database'];
        
        // Determine which channels to use based on priority and user preferences
        if ($tracking->is_critical) {
            $channels = ['database', 'mail', 'sms', 'push'];
        } elseif ($tracking->requires_action) {
            $channels = ['database', 'push', 'mail'];
        } elseif ($options['send_notification'] ?? true) {
            $channels = ['database', 'push'];
        }
        
        // Send to customer
        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order, $tracking, $channels));
        }
        
        // Send to rider if applicable
        if ($order->rider && in_array($tracking->status_type, ['pickup', 'delivery'])) {
            $order->rider->notify(new OrderStatusNotification($order, $tracking, ['database', 'push']));
        }
        
        // Log notification
        $tracking->update([
            'is_notification_sent' => true,
            'notification_sent_at' => now(),
            'notification_channel' => implode(',', $channels),
        ]);
    }

    /**
     * Get status code for API responses
     */
    protected function getStatusCode(string $status): string
    {
        $codes = [
            'pending' => 'ORD_PENDING',
            'confirmed' => 'ORD_CONFIRMED',
            'processing' => 'ORD_PROCESSING',
            'rider_assigned' => 'RIDER_ASSIGNED',
            'rider_en_route_pickup' => 'RIDER_EN_ROUTE_PICKUP',
            'items_collected' => 'ITEMS_COLLECTED',
            'at_laundry' => 'AT_LAUNDRY',
            'washing' => 'WASHING',
            'quality_check' => 'QUALITY_CHECK',
            'ready_for_delivery' => 'READY_FOR_DELIVERY',
            'out_for_delivery' => 'OUT_FOR_DELIVERY',
            'delivered' => 'DELIVERED',
            'completed' => 'COMPLETED',
            'cancelled' => 'CANCELLED',
        ];
        
        return $codes[$status] ?? strtoupper($status);
    }

    /**
     * Get status type for categorization
     */
    protected function getStatusType(string $status): string
    {
        $pickupStatuses = ['rider_assigned', 'rider_en_route_pickup', 'at_pickup_location', 'items_collected'];
        $transportStatuses = ['in_transit_to_laundry', 'arrived_at_laundry'];
        $processingStatuses = ['at_laundry', 'washing', 'drying', 'ironing', 'folding', 'quality_check'];
        $deliveryStatuses = ['ready_for_delivery', 'out_for_delivery', 'delivered'];
        $orderStatuses = ['pending', 'confirmed', 'processing', 'completed', 'cancelled'];
        
        if (in_array($status, $pickupStatuses)) return 'pickup';
        if (in_array($status, $transportStatuses)) return 'transport';
        if (in_array($status, $processingStatuses)) return 'processing';
        if (in_array($status, $deliveryStatuses)) return 'delivery';
        if (in_array($status, $orderStatuses)) return 'order';
        
        return 'system';
    }

    /**
     * Get timestamp field name for status
     */
    protected function getStatusTimestampField(string $status): string
    {
        $fields = [
            'pending' => 'created_at',
            'confirmed' => 'order_confirmed_at',
            'rider_assigned' => 'rider_assigned_at',
            'items_collected' => 'picked_up_at',
            'arrived_at_laundry' => 'arrived_at_laundry_at',
            'processing_started' => 'processing_started_at',
            'processing_completed' => 'processing_completed_at',
            'ready_for_delivery' => 'ready_for_delivery_at',
            'out_for_delivery' => 'out_for_delivery_at',
            'delivered' => 'delivered_at',
            'completed' => 'completed_at',
            'cancelled' => 'cancelled_at',
        ];
        
        return $fields[$status] ?? 'updated_at';
    }

    /**
     * Check if status is critical
     */
    protected function isCriticalStatus(string $status): bool
    {
        $critical = ['delivered', 'completed', 'cancelled', 'refunded', 'disputed'];
        return in_array($status, $critical);
    }

    /**
     * Check if status requires user action
     */
    protected function requiresAction(string $status): bool
    {
        $actionRequired = ['payment_pending', 'quality_check_failed', 'disputed'];
        return in_array($status, $actionRequired);
    }

    /**
     * Calculate duration since previous status
     */
    protected function calculateDuration(Order $order, string $currentStatus): ?int
    {
        $previousTracking = Tracking::where('order_id', $order->id)
            ->where('status', '!=', $currentStatus)
            ->latest()
            ->first();
        
        if ($previousTracking) {
            return now()->diffInMinutes($previousTracking->tracked_at);
        }
        
        return null;
    }

    /**
     * Get expected duration for a status (in minutes)
     */
    protected function getExpectedDuration(string $status): ?int
    {
        $durations = [
            'pending' => 5,
            'confirmation' => 10,
            'rider_assigned' => 5,
            'rider_en_route_pickup' => 30,
            'items_collected' => 60,
            'washing' => 45,
            'drying' => 30,
            'quality_check' => 15,
            'out_for_delivery' => 60,
        ];
        
        return $durations[$status] ?? null;
    }

    /**
     * Calculate route deviation in meters
     */
    protected function calculateRouteDeviation(float $lat, float $lng, array $polyline): float
    {
        // Simplified - implement actual calculation
        $minDistance = PHP_FLOAT_MAX;
        
        foreach ($polyline as $point) {
            $distance = $this->mapService->calculateDistance(
                $lat, $lng,
                $point[0], $point[1]
            );
            $minDistance = min($minDistance, $distance);
        }
        
        return $minDistance * 1000; // Convert to meters
    }

    /**
     * Cache latest tracking for quick access
     */
    protected function cacheLatestTracking(Order $order, Tracking $tracking): void
    {
        Cache::put("order_{$order->id}_latest_tracking", $tracking, now()->addHours(1));
    }

    /**
     * Get active issues for an order
     */
    public function getActiveIssues(Order $order): array
    {
        return TrackingAlert::where('order_id', $order->id)
            ->where('is_resolved', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(int $alertId, string $resolutionNote, ?int $resolvedBy = null): bool
    {
        $alert = TrackingAlert::find($alertId);
        
        if ($alert) {
            $alert->resolve($resolutionNote, $resolvedBy);
            return true;
        }
        
        return false;
    }

    /**
     * Get order progress percentage
     */
    public function getOrderProgress(Order $order): int
    {
        $progressMap = [
            'pending' => 0,
            'confirmed' => 10,
            'processing' => 15,
            'rider_assigned' => 20,
            'rider_en_route_pickup' => 25,
            'items_collected' => 35,
            'arrived_at_laundry' => 45,
            'washing' => 55,
            'drying' => 60,
            'quality_check' => 70,
            'ready_for_delivery' => 80,
            'out_for_delivery' => 85,
            'delivered' => 95,
            'completed' => 100,
        ];
        
        return $progressMap[$order->status] ?? 0;
    }
}