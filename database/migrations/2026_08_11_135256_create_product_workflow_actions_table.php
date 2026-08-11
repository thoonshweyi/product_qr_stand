<?php

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
        Schema::create('product_workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('workflow_process_id');
            $table->unsignedBigInteger("action_by")->nullable();
            $table->unsignedBigInteger('status_id')->nullable(); // action
            $table->text('remark')->nullable();
            $table->timestamp('action_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_workflow_actions');
    }
};


// action

//     pending
//     approved
//     rejected
//     returned
//     skipped