<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'price',
        'thumbnail',
        'brand_id'

    ];

    protected static function boot()
    {
        parent::boot();

        // TODO  3rd lesson
        static::created(function (Product $product) {
            $product->slug = $product->slug ?? str($product->title)->slug();
        });
    }


    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }


}
