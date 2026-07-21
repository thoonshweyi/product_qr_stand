<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'brand',
        'name',
        'model',
        'country_of_origin',
        'website_url',
        'description',
        'status_id',
        'user_id',
        'product_name',
        'category_id',
        'unit',
        'image',
        'thumbnail',
        'brand_icon',
    ];

    public function specificationValues()
    {
        return $this->hasMany(ProductSpecificationValue::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_of_origin', 'id');
    }
}
