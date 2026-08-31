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
            'stand' => $this->productCountByWorkflowSlug('stand-only'),
            'online' => $this->productCountByWorkflowSlug('online-only'),
            'stand_and_online' => $this->productCountByWorkflowSlug('stand-and-online'),
        ];

        $activeBranchCount = Branch::query()
            ->where('status_id', 3)
            ->count();

        $printReportProducts = Product::query()
            ->with([
                'printRecords' => fn ($query) => $query
                    ->with('branch:id,branch_name,branch_code,branch_short_name')
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
            'activeBranchCount',
            'printReportProducts',
        ));
    }

    private function productCountByWorkflowSlug(string $workflowSlug): int
    {
        return Product::query()
            ->whereExists(function ($query) use ($workflowSlug) {
                $query->selectRaw('1')
                    ->from('product_workflows')
                    ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                    ->whereColumn('product_workflows.product_id', 'products.id')
                    ->where('workflows.slug', $workflowSlug);
            })
            ->count();
    }
}
