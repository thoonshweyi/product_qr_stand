<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('print_version')->default(1)->after('qr_destination');
        });

        Schema::table('product_print_records', function (Blueprint $table) {
            $table->unsignedInteger('product_version')->default(1)->after('product_id');
            $table->index(['product_id', 'branch_id', 'status', 'product_version'], 'product_print_branch_version_index');
        });
    }

    public function down(): void
    {
        Schema::table('product_print_records', function (Blueprint $table) {
            $table->dropIndex('product_print_branch_version_index');
            $table->dropColumn('product_version');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('print_version');
        });
    }
};
