<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'rider_id',
        'laundry_id',
        'review_type',
        'review_target_type',
        'review_target_id',
        'rating',
        'communication_rating',
        'timeliness_rating',
        'quality_rating',
        'professionalism_rating',
        'value_rating',
        'packaging_rating',
        'review_text',
        'title',
        'pros',
        'cons',
        'aspects',
        'photos',
        'videos',
        'thumbnail',
        'response_text',
        'responded_at',
        'responded_by',
        'is_verified',
        'verified_at',
        'verified_by',
        'is_published',
        'is_featured',
        'is_flagged',
        'flag_reason',
        'helpful_count',
        'unhelpful_count',
        'helpful_votes',
        'is_verified_purchase',
        'purchased_at',
        'is_incentivized',
        'incentive_type',
        'incentive_details',
        'is_anonymous',
        'anonymous_name',
        'ip_address',
        'user_agent',
        'review_location',
        'metadata',
    ];

    protected $casts = [
        'rating' => 'integer',
        'communication_rating' => 'integer',
        'timeliness_rating' => 'integer',
        'quality_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'value_rating' => 'integer',
        'packaging_rating' => 'integer',
        'pros' => 'array',
        'cons' => 'array',
        'aspects' => 'array',
        'photos' => 'array',
        'videos' => 'array',
        'helpful_votes' => 'array',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'is_flagged' => 'boolean',
        'is_verified_purchase' => 'boolean',
        'is_incentivized' => 'boolean',
        'is_anonymous' => 'boolean',
        'verified_at' => 'datetime',
        'responded_at' => 'datetime',
        'purchased_at' => 'datetime',
    ];

    protected $appends = [
        'average_aspect_rating',
        'rating_percentage',
        'display_name',
        'is_responded'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    public function laundry(): BelongsTo
    {
        return $this->belongsTo(Laundry::class);
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ReviewReport::class);
    }

    public function getAverageAspectRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->communication_rating,
            $this->timeliness_rating,
            $this->quality_rating,
            $this->professionalism_rating,
            $this->value_rating,
            $this->packaging_rating,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 2);
    }

    public function getRatingPercentageAttribute(): int
    {
        return round(($this->rating / 5) * 100);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return $this->anonymous_name ?? 'Anonymous User';
        }
        
        return $this->user->name ?? 'User';
    }

    public function getIsRespondedAttribute(): bool
    {
        return !is_null($this->responded_at);
    }

    public function publish(): void
    {
        $this->is_published = true;
        $this->save();
    }

    public function unpublish(): void
    {
        $this->is_published = false;
        $this->save();
    }

    public function verify(string $verifiedBy = 'admin'): void
    {
        $this->is_verified = true;
        $this->verified_at = now();
        $this->verified_by = $verifiedBy;
        $this->save();
    }

    public function flag(string $reason): void
    {
        $this->is_flagged = true;
        $this->flag_reason = $reason;
        $this->save();
    }

    public function resolveFlag(): void
    {
        $this->is_flagged = false;
        $this->flag_reason = null;
        $this->save();
    }

    public function respond(string $response, int $responderId): void
    {
        $this->response_text = $response;
        $this->responded_at = now();
        $this->responded_by = $responderId;
        $this->save();
    }

    public function markHelpful(int $userId, bool $isHelpful): void
    {
        // Check if user already voted
        $existingVote = ReviewHelpfulVote::where('review_id', $this->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingVote) {
            // Update existing vote
            $oldVote = $existingVote->is_helpful;
            $existingVote->update(['is_helpful' => $isHelpful]);
            
            // Update counters
            if ($oldVote && !$isHelpful) {
                $this->decrement('helpful_count');
                $this->increment('unhelpful_count');
            } elseif (!$oldVote && $isHelpful) {
                $this->increment('helpful_count');
                $this->decrement('unhelpful_count');
            }
        } else {
            // Create new vote
            ReviewHelpfulVote::create([
                'review_id' => $this->id,
                'user_id' => $userId,
                'is_helpful' => $isHelpful,
            ]);
            
            // Update counter
            if ($isHelpful) {
                $this->increment('helpful_count');
            } else {
                $this->increment('unhelpful_count');
            }
        }
        
        $this->save();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('is_verified', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('review_type', $type);
    }

    public function scopeForRider($query, int $riderId)
    {
        return $query->where('rider_id', $riderId)->where('review_type', 'rider');
    }

    public function scopeForLaundry($query, int $laundryId)
    {
        return $query->where('laundry_id', $laundryId)->where('review_type', 'laundry');
    }

    public function scopeHighestRated($query)
    {
        return $query->orderBy('rating', 'desc')->orderBy('helpful_count', 'desc');
    }

    public function scopeMostHelpful($query)
    {
        return $query->orderBy('helpful_count', 'desc')->orderBy('created_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}