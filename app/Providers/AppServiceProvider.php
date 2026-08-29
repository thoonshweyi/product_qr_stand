<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('userdata', Auth::user());
        });

        View::composer('layouts.partials.sidebar', function ($view) {
            $user = Auth::user();
            $sidebarProductCounts = [
                'all' => 0,
                'stand' => 0,
                'online' => 0,
            ];

            if ($user) {
                $sidebarProductCounts = [
                    'all' => $this->sidebarProductCount($user),
                    'stand' => $this->sidebarProductCount($user, 'stand'),
                    'online' => $this->sidebarProductCount($user, 'online'),
                ];
            }

            $view->with('sidebarProductCounts', $sidebarProductCounts);
        });

        // Paginator::useBootstrapFive();
        Paginator::useTailwind();

    }

    private function sidebarProductCount($user, ?string $workflowChannel = null): int
    {
        return Product::query()
            ->when($workflowChannel, function ($query) use ($workflowChannel) {
                $this->filterByWorkflowChannel($query, $workflowChannel);
            })
            ->where(function ($query) use ($workflowChannel, $user) {
                $query->where(function ($query) use ($user) {
                    $query->canAction($user);
                });

                if ($workflowChannel !== 'online' && $user->hasRoles(['Viewer']) && filled($user->branch_id)) {
                    $query->orWhere(function ($query) use ($user) {
                        $this->filterByWorkflowChannel($query, 'stand');
                        $this->filterByStandPrintPending($query, $user->branch_id);
                    });
                }
            })
            ->when(! $user->can('viewany', Product::class), fn ($query) => $query->where('status_id', 1))
            ->count();
    }

    private function filterByWorkflowChannel($query, string $workflowChannel): void
    {
        $query->whereExists(function ($query) use ($workflowChannel) {
            $query->selectRaw('1')
                ->from('product_workflows')
                ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                ->whereColumn('product_workflows.product_id', 'products.id')
                ->where('workflows.slug', 'like', '%'.$workflowChannel.'%');
        });
    }

    private function filterByStandPrintPending($query, int $branchId): void
    {
        $query->whereIn('stage', ['checked', 'finished'])
            ->whereDoesntHave('printRecords', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                    ->where('status', 'printed')
                    ->whereColumn('product_print_records.product_version', 'products.print_version');
            });
    }
}
