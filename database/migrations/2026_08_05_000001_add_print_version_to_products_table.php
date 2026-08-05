<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('products', 'print_version')) {
            Schema::table('products', function (Blueprint $table) {
                $table->unsignedInteger('print_version')->default(1)->after('qr_destination');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'print_version')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('print_version');
            });
        }
    }
};
