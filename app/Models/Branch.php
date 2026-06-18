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
    ];


}
