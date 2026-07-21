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
        'status',
        'ip_address',
        'user_agent',
        'requested_at',
        'dialog_closed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'dialog_closed_at' => 'datetime',
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
