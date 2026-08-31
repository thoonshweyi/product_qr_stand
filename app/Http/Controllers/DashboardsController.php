<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $activeBranchCount = Branch::where('status_id', 3)->count();

        $printReportProducts = $this->branchPrintReportQuery()
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboards.index', compact(
            'activeBranchCount',
            'productCounts',
            'printReportProducts',
        ));
    }

    public function branchPrintReport(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $printedBranchId = $request->query('printed_branch_id');
        $activeBranchCount = Branch::where('status_id', 3)->count();
        $branches = Branch::query()
            ->where('status_id', 3)
            ->orderBy('branch_code')
            ->get(['id', 'branch_name', 'branch_code', 'branch_short_name']);

        $printReportProducts = $this->branchPrintReportQuery()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('product_code', 'like', '%'.$keyword.'%')
                        ->orWhere('product_name', 'like', '%'.$keyword.'%')
                        ->orWhere('name', 'like', '%'.$keyword.'%');
                });
            })
            ->when(filled($printedBranchId), function ($query) use ($printedBranchId) {
                $query->whereExists(function ($query) use ($printedBranchId) {
                    $query->selectRaw('1')
                        ->from('product_print_records')
                        ->whereColumn('product_print_records.product_id', 'products.id')
                        ->whereColumn('product_print_records.product_version', 'products.print_version')
                        ->where('product_print_records.status', 'printed')
                        ->where('product_print_records.branch_id', $printedBranchId);
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('dashboards.branch-print-report', compact(
            'activeBranchCount',
            'branches',
            'printReportProducts',
        ));
    }

    private function branchPrintReportQuery(): Builder
    {
        return Product::query()
            ->select('products.*')
            ->with([
                'printRecords' => fn ($query) => $query
                    ->with('branch:id,branch_name,branch_code,branch_short_name')
                    ->where('status', 'printed')
                    ->whereNotNull('branch_id')
                    ->latest('printed_at')
                    ->select(['id', 'product_id', 'branch_id', 'product_version', 'printed_at']),
            ])
            ->selectSub(
                DB::table('product_print_records')
                    ->join('branches', 'branches.id', '=', 'product_print_records.branch_id')
                    ->selectRaw('COUNT(DISTINCT product_print_records.branch_id)')
                    ->whereColumn('product_print_records.product_id', 'products.id')
                    ->whereColumn('product_print_records.product_version', 'products.print_version')
                    ->where('product_print_records.status', 'printed')
                    ->whereNotNull('product_print_records.branch_id')
                    ->where('branches.status_id', 3),
                'all_branch_printed_count'
            )
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('product_workflows')
                    ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                    ->whereColumn('product_workflows.product_id', 'products.id')
                    ->where('workflows.slug', 'like', '%stand%');
            })
            ->whereIn('stage', ['checked', 'finished']);
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
