<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;
    protected $fillable = [
        'workflow_id',
        'step_no',
        'name',
        'action',
        'role_id',
    ];
}
