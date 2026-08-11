<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('status_id')->references('id')->on('statuses')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};


// workflows
// ---------
// id
// code
// name

// workflow_steps
// -------------
// id
// workflow_id
// department_id
// step_order
// name

// product_workflows
// ----------------
// id
// product_id
// workflow_id
// status
// current_step