<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_print_records', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();

            $table->index(['product_id', 'branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('product_print_records', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'branch_id', 'status']);
            $table->dropConstrainedForeignId('branch_id');
        });
    }
};
