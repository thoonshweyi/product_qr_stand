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
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id',12)->nullable();
            $table->unsignedBigInteger("branch_id")->nullable();
            $table->unsignedBigInteger("status_id")->nullable();
            $table->unsignedBigInteger("department_id")->nullable();
            $table->string('phone_no')->nullable();
            $table->string('address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("employee_id");
            $table->dropColumn("branch_id");
            $table->dropColumn("status_id");
            $table->dropColumn("department_id");
            $table->dropColumn("phone_no");
            $table->dropColumn("address");
        });
    }
};
