<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Grimzy\LaravelMysqlSpatial\Eloquent\SpatialTrait;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class Location extends Model
{
    use SoftDeletes, SpatialTrait;

    protected $fillable = [
        'locationable_id',
        'locationable_type',
        'type',
        'full_address',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'coordinates',
        'landmark',
        'delivery_instructions',
        'contact_person',
        'contact_phone',
        'is_default',
        'is_verified',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected $spatialFields = [
        'coordinates',
    ];

    /**
     * Get the parent locationable model.
     */
    public function locationable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Set coordinates from latitude and longitude.
     */
    public function setCoordinatesFromLatLng(): void
    {
        if ($this->latitude && $this->longitude) {
            $this->coordinates = new Point($this->latitude, $this->longitude);
        }
    }

    /**
     * Get distance from another location in kilometers.
     */
    public function distanceFrom(float $latitude, float $longitude, string $unit = 'km'): ?float
    {
        if (!$this->coordinates) {
            return null;
        }

        $distance = $this->coordinates->distance(new Point($latitude, $longitude));
        
        if ($unit === 'miles') {
            $distance *= 0.621371;
        }

        return round($distance, 2);
    }

    /**
     * Scope a query to find nearby locations.
     */
    public function scopeNearby($query, float $latitude, float $longitude, int $radius = 10, string $unit = 'km')
    {
        $meters = $unit === 'km' ? $radius * 1000 : $radius * 1609.34;
        
        return $query->whereRaw(
            "ST_Distance_Sphere(coordinates, POINT(?, ?)) <= ?",
            [$longitude, $latitude, $meters]
        );
    }
}