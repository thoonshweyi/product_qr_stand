<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_print_records', 'product_version')) {
            Schema::table('product_print_records', function (Blueprint $table) {
                $table->unsignedInteger('product_version')->default(1)->after('product_id');
            });
        }

        if (! Schema::hasIndex('product_print_records', 'product_print_branch_version_index')) {
            Schema::table('product_print_records', function (Blueprint $table) {
                $table->index(
                    ['product_id', 'branch_id', 'status', 'product_version'],
                    'product_print_branch_version_index'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('product_print_records', 'product_print_branch_version_index')) {
            Schema::table('product_print_records', function (Blueprint $table) {
                $table->dropIndex('product_print_branch_version_index');
            });
        }

        if (Schema::hasColumn('product_print_records', 'product_version')) {
            Schema::table('product_print_records', function (Blueprint $table) {
                $table->dropColumn('product_version');
            });
        }
    }
};
