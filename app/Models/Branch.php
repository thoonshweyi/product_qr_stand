<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $table = "branches";
    protected $primaryKey = "id";

    protected $fillable = [
        'branch_name',
        'branch_code',
        'branch_short_name',
        'branch_address',
        'branch_phone_no',
        'erp_branch_id'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function productPrintRecords()
    {
        return $this->hasMany(ProductPrintRecord::class);
    }

}
