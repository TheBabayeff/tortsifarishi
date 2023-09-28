<?php

namespace App\Models;

use App\Models\Shop\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InternalOrder extends Model implements HasMedia
{
    use HasFactory , SoftDeletes , InteractsWithMedia;

    protected $fillable = [
        'shop_customer_id',
        'name',
        'beh',
        'date',
        'time',
        'total',
        'status',
        'description'
    ];


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('order-photo');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'shop_customer_id');
    }

}
