<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPrintRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductPrintController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $record = $product->printRecords()->create([
            'user_id' => $request->user()?->id,
            'print_reference' => (string) Str::uuid(),
            'product_code' => $product->product_code,
            'product_name' => $product->name,
            'print_url' => route('products.show', $product),
            'status' => 'printing',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'print_started_at' => now(),
        ]);

        if (! $record->user_id) {
            $request->session()->put('print_records.'.$record->id, true);
        }

        return $this->sendRespond($record, 'Print preview is ready.');
    }

    public function complete(Request $request, ProductPrintRecord $printRecord)
    {
        $canComplete = $printRecord->user_id
            ? $printRecord->user_id === $request->user()?->id
            : (bool) $request->session()->pull('print_records.'.$printRecord->id);

        abort_unless($canComplete, 403);

        if (! $printRecord->printed_at) {
            $printRecord->update([
                'status' => 'printed',
                'printed_at' => now(),
            ]);
        }

        return $this->sendRespond($printRecord, 'Print result recorded.');
    }
}
