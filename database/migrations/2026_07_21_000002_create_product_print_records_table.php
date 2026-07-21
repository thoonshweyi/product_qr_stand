<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_print_records', function (Blueprint $table) {
            $table->id();
            $table->uuid('print_reference')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_code');
            $table->string('product_name');
            $table->text('print_url');
            $table->string('status', 20)->default('printing');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('print_started_at');
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('printed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_print_records');
    }
};
