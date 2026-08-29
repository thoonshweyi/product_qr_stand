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
                $query->whereExists(function ($query) use ($workflowChannel) {
                    $query->selectRaw('1')
                        ->from('product_workflows')
                        ->join('workflows', 'workflows.id', '=', 'product_workflows.workflow_id')
                        ->whereColumn('product_workflows.product_id', 'products.id')
                        ->where('workflows.slug', 'like', '%'.$workflowChannel.'%');
                });
            })
            ->when($workflowChannel, function ($query) use ($workflowChannel, $user) {
                $isStandViewer = $workflowChannel === 'stand'
                    && $user->hasRoles(['Viewer']);

                $query->where(function ($query) use ($user, $isStandViewer) {
                    $query->where('user_id', $user->id)
                        ->orWhere(function ($query) use ($user) {
                            $query->canAction($user);
                        });

                    if ($isStandViewer) {
                        $query->orWhere(function ($query) use ($user) {
                            $query->visibleToStandViewerAfterChecked($user);
                        });
                    }
                });
            })
            ->when(! $user->can('viewany', Product::class), fn ($query) => $query->where('status_id', 1))
            ->count();
    }
}
