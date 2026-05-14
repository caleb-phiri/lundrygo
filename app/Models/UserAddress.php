<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'user_addresses';
    
    protected $fillable = [
        'user_id',
        'label',
        'address_type',
        'place_id',
        'full_address',
        'street_address',
        'apartment_suite',
        'floor',
        'intercom',
        'city',
        'state',
        'postal_code',
        'country',
        'county',
        'neighborhood',
        'latitude',
        'longitude',
        'delivery_instructions',
        'access_instructions',
        'preferred_time_start',
        'preferred_time_end',
        'preferred_day',
        'contact_person_name',
        'contact_phone',
        'alternate_phone',
        'email',
        'is_default',
        'is_verified',
        'verified_at',
        'is_active',
        'is_residential',
        'is_commercial',
        'has_elevator',
        'requires_approval',
        'security_code',
        'landmarks',
        'delivery_count',
        'pickup_count',
        'last_used_at',
        'rider_rating',
        'rider_notes',
        'is_in_service_area',
        'service_zone',
        'delivery_restrictions',
        'blackout_dates',
        'metadata',
        'custom_fields',
    ];
    
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'is_residential' => 'boolean',
        'is_commercial' => 'boolean',
        'has_elevator' => 'boolean',
        'requires_approval' => 'boolean',
        'is_in_service_area' => 'boolean',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
        'rider_rating' => 'decimal:1',
        'delivery_count' => 'integer',
        'pickup_count' => 'integer',
        'delivery_restrictions' => 'array',
        'blackout_dates' => 'array',
        'metadata' => 'array',
        'custom_fields' => 'array',
    ];
    
    protected $appends = [
        'full_address_with_label', 
        'delivery_window', 
        'formatted_coordinates',
        'has_coordinates'
    ];
    
    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function pickupOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_location_id');
    }
    
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'delivery_location_id');
    }
    
    public function deliveryWindows(): HasMany
    {
        return $this->hasMany(AddressDeliveryWindow::class);
    }
    
    public function validationLogs(): HasMany
    {
        return $this->hasMany(AddressValidationLog::class);
    }
    
    /**
     * Accessors & Mutators
     */
    public function getFullAddressWithLabelAttribute(): string
    {
        return "{$this->label}: {$this->full_address}";
    }
    
    public function getDeliveryWindowAttribute(): ?string
    {
        if ($this->preferred_time_start && $this->preferred_time_end) {
            return "{$this->preferred_time_start} - {$this->preferred_time_end}";
        }
        return null;
    }
    
    public function getFormattedCoordinatesAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return "{$this->latitude}, {$this->longitude}";
        }
        return null;
    }
    
    /**
     * Get coordinates as array
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float)$this->latitude,
                'lng' => (float)$this->longitude,
            ];
        }
        return null;
    }
    
    /**
     * Check if address has valid coordinates
     */
    public function getHasCoordinatesAttribute(): bool
    {
        return $this->hasCoordinates();
    }
    
    public function hasCoordinates(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude) 
               && $this->latitude != 0 && $this->longitude != 0;
    }
    
    /**
     * Set latitude with validation
     */
    public function setLatitudeAttribute($value)
    {
        if ($value !== null && is_numeric($value)) {
            $lat = (float)$value;
            // Validate latitude range (-90 to 90)
            if ($lat >= -90 && $lat <= 90) {
                $this->attributes['latitude'] = $lat;
            } else {
                $this->attributes['latitude'] = null;
            }
        } else {
            $this->attributes['latitude'] = null;
        }
    }
    
    /**
     * Set longitude with validation
     */
    public function setLongitudeAttribute($value)
    {
        if ($value !== null && is_numeric($value)) {
            $lng = (float)$value;
            // Validate longitude range (-180 to 180)
            if ($lng >= -180 && $lng <= 180) {
                $this->attributes['longitude'] = $lng;
            } else {
                $this->attributes['longitude'] = null;
            }
        } else {
            $this->attributes['longitude'] = null;
        }
    }
    
    /**
     * Business Logic Methods
     */
    public function setAsDefault(): void
    {
        // Remove default from all other addresses of this user
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // Set this as default
        $this->is_default = true;
        $this->save();
    }
    
    public function incrementDeliveryCount(): void
    {
        $this->increment('delivery_count');
        $this->last_used_at = now();
        $this->save();
    }
    
    public function incrementPickupCount(): void
    {
        $this->increment('pickup_count');
        $this->last_used_at = now();
        $this->save();
    }
    
    public function markAsVerified(?string $validator = null, array $validationData = null): void
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->save();
        
        if ($validator && $validationData) {
            $this->validationLogs()->create([
                'validator' => $validator,
                'original_address' => $this->full_address,
                'validated_address' => $validationData['validated_address'] ?? $this->full_address,
                'validation_response' => $validationData,
                'confidence_score' => $validationData['confidence_score'] ?? null,
                'is_valid' => true,
            ]);
        }
    }
    
    /**
     * Calculate distance to another coordinate (Haversine formula)
     */
    public function getDistanceTo(float $latitude, float $longitude, string $unit = 'km'): ?float
    {
        if (!$this->hasCoordinates()) {
            return null;
        }

        $earthRadius = $unit === 'km' ? 6371 : 3959;
        $latDelta = deg2rad($latitude - (float)$this->latitude);
        $lonDelta = deg2rad($longitude - (float)$this->longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad((float)$this->latitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
    
    /**
     * Calculate distance to another address
     */
    public function getDistanceToAddress(self $address, string $unit = 'km'): ?float
    {
        if (!$this->hasCoordinates() || !$address->hasCoordinates()) {
            return null;
        }
        
        return $this->getDistanceTo(
            (float)$address->latitude,
            (float)$address->longitude,
            $unit
        );
    }
    
    public function isDeliverableNow(): bool
    {
        // Check if address has coordinates
        if (!$this->hasCoordinates()) {
            return false;
        }
        
        // Check if there are any blackout dates
        if ($this->blackout_dates) {
            $today = now()->format('Y-m-d');
            if (in_array($today, $this->blackout_dates)) {
                return false;
            }
        }

        // Check delivery restrictions
        if ($this->delivery_restrictions) {
            $currentTime = now()->format('H:i');
            $currentDay = strtolower(now()->format('l'));
            
            if (isset($this->delivery_restrictions[$currentDay])) {
                $restrictions = $this->delivery_restrictions[$currentDay];
                if ($currentTime < $restrictions['start'] || $currentTime > $restrictions['end']) {
                    return false;
                }
            }
        }

        return $this->is_active && $this->is_in_service_area;
    }
    
    /**
     * Get default coordinates for fallback
     */
    public static function getDefaultCoordinates(): array
    {
        return [
            'latitude' => config('app.default_latitude', -15.3875),
            'longitude' => config('app.default_longitude', 28.3228),
        ];
    }
    
    /**
     * Scopes
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
    
    public function scopeWithCoordinates($query)
    {
        return $query->whereNotNull('latitude')
                     ->whereNotNull('longitude')
                     ->where('latitude', '!=', 0)
                     ->where('longitude', '!=', 0);
    }
    
    public function scopeWithoutCoordinates($query)
    {
        return $query->where(function($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude')
              ->orWhere('latitude', 0)
              ->orWhere('longitude', 0);
        });
    }
    
    public function scopeByType($query, string $type)
    {
        return $query->where('address_type', $type);
    }
    
    public function scopeInCity($query, string $city)
    {
        return $query->where('city', 'LIKE', "%{$city}%");
    }
    
    public function scopeInPostalCode($query, string $postalCode)
    {
        return $query->where('postal_code', $postalCode);
    }
    
    public function scopeInServiceArea($query)
    {
        return $query->where('is_in_service_area', true);
    }
    
    /**
     * Find nearby addresses using Haversine formula
     */
    public function scopeNearby($query, float $latitude, float $longitude, int $radius = 5, string $unit = 'km')
    {
        $earthRadius = $unit === 'km' ? 6371 : 3959;
        
        return $query->selectRaw(
            "*, (
                {$earthRadius} * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )
            ) AS distance",
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radius)
         ->orderBy('distance');
    }
    
    public function scopeMostUsed($query, $limit = 10)
    {
        return $query->orderBy('delivery_count', 'desc')
            ->orderBy('pickup_count', 'desc')
            ->limit($limit);
    }
    
    public function scopeRecentlyUsed($query)
    {
        return $query->whereNotNull('last_used_at')->orderBy('last_used_at', 'desc');
    }
}