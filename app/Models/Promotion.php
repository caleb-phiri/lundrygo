<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'short_description',
        'type',
        'value',
        'discount_on',
        'buy_quantity',
        'get_quantity',
        'get_discount_percentage',
        'buy_service_id',
        'get_service_id',
        'min_order_amount',
        'min_quantity',
        'min_items',
        'max_discount',
        'max_discount_percent',
        'max_uses_per_user',
        'max_uses_per_day',
        'total_uses',
        'total_uses_today',
        'last_used_date',
        'starts_at',
        'expires_at',
        'time_restriction',
        'available_from',
        'available_to',
        'available_monday',
        'available_tuesday',
        'available_wednesday',
        'available_thursday',
        'available_friday',
        'available_saturday',
        'available_sunday',
        'user_eligibility',
        'specific_user_ids',
        'excluded_user_ids',
        'first_order_only',
        'first_time_user',
        'applicable_service_ids',
        'applicable_category_ids',
        'excluded_service_ids',
        'excluded_category_ids',
        'applicable_cities',
        'applicable_zip_codes',
        'stackable',
        'stackable_with',
        'apply_before_tax',
        'banner_image',
        'icon',
        'button_text',
        'button_link',
        'color_theme',
        'sort_order',
        'auto_apply',
        'needs_coupon_code',
        'is_referral',
        'referrer_user_id',
        'referrer_reward',
        'referee_reward',
        'status',
        'is_featured',
        'is_public',
        'is_verified',
        'verified_at',
        'verified_by',
        'admin_notes',
        'internal_notes',
        'metadata',
        'conditions',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'max_discount_percent' => 'decimal:2',
        'referrer_reward' => 'decimal:2',
        'referee_reward' => 'decimal:2',
        'total_uses' => 'integer',
        'total_uses_today' => 'integer',
        'max_uses_per_user' => 'integer',
        'max_uses_per_day' => 'integer',
        'specific_user_ids' => 'array',
        'excluded_user_ids' => 'array',
        'applicable_service_ids' => 'array',
        'applicable_category_ids' => 'array',
        'excluded_service_ids' => 'array',
        'excluded_category_ids' => 'array',
        'applicable_cities' => 'array',
        'applicable_zip_codes' => 'array',
        'stackable_with' => 'array',
        'metadata' => 'array',
        'conditions' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'available_from' => 'datetime:H:i',
        'available_to' => 'datetime:H:i',
        'last_used_date' => 'date',
        'verified_at' => 'datetime',
        'available_monday' => 'boolean',
        'available_tuesday' => 'boolean',
        'available_wednesday' => 'boolean',
        'available_thursday' => 'boolean',
        'available_friday' => 'boolean',
        'available_saturday' => 'boolean',
        'available_sunday' => 'boolean',
        'first_order_only' => 'boolean',
        'first_time_user' => 'boolean',
        'stackable' => 'boolean',
        'apply_before_tax' => 'boolean',
        'auto_apply' => 'boolean',
        'needs_coupon_code' => 'boolean',
        'is_referral' => 'boolean',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'is_verified' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['is_valid', 'discount_text', 'usage_left'];

    public function usages(): HasMany
    {
        return $this->hasMany(PromotionUsage::class);
    }

    public function userEligibility(): HasMany
    {
        return $this->hasMany(UserPromotionEligibility::class);
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(PromotionRedemption::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(PromotionAnalytics::class);
    }

    public function rules(): HasMany
    {
        return $this->hasMany(PromotionRule::class);
    }

    public function getIsValidAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->isActive() && 
               $this->isWithinDateRange() &&
               $this->isWithinUsageLimit();
    }

    public function getDiscountTextAttribute(): string
    {
        switch ($this->type) {
            case 'percentage':
                return "{$this->value}% OFF";
            case 'fixed_amount':
                return "$" . number_format($this->value, 2) . " OFF";
            case 'free_delivery':
                return "Free Delivery";
            case 'buy_x_get_y':
                if ($this->get_discount_percentage) {
                    return "Buy {$this->buy_quantity} Get {$this->get_discount_percentage}% OFF";
                }
                return "Buy {$this->buy_quantity} Get {$this->get_quantity} Free";
            default:
                return $this->name;
        }
    }

    public function getUsageLeftAttribute(): ?int
    {
        if ($this->max_uses_per_user) {
            return max(0, $this->max_uses_per_user - $this->getUserUsageCount());
        }
        return null;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isWithinDateRange(): bool
    {
        $now = now();
        
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        
        if ($this->expires_at && $now->gt($this->expires_at)) {
            return false;
        }
        
        return true;
    }

    public function isWithinTimeRestriction(): bool
    {
        if ($this->time_restriction === 'anytime') {
            return true;
        }
        
        $now = now();
        
        // Check day of week
        $dayOfWeek = strtolower($now->format('l'));
        if (!$this->{"available_{$dayOfWeek}"}) {
            return false;
        }
        
        // Check time range
        if ($this->available_from && $this->available_to) {
            $currentTime = $now->format('H:i');
            if ($currentTime < $this->available_from->format('H:i') || 
                $currentTime > $this->available_to->format('H:i')) {
                return false;
            }
        }
        
        return true;
    }

    public function isWithinUsageLimit(): bool
    {
        // Check total usage limit
        if ($this->max_uses_per_day && $this->total_uses_today >= $this->max_uses_per_day) {
            return false;
        }
        
        return true;
    }

    public function isEligibleForUser(User $user): bool
    {
        // Check user eligibility type
        switch ($this->user_eligibility) {
            case 'new_users':
                if ($user->orders()->count() > 0) {
                    return false;
                }
                break;
            case 'returning_users':
                if ($user->orders()->count() == 0) {
                    return false;
                }
                break;
            case 'specific_users':
                if (!in_array($user->id, $this->specific_user_ids ?? [])) {
                    return false;
                }
                break;
        }
        
        // Check excluded users
        if (in_array($user->id, $this->excluded_user_ids ?? [])) {
            return false;
        }
        
        // Check first order only
        if ($this->first_order_only && $user->orders()->count() > 0) {
            return false;
        }
        
        // Check first time user
        if ($this->first_time_user && $user->orders()->count() > 0) {
            return false;
        }
        
        // Check per-user usage limit
        if ($this->max_uses_per_user) {
            $userUsage = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsage >= $this->max_uses_per_user) {
                return false;
            }
        }
        
        return true;
    }

    public function isApplicableToCart(array $cart, User $user): bool
    {
        // Check minimum order amount
        if ($this->min_order_amount > 0) {
            $orderTotal = collect($cart)->sum('total');
            if ($orderTotal < $this->min_order_amount) {
                return false;
            }
        }
        
        // Check minimum quantity
        if ($this->min_quantity > 0) {
            $totalQuantity = collect($cart)->sum('quantity');
            if ($totalQuantity < $this->min_quantity) {
                return false;
            }
        }
        
        // Check minimum items
        if ($this->min_items > 0) {
            $totalItems = count($cart);
            if ($totalItems < $this->min_items) {
                return false;
            }
        }
        
        // Check applicable services
        if ($this->applicable_service_ids) {
            $cartServiceIds = collect($cart)->pluck('service_id')->toArray();
            if (!array_intersect($this->applicable_service_ids, $cartServiceIds)) {
                return false;
            }
        }
        
        return true;
    }

    public function calculateDiscount(array $cart): float
    {
        $total = collect($cart)->sum('total');
        
        switch ($this->type) {
            case 'percentage':
                $discount = $total * ($this->value / 100);
                break;
            case 'fixed_amount':
                $discount = min($this->value, $total);
                break;
            case 'free_delivery':
                $discount = $cart['delivery_fee'] ?? 0;
                break;
            default:
                $discount = 0;
        }
        
        // Apply max discount limit
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }
        
        return round($discount, 2);
    }

    public function recordUsage(User $user, Order $order, float $discountAmount, array $context = []): PromotionUsage
    {
        $usage = PromotionUsage::create([
            'promotion_id' => $this->id,
            'user_id' => $user->id,
            'order_id' => $order->id,
            'original_amount' => $order->subtotal,
            'discount_amount' => $discountAmount,
            'final_amount' => $order->total,
            'applied_rules' => json_encode($context),
            'used_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
        
        // Update usage counters
        $this->increment('total_uses');
        $this->increment('total_uses_today');
        $this->last_used_date = now();
        $this->save();
        
        // Update user eligibility
        UserPromotionEligibility::updateOrCreate(
            ['user_id' => $user->id, 'promotion_id' => $this->id],
            ['usage_count' => $this->usages()->where('user_id', $user->id)->count()]
        );
        
        // Update analytics
        $this->updateAnalytics($discountAmount, $order->total);
        
        return $usage;
    }

    protected function updateAnalytics(float $discountAmount, float $orderValue): void
    {
        $analytics = PromotionAnalytics::firstOrCreate([
            'promotion_id' => $this->id,
            'date' => now()->toDateString(),
        ]);
        
        $analytics->increment('redemptions');
        $analytics->increment('total_discount', $discountAmount);
        $analytics->increment('total_order_value', $orderValue);
        $analytics->save();
    }

    public function getUserUsageCount(int $userId = null): int
    {
        if (!$userId) {
            return 0;
        }
        
        return $this->usages()
            ->where('user_id', $userId)
            ->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereRaw('total_uses < max_uses');
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_public', true);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->active()
            ->where(function ($q) use ($user) {
                $q->where('user_eligibility', 'all')
                    ->orWhere(function ($q2) use ($user) {
                        $q2->where('user_eligibility', 'specific_users')
                            ->whereJsonContains('specific_user_ids', $user->id);
                    });
            })
            ->whereDoesntHave('usages', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
    }
}