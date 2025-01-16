<?php

namespace App\Models;

use App\Enums\MovieTag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'price', 'tag'];

    protected static array $adjustments;

    protected $casts = [
        'tag' => MovieTag::class,
    ];

    /**
     * Boot the model and initialize the static adjustments property.
     */
    protected static function boot()
    {
        parent::boot();

        // Initialize adjustments from the config
        self::$adjustments = config('price_adjustments.adjustments', []);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_movie');
    }

    /**
     * Get all movies with adjusted prices.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function priceAdjusted()
    {
        return self::all()->map(function ($movie) {
            // Add the adjusted price to the movie object
            $movie->adjusted_price = $movie->price * $movie->getPriceByTag();

            return $movie;
        });
    }

    public function getPriceByTag() {
        $tag = $this->tag instanceof MovieTag ? $this->tag->value : $this->tag;
        return self::$adjustments[$tag] ?? 1.0;
    }
}
