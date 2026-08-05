<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_edit_logs')) {
            Schema::create('product_edit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedInteger('from_version');
                $table->unsignedInteger('to_version');
                $table->json('old_values');
                $table->json('new_values');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'created_at']);
                $table->index(['product_id', 'to_version']);
            });
        }

        if (! Schema::hasIndex('product_edit_logs', 'product_edit_logs_product_id_created_at_index')) {
            Schema::table('product_edit_logs', function (Blueprint $table) {
                $table->index(['product_id', 'created_at']);
            });
        }

        if (! Schema::hasIndex('product_edit_logs', 'product_edit_logs_product_id_to_version_index')) {
            Schema::table('product_edit_logs', function (Blueprint $table) {
                $table->index(['product_id', 'to_version']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_edit_logs');
    }
};
