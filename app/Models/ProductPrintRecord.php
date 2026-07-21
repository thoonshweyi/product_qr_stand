<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrintRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'print_reference',
        'product_code',
        'product_name',
        'print_url',
        'status',
        'ip_address',
        'user_agent',
        'print_started_at',
        'printed_at',
    ];

    protected $casts = [
        'print_started_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
