<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $casts = [
        'print_version' => 'integer',
        'online_date' => 'date',
    ];

    protected $fillable = [
        'product_code',
        'brand',
        'name',
        'model',
        'country_of_origin',
        'website_url',
        'description',
        'description_en',
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
        'workflow_id',
        'stage',
        'online_date',
    ];

    // Method 2 Can Action
    protected $appends = [
        'can_action',
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

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
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

    // Method 2 Can Action
    public function getCanActionAttribute()
    {
        $productWorkflow = ProductWorkflow::where('product_id', $this->id)
            ->latest('id')
            ->first();

        $currentWorkflowStep = $productWorkflow?->current_step_id
        ? WorkflowStep::find($productWorkflow->current_step_id)
        : null;

        $isAdmin = request()->user()->hasRoles(['Admin', 'Administrator']);
        $hasStepRole = $currentWorkflowStep
            && (
                ! $currentWorkflowStep->role_id
                || request()->user()->roles()->whereKey($currentWorkflowStep->role_id)->exists()
            );

        $canWorkflowAction = $currentWorkflowStep
            && $productWorkflow->status === 'ongoing'
            && ($isAdmin || $hasStepRole);

        return $canWorkflowAction;
    }

    public function latestWorkflow()
    {
        return $this->hasOne(ProductWorkflow::class)->latestOfMany();
    }

    public function scopeCanAction($query, $user = null)
    {
        $user = $user ?? request()->user();
        $isAdmin = $user->hasRoles(['Admin', 'Administrator']);
        $userRoleIds = $user->roles()->pluck('roles.id')->all();

        return $query->whereHas('latestWorkflow', function ($q) use ($isAdmin, $userRoleIds) {
            $q->where('status', 'ongoing')
                ->whereHas('currentStep', function ($stepQuery) use ($isAdmin, $userRoleIds) {
                    if (! $isAdmin) {
                        $stepQuery->where(function ($roleQuery) use ($userRoleIds) {
                            $roleQuery->whereNull('role_id');
                            if (! empty($userRoleIds)) {
                                $roleQuery->orWhereIn('role_id', $userRoleIds);
                            }
                        });
                    }
                });
        });
    }

    public function scopeVisibleToStandViewerAfterChecked($query, $user = null)
    {
        $user = $user ?? request()->user();

        if (! $user?->hasRoles(['Viewer'])) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('stage', ['checked','finished']);
    }
}
