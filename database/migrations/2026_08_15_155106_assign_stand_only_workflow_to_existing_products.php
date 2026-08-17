<?php

use App\Models\Product;
use App\Models\ProductWorkflow;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $workflow = Workflow::where('name', 'Stand Only')->first();

        if (!$workflow) {
            return;
        }

        $firstWorkflowStep = WorkflowStep::where('workflow_id', $workflow->id)
            ->orderBy('step_no')
            ->orderBy('id')
            ->first();

        if (!$firstWorkflowStep) {
            return;
        }

        Product::query()->each(function ($product) use (
            $workflow,
            $firstWorkflowStep
        ) {
            ProductWorkflow::firstOrCreate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'workflow_id' => $workflow->id,
                    'current_step_id' => $firstWorkflowStep->id,
                    'status' => 'ongoing',
                ]
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('existing_products', function (Blueprint $table) {
            //
        });
    }
};
