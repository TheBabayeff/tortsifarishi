<?php

namespace App\Models\Shop;

use App\Models\Comment;
use App\Models\Reason;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * @var string
     */
    protected $table = 'shop_products';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => 'array',
        'product_reason' => 'array',
        'product_images' => 'array',
        'original_filename' => 'array',
        'is_visible' => 'boolean',
        'published_at' => 'date',
    ];


    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_category_product', 'shop_product_id', 'shop_category_id')->withTimestamps();
    }

    public function reasons()
    {
        return $this->belongsToMany(Reason::class, 'shop_product_reason', 'shop_product_id', 'reason_id')->withTimestamps();
    }

    public function compositions()
    {
        return $this->belongsToMany(ProductComposition::class, 'shop_product_composition', 'shop_product_id', 'product_composition_id')->withTimestamps();
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
