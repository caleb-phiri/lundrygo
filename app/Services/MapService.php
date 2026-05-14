<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class MapService
{
    private ?string $apiKey;
    private string $baseUrl;
    private string $provider;
    private bool $useFallback;

    public function __construct()
    {
        $this->apiKey = Config::get('services.google.maps_api_key');
        $this->baseUrl = 'https://maps.googleapis.com/maps/api';
        $this->provider = Config::get('services.map.provider', 'google');
        $this->useFallback = Config::get('services.map.use_fallback', true);
    }

    /**
     * Geocode an address to coordinates
     */
    public function geocodeAddress(string $address): ?array
    {
        $cacheKey = 'geocode_' . md5($address);

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($address) {
            // Try primary provider first
            $result = $this->geocodeWithProvider($address);
            
            // Fallback to OpenStreetMap if needed
            if (!$result && $this->useFallback) {
                $result = $this->nominatimGeocode($address);
            }
            
            return $result;
        });
    }

    /**
     * Geocode using the configured provider
     */
    private function geocodeWithProvider(string $address): ?array
    {
        switch ($this->provider) {
            case 'google':
                return $this->googleGeocode($address);
            case 'mapbox':
                return $this->mapboxGeocode($address);
            default:
                return null;
        }
    }

    /**
     * Google Maps Geocoding
     */
    private function googleGeocode(string $address): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Google Maps API key not configured');
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->retry(3, 100)
                ->get("{$this->baseUrl}/geocode/json", [
                    'address' => $address,
                    'key' => $this->apiKey,
                ]);

            if ($response->successful() && $response['status'] === 'OK') {
                $result = $response['results'][0];
                $location = $result['geometry']['location'];
                $components = collect($result['address_components']);

                return [
                    'success' => true,
                    'provider' => 'google',
                    'formatted_address' => $result['formatted_address'],
                    'latitude' => (float) $location['lat'],
                    'longitude' => (float) $location['lng'],
                    'city' => $this->getAddressComponent($components, 'locality'),
                    'state' => $this->getAddressComponent($components, 'administrative_area_level_1'),
                    'postal_code' => $this->getAddressComponent($components, 'postal_code'),
                    'country' => $this->getAddressComponent($components, 'country'),
                    'place_id' => $result['place_id'],
                    'full_data' => $result,
                ];
            }
            
            if ($response['status'] === 'REQUEST_DENIED') {
                Log::error('Google Maps API key invalid or restricted');
            }
            
        } catch (\Exception $e) {
            Log::error('Google geocoding failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Mapbox Geocoding
     */
    private function mapboxGeocode(string $address): ?array
    {
        $mapboxKey = Config::get('services.mapbox.access_token');
        
        if (!$mapboxKey) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->get("https://api.mapbox.com/geocoding/v5/mapbox.places/{$address}.json", [
                    'access_token' => $mapboxKey,
                    'limit' => 1,
                ]);

            if ($response->successful() && isset($response['features'][0])) {
                $feature = $response['features'][0];
                $coordinates = $feature['geometry']['coordinates'];

                return [
                    'success' => true,
                    'provider' => 'mapbox',
                    'formatted_address' => $feature['place_name'],
                    'latitude' => (float) $coordinates[1],
                    'longitude' => (float) $coordinates[0],
                    'place_id' => $feature['id'],
                    'full_data' => $feature,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Mapbox geocoding failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Reverse geocode coordinates to address
     */
    public function reverseGeocode($latitude, $longitude): ?array
    {
        // Convert to float and handle null values
        $latitude = is_numeric($latitude) ? (float)$latitude : 0;
        $longitude = is_numeric($longitude) ? (float)$longitude : 0;
        
        if ($latitude == 0 || $longitude == 0) {
            return null;
        }
        
        $cacheKey = "reverse_geocode_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, now()->addDays(30), function () use ($latitude, $longitude) {
            // Try Google first
            if ($this->apiKey) {
                $result = $this->googleReverseGeocode($latitude, $longitude);
                if ($result) {
                    return $result;
                }
            }
            
            // Fallback to OpenStreetMap
            if ($this->useFallback) {
                return $this->nominatimReverseGeocode($latitude, $longitude);
            }
            
            return null;
        });
    }

    /**
     * Google Reverse Geocoding
     */
    private function googleReverseGeocode(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/geocode/json", [
                    'latlng' => "{$latitude},{$longitude}",
                    'key' => $this->apiKey,
                ]);

            if ($response->successful() && $response['status'] === 'OK' && count($response['results']) > 0) {
                $result = $response['results'][0];
                $components = collect($result['address_components']);

                return [
                    'success' => true,
                    'provider' => 'google',
                    'formatted_address' => $result['formatted_address'],
                    'city' => $this->getAddressComponent($components, 'locality'),
                    'state' => $this->getAddressComponent($components, 'administrative_area_level_1'),
                    'postal_code' => $this->getAddressComponent($components, 'postal_code'),
                    'country' => $this->getAddressComponent($components, 'country'),
                    'place_id' => $result['place_id'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Google reverse geocoding failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Calculate distance between two points (Haversine formula)
     * UPDATED: Handles null values gracefully
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2, string $unit = 'km'): float
    {
        // Convert to float and handle null/empty values
        $lat1 = $this->sanitizeCoordinate($lat1);
        $lon1 = $this->sanitizeCoordinate($lon1);
        $lat2 = $this->sanitizeCoordinate($lat2);
        $lon2 = $this->sanitizeCoordinate($lon2);
        
        $earthRadius = $unit === 'km' ? 6371 : 3959;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
    
    /**
     * Sanitize coordinate values
     */
    private function sanitizeCoordinate($coordinate): float
    {
        if (is_null($coordinate) || $coordinate === '') {
            return 0;
        }
        
        $value = is_numeric($coordinate) ? (float)$coordinate : 0;
        
        // Validate latitude range (-90 to 90)
        if ($value < -90 || $value > 90) {
            return 0;
        }
        
        // Validate longitude range (-180 to 180)
        if ($value < -180 || $value > 180) {
            return 0;
        }
        
        return $value;
    }

    /**
     * Get distance matrix between multiple origins and destinations
     */
    public function getDistanceMatrix(array $origins, array $destinations): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->get("{$this->baseUrl}/distancematrix/json", [
                    'origins' => implode('|', $origins),
                    'destinations' => implode('|', $destinations),
                    'key' => $this->apiKey,
                    'units' => 'imperial',
                ]);

            if ($response->successful() && $response['status'] === 'OK') {
                return [
                    'success' => true,
                    'rows' => $response['rows'],
                    'origin_addresses' => $response['origin_addresses'],
                    'destination_addresses' => $response['destination_addresses'],
                ];
            }
        } catch (\Exception $e) {
            Log::error('Distance matrix API failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Calculate delivery fee based on distance
     * UPDATED: Handles null values gracefully
     */
    public function calculateDeliveryFee($pickupLat, $pickupLng, $deliveryLat, $deliveryLng, array $options = []): array
    {
        // Sanitize coordinates
        $pickupLat = $this->sanitizeCoordinate($pickupLat);
        $pickupLng = $this->sanitizeCoordinate($pickupLng);
        $deliveryLat = $this->sanitizeCoordinate($deliveryLat);
        $deliveryLng = $this->sanitizeCoordinate($deliveryLng);
        
        $distance = $this->calculateDistance($pickupLat, $pickupLng, $deliveryLat, $deliveryLng);
        
        $baseFee = $options['base_fee'] ?? Config::get('delivery.base_fee', 5.00);
        $perKmRate = $options['per_km_rate'] ?? Config::get('delivery.per_km_rate', 0.50);
        $maxFee = $options['max_fee'] ?? Config::get('delivery.max_fee', 25.00);
        $expressMultiplier = $options['express_multiplier'] ?? Config::get('delivery.express_multiplier', 1.5);
        
        $standardFee = round($baseFee + ($distance * $perKmRate), 2);
        $standardFee = min($standardFee, $maxFee);
        
        $expressFee = round($standardFee * $expressMultiplier, 2);
        $expressFee = min($expressFee, $maxFee * $expressMultiplier);
        
        // Apply minimum fees
        $minFee = $options['min_fee'] ?? Config::get('delivery.min_fee', 5.00);
        $standardFee = max($standardFee, $minFee);
        $expressFee = max($expressFee, $minFee * $expressMultiplier);

        return [
            'distance_km' => $distance,
            'distance_miles' => round($distance * 0.621371, 2),
            'standard_fee' => $standardFee,
            'express_fee' => $expressFee,
            'currency' => 'USD',
            'calculation' => [
                'base_fee' => $baseFee,
                'per_km_rate' => $perKmRate,
                'distance_charge' => round($distance * $perKmRate, 2),
            ],
        ];
    }

    /**
     * Get estimated travel time
     */
    public function getTravelTime($originLat, $originLng, $destLat, $destLng): ?array
    {
        // Sanitize coordinates
        $originLat = $this->sanitizeCoordinate($originLat);
        $originLng = $this->sanitizeCoordinate($originLng);
        $destLat = $this->sanitizeCoordinate($destLat);
        $destLng = $this->sanitizeCoordinate($destLng);
        
        if ($originLat == 0 || $originLng == 0 || $destLat == 0 || $destLng == 0) {
            return $this->estimateTravelTime($originLat, $originLng, $destLat, $destLng);
        }
        
        if (!$this->apiKey) {
            return $this->estimateTravelTime($originLat, $originLng, $destLat, $destLng);
        }

        try {
            $response = Http::timeout(10)
                ->get("{$this->baseUrl}/distancematrix/json", [
                    'origins' => "{$originLat},{$originLng}",
                    'destinations' => "{$destLat},{$destLng}",
                    'key' => $this->apiKey,
                    'departure_time' => 'now',
                ]);

            if ($response->successful() && $response['status'] === 'OK') {
                $element = $response['rows'][0]['elements'][0] ?? null;
                
                if ($element && isset($element['duration'])) {
                    return [
                        'success' => true,
                        'duration_text' => $element['duration']['text'],
                        'duration_seconds' => $element['duration']['value'],
                        'distance_text' => $element['distance']['text'] ?? null,
                        'distance_meters' => $element['distance']['value'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Travel time API failed: ' . $e->getMessage());
        }
        
        return $this->estimateTravelTime($originLat, $originLng, $destLat, $destLng);
    }

    /**
     * Estimate travel time based on distance (fallback)
     */
    private function estimateTravelTime(float $originLat, float $originLng, float $destLat, float $destLng): array
    {
        $distance = $this->calculateDistance($originLat, $originLng, $destLat, $destLng);
        $avgSpeed = 30; // km/h in city
        $hours = $distance / $avgSpeed;
        $minutes = round($hours * 60);
        
        return [
            'success' => false,
            'estimated' => true,
            'duration_seconds' => $minutes * 60,
            'duration_text' => "{$minutes} mins (estimated)",
            'distance_km' => $distance,
        ];
    }

    /**
     * Get place details by Place ID
     */
    public function getPlaceDetails(string $placeId): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        $cacheKey = 'place_details_' . $placeId;

        return Cache::remember($cacheKey, now()->addDays(7), function () use ($placeId) {
            try {
                $response = Http::timeout(10)
                    ->get("{$this->baseUrl}/place/details/json", [
                        'place_id' => $placeId,
                        'key' => $this->apiKey,
                        'fields' => 'name,formatted_address,geometry,photos,rating,reviews,opening_hours,website,formatted_phone_number',
                    ]);

                if ($response->successful() && $response['status'] === 'OK') {
                    return $response['result'];
                }
            } catch (\Exception $e) {
                Log::error('Place details failed: ' . $e->getMessage());
            }
            
            return null;
        });
    }

    /**
     * Autocomplete address
     */
    public function autocompleteAddress(string $input, array $options = []): array
    {
        if (!$this->apiKey) {
            return [];
        }

        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/place/autocomplete/json", [
                    'input' => $input,
                    'key' => $this->apiKey,
                    'types' => $options['types'] ?? 'geocode',
                    'components' => $options['components'] ?? null,
                ]);

            if ($response->successful() && $response['status'] === 'OK') {
                return $response['predictions'];
            }
        } catch (\Exception $e) {
            Log::error('Autocomplete failed: ' . $e->getMessage());
        }
        
        return [];
    }

    /**
     * Extract address component
     */
    private function getAddressComponent($components, string $type): ?string
    {
        $component = $components->firstWhere(function ($component) use ($type) {
            return in_array($type, $component['types'] ?? []);
        });
        
        return $component['short_name'] ?? $component['long_name'] ?? null;
    }

    /**
     * OpenStreetMap Nominatim Geocoding (Fallback)
     */
    private function nominatimGeocode(string $address): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'LaundryGo/1.0'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ]);

            if ($response->successful() && count($response->json()) > 0) {
                $result = $response->json()[0];
                return [
                    'success' => true,
                    'provider' => 'nominatim',
                    'formatted_address' => $result['display_name'],
                    'latitude' => (float) $result['lat'],
                    'longitude' => (float) $result['lon'],
                    'place_id' => $result['place_id'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Nominatim geocoding failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * OpenStreetMap Nominatim Reverse Geocoding (Fallback)
     */
    private function nominatimReverseGeocode(float $latitude, float $longitude): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'LaundryGo/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'json',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'zoom' => 18,
                    'addressdetails' => 1,
                ]);

            if ($response->successful() && isset($response['display_name'])) {
                return [
                    'success' => true,
                    'provider' => 'nominatim',
                    'formatted_address' => $response['display_name'],
                    'city' => $response['address']['city'] ?? $response['address']['town'] ?? null,
                    'state' => $response['address']['state'] ?? null,
                    'postal_code' => $response['address']['postcode'] ?? null,
                    'country' => $response['address']['country'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error('Nominatim reverse geocoding failed: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Batch geocode multiple addresses
     */
    public function batchGeocode(array $addresses): array
    {
        $results = [];
        foreach ($addresses as $index => $address) {
            $results[$index] = $this->geocodeAddress($address);
            
            // Rate limiting for Nominatim (1 request per second)
            if ($this->provider !== 'google') {
                usleep(1000000);
            }
        }
        
        return $results;
    }

    /**
     * Check if coordinates are within a geofence
     */
    public function isWithinGeofence($latitude, $longitude, array $geofence): bool
    {
        $latitude = $this->sanitizeCoordinate($latitude);
        $longitude = $this->sanitizeCoordinate($longitude);
        
        // Simple bounding box check
        if (isset($geofence['north'], $geofence['south'], $geofence['east'], $geofence['west'])) {
            return $latitude <= $geofence['north'] &&
                   $latitude >= $geofence['south'] &&
                   $longitude <= $geofence['east'] &&
                   $longitude >= $geofence['west'];
        }
        
        // Polygon check (ray casting algorithm)
        if (isset($geofence['polygon']) && is_array($geofence['polygon'])) {
            return $this->pointInPolygon($latitude, $longitude, $geofence['polygon']);
        }
        
        return false;
    }

    /**
     * Point in polygon check
     */
    private function pointInPolygon(float $latitude, float $longitude, array $polygon): bool
    {
        $inside = false;
        $j = count($polygon) - 1;
        
        for ($i = 0; $i < count($polygon); $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];
            
            $intersect = (($yi > $longitude) != ($yj > $longitude)) &&
                ($latitude < ($xj - $xi) * ($longitude - $yi) / ($yj - $yi) + $xi);
            
            if ($intersect) {
                $inside = !$inside;
            }
            $j = $i;
        }
        
        return $inside;
    }
    
    /**
     * Validate coordinates
     */
    public function validateCoordinates($latitude, $longitude): bool
    {
        $lat = $this->sanitizeCoordinate($latitude);
        $lng = $this->sanitizeCoordinate($longitude);
        
        return $lat != 0 && $lng != 0;
    }
    
    /**
     * Get default coordinates for fallback
     */
    public function getDefaultCoordinates(): array
    {
        return [
            'latitude' => Config::get('app.default_latitude', -15.3875),
            'longitude' => Config::get('app.default_longitude', 28.3228),
        ];
    }
}