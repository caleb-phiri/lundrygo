<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class RiderLocation extends Model
{
    use SpatialTrait;

    protected $table = 'rider_locations';

    protected $fillable = [
        'rider_id',
        'latitude',
        'longitude',
        'coordinates',
        'previous_coordinates',
        'place_id',
        'location_name',
        'speed',
        'bearing',
        'accuracy',
        'altitude',
        'vertical_accuracy',
        'distance_traveled_today',
        'distance_traveled_shift',
        'total_distance_traveled',
        'is_online',
        'is_available',
        'is_on_delivery',
        'is_on_break',
        'is_active',
        'is_emergency',
        'ride_status',
        'current_order_id',
        'current_laundry_id',
        'battery_level',
        'is_charging',
        'device_id',
        'device_model',
        'os_version',
        'app_version',
        'network_type',
        'ip_address',
        'is_in_service_area',
        'is_at_laundry',
        'is_at_customer',
        'current_zone',
        'response_time_seconds',
        'idle_duration_minutes',
        'last_assigned_at',
        'last_completed_at',
        'last_updated_at',
        'last_active_at',
        'shift_started_at',
        'break_started_at',
        'location_snapshot',
        'metadata',
        'last_heartbeat_at',
        'missed_heartbeats',
        'connection_quality',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'speed' => 'decimal:2',
        'bearing' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'altitude' => 'decimal:2',
        'vertical_accuracy' => 'decimal:2',
        'distance_traveled_today' => 'decimal:2',
        'distance_traveled_shift' => 'decimal:2',
        'total_distance_traveled' => 'decimal:2',
        'is_online' => 'boolean',
        'is_available' => 'boolean',
        'is_on_delivery' => 'boolean',
        'is_on_break' => 'boolean',
        'is_active' => 'boolean',
        'is_emergency' => 'boolean',
        'battery_level' => 'integer',
        'is_charging' => 'boolean',
        'response_time_seconds' => 'decimal:2',
        'idle_duration_minutes' => 'decimal:2',
        'last_assigned_at' => 'datetime',
        'last_completed_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'last_active_at' => 'datetime',
        'shift_started_at' => 'datetime',
        'break_started_at' => 'datetime',
        'location_snapshot' => 'array',
        'metadata' => 'array',
        'last_heartbeat_at' => 'datetime',
        'missed_heartbeats' => 'integer',
        'connection_quality' => 'boolean',
    ];

    protected $spatialFields = [
        'coordinates',
        'previous_coordinates',
    ];

    protected $appends = ['status', 'eta_minutes', 'is_idle'];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function currentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_emergency) {
            return 'Emergency';
        }
        
        if ($this->is_on_break) {
            return 'On Break';
        }
        
        if ($this->is_on_delivery) {
            return 'On Delivery';
        }
        
        if ($this->is_online && $this->is_available) {
            return 'Available';
        }
        
        if ($this->is_online && !$this->is_available) {
            return 'Busy';
        }
        
        return 'Offline';
    }

    public function getIsIdleAttribute(): bool
    {
        return $this->ride_status === 'idle' && 
               !$this->is_on_delivery && 
               !$this->is_on_break;
    }

    public function getEtaMinutesAttribute(): ?int
    {
        if (!$this->current_order_id || !$this->coordinates) {
            return null;
        }
        
        // Calculate ETA based on current location and destination
        $order = $this->currentOrder;
        if (!$order) {
            return null;
        }
        
        $destination = $order->pickup_location_id ? 
            $order->pickupLocation : 
            $order->deliveryLocation;
            
        if ($destination && $destination->coordinates) {
            $distance = $this->coordinates->distance($destination->coordinates);
            $speed = max($this->speed ?? 20, 20); // Assume 20km/h if no speed data
            $etaHours = $distance / $speed;
            return (int) ceil($etaHours * 60);
        }
        
        return null;
    }

    public function updateLocation(float $latitude, float $longitude, array $data = []): void
    {
        // Store previous location
        if ($this->latitude && $this->longitude) {
            $this->previous_coordinates = new Point($this->latitude, $this->longitude);
        }
        
        // Update current location
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->coordinates = new Point($latitude, $longitude);
        
        // Calculate distance traveled
        if ($this->previous_coordinates) {
            $distance = $this->previous_coordinates->distance($this->coordinates);
            $this->distance_traveled_today += $distance;
            $this->distance_traveled_shift += $distance;
            $this->total_distance_traveled += $distance;
        }
        
        // Update other fields
        foreach ($data as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->$key = $value;
            }
        }
        
        $this->last_updated_at = now();
        $this->location_snapshot = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'speed' => $this->speed,
            'bearing' => $this->bearing,
            'accuracy' => $this->accuracy,
            'timestamp' => now()->toISOString(),
        ];
        
        $this->save();
        
        // Save to history
        $this->saveToHistory();
    }

    protected function saveToHistory(): void
    {
        RiderLocationHistory::create([
            'rider_id' => $this->rider_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'coordinates' => $this->coordinates,
            'speed' => $this->speed,
            'bearing' => $this->bearing,
            'accuracy' => $this->accuracy,
            'ride_status' => $this->ride_status,
            'order_id' => $this->current_order_id,
            'battery_level' => $this->battery_level,
            'tracked_at' => now(),
        ]);
    }

    public function goOnline(): void
    {
        $this->is_online = true;
        $this->is_available = true;
        $this->last_active_at = now();
        
        if (!$this->shift_started_at) {
            $this->shift_started_at = now();
            $this->createShift();
        }
        
        $this->save();
    }

    public function goOffline(): void
    {
        $this->is_online = false;
        $this->is_available = false;
        $this->save();
        
        $this->endCurrentShift();
    }

    public function startBreak(): void
    {
        $this->is_on_break = true;
        $this->is_available = false;
        $this->break_started_at = now();
        $this->save();
    }

    public function endBreak(): void
    {
        if ($this->break_started_at) {
            $breakMinutes = now()->diffInMinutes($this->break_started_at);
            $this->updateCurrentShiftBreak($breakMinutes);
        }
        
        $this->is_on_break = false;
        $this->is_available = true;
        $this->break_started_at = null;
        $this->save();
    }

    protected function createShift(): void
    {
        RiderShift::create([
            'rider_id' => $this->rider_id,
            'shift_start_at' => $this->shift_started_at,
            'status' => 'active',
        ]);
    }

    protected function endCurrentShift(): void
    {
        $shift = RiderShift::where('rider_id', $this->rider_id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        if ($shift) {
            $shift->shift_end_at = now();
            $shift->total_distance = $this->distance_traveled_shift;
            $shift->status = 'completed';
            $shift->save();
        }
        
        // Reset shift tracking
        $this->distance_traveled_shift = 0;
        $this->shift_started_at = null;
        $this->save();
    }

    protected function updateCurrentShiftBreak(int $breakMinutes): void
    {
        $shift = RiderShift::where('rider_id', $this->rider_id)
            ->where('status', 'active')
            ->latest()
            ->first();
            
        if ($shift) {
            $shift->total_break_minutes += $breakMinutes;
            $shift->save();
        }
    }

    public function updateRideStatus(string $status, ?int $orderId = null): void
    {
        $this->ride_status = $status;
        
        if ($orderId) {
            $this->current_order_id = $orderId;
        }
        
        if ($status === 'idle') {
            $this->is_on_delivery = false;
            $this->current_order_id = null;
            $this->last_completed_at = now();
        } elseif (in_array($status, ['going_to_pickup', 'going_to_delivery'])) {
            $this->is_on_delivery = true;
            $this->is_available = false;
            $this->last_assigned_at = now();
        }
        
        $this->save();
    }

    public function sendHeartbeat(): void
    {
        $this->last_heartbeat_at = now();
        $this->missed_heartbeats = 0;
        $this->connection_quality = true;
        $this->save();
    }

    public function missHeartbeat(): void
    {
        $this->missed_heartbeats++;
        
        if ($this->missed_heartbeats >= 3) {
            $this->connection_quality = false;
        }
        
        if ($this->missed_heartbeats >= 5) {
            $this->is_online = false;
            $this->is_available = false;
        }
        
        $this->save();
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_online', true)
            ->where('is_available', true)
            ->where('is_on_break', false)
            ->where('is_active', true);
    }

    public function scopeNearby($query, float $latitude, float $longitude, int $radius = 5, string $unit = 'km')
    {
        $meters = $unit === 'km' ? $radius * 1000 : $radius * 1609.34;
        
        return $query->whereRaw(
            "ST_Distance_Sphere(coordinates, POINT(?, ?)) <= ?",
            [$longitude, $latitude, $meters]
        );
    }

    public function scopeByRideStatus($query, string $status)
    {
        return $query->where('ride_status', $status);
    }

    public function scopeWithActiveOrder($query)
    {
        return $query->whereNotNull('current_order_id');
    }
}