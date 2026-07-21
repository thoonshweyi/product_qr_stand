<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrintRecord;
use Illuminate\Http\Request;

class ProductPrintController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $record = $product->printRecords()->create([
            'user_id' => $request->user()?->id,
            'status' => 'requested',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'requested_at' => now(),
        ]);

        return $this->sendRespond($record, 'Print request recorded.');
    }

    public function close(Request $request, ProductPrintRecord $printRecord)
    {
        abort_unless($printRecord->user_id === $request->user()?->id, 403);

        if (! $printRecord->dialog_closed_at) {
            $printRecord->update([
                'status' => 'dialog_closed',
                'dialog_closed_at' => now(),
            ]);
        }

        return $this->sendRespond($printRecord, 'Print dialog closure recorded.');
    }
}
