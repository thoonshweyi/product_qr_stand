<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;

class DashboardsController extends Controller
{
    public function index()
    {
        $productCounts = [
            'total' => Product::count(),
            'stand' => $this->productCountByWorkflow('stand'),
            'online' => $this->productCountByWorkflow('online'),
            'stand_and_online' => Product::query()
                ->whereExists(function ($query) {
                    $query->selectRaw('1')
                        ->from('product_workflows')
                        ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                        ->whereColumn('product_workflows.product_id', 'products.id')
                        ->where('workflows.slug', 'like', '%stand%')
                        ->where('workflows.slug', 'like', '%online%');
                })
                ->count(),
        ];

        $branches = Branch::query()
            ->where('status_id', 3)
            ->orderBy('branch_code')
            ->get(['id', 'branch_name', 'branch_code', 'branch_short_name']);

        $printReportProducts = Product::query()
            ->with([
                'printRecords' => fn ($query) => $query
                    ->where('status', 'printed')
                    ->whereNotNull('branch_id')
                    ->latest('printed_at')
                    ->select(['id', 'product_id', 'branch_id', 'product_version', 'printed_at']),
            ])
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('product_workflows')
                    ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                    ->whereColumn('product_workflows.product_id', 'products.id')
                    ->where('workflows.slug', 'like', '%stand%');
            })
            ->whereIn('stage', ['checked', 'finished'])
            ->latest('id')
            ->limit(30)
            ->get(['id', 'product_code', 'product_name', 'name', 'brand', 'stage', 'print_version']);

        return view('dashboards.index', compact(
            'productCounts',
            'branches',
            'printReportProducts',
        ));
    }

    private function productCountByWorkflow(string $workflowChannel): int
    {
        return Product::query()
            ->whereExists(function ($query) use ($workflowChannel) {
                $query->selectRaw('1')
                    ->from('product_workflows')
                    ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                    ->whereColumn('product_workflows.product_id', 'products.id')
                    ->where('workflows.slug', 'like', '%'.$workflowChannel.'%');
            })
            ->count();
    }
}
