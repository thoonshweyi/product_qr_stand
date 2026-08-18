<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'workflow_id',
        'current_step_id',
        'status',
    ];
    
    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }
}
