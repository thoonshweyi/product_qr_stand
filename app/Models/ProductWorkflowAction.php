<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductWorkflowAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_workflow_id',
        'workflow_step_id',
        'user_id',
        'action',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
