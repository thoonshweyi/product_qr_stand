<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_print_records')
            ->whereNull('branch_id')
            ->whereNotNull('user_id')
            ->orderBy('id')
            ->chunkById(500, function ($records) {
                $branchByUser = DB::table('users')
                    ->whereIn('id', $records->pluck('user_id')->filter()->unique())
                    ->whereNotNull('branch_id')
                    ->pluck('branch_id', 'id');

                foreach ($records as $record) {
                    $branchId = $branchByUser->get($record->user_id);

                    if ($branchId) {
                        DB::table('product_print_records')
                            ->where('id', $record->id)
                            ->update(['branch_id' => $branchId]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Historical branch snapshots cannot be safely distinguished from new records.
    }
};
