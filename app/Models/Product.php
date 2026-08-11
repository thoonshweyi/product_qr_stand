<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $casts = [
        'print_version' => 'integer',
    ];

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
        'qr',
        'brand_icon',
        'qr_destination',
        'print_version',
    ];

    public function specificationValues()
    {
        return $this->hasMany(ProductSpecificationValue::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function printRecords()
    {
        return $this->hasMany(ProductPrintRecord::class);
    }

    public function editLogs()
    {
        return $this->hasMany(ProductEditLog::class);
    }

    public function workflows()
    {
        return $this->belongsToMany(Workflow::class, 'product_workflows')->withTimestamps();
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
