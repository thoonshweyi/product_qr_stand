<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductPrintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WorkflowsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return redirect()->route(auth()->check() ? 'dashboards.index' : 'products.catalog');
    return redirect()->route('dashboards.index');
});

Route::get('/catalog/products', [ProductController::class, 'catalog'])->name('products.catalog');
Route::get('/catalog/scan', [ProductController::class, 'scanner'])->name('products.scanner');
Route::post('/catalog/scan/lookup', [ProductController::class, 'scanLookup'])->name('products.scan.lookup');
Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');
Route::post('/products/{product}/print-records', [ProductPrintController::class, 'store'])->name('products.print-records.store');
Route::patch('/product-print-records/{printRecord}/complete', [ProductPrintController::class, 'complete'])->name('products.print-records.complete');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/branch', [ProfileController::class, 'switchBranch'])->name('profile.branch.switch');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboards', [DashboardsController::class, 'index'])->name('dashboards.index');
    Route::get('/dashboards/branch-print-report', [DashboardsController::class, 'branchPrintReport'])
        ->name('dashboards.branch-print-report');

    Route::resource('statuses', StatusesController::class);

    Route::resource('users', UsersController::class);

    Route::resource('branches', BranchesController::class);
    Route::resource('workflows', WorkflowsController::class)->except(['create', 'edit']);
    Route::get('/branchesstatus', [BranchesController::class, 'changestatus']);

    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::post('/products/workflow/online/finish', [ProductController::class, 'bulkFinishOnlineProducts'])
        ->name('products.workflow.online.finish');
    Route::post('/products/description/clean-json', [ProductController::class, 'cleanDescriptionJson'])
        ->name('products.description.clean-json');
    Route::get('/products/workflow/{channel}', [ProductController::class, 'workflowProducts'])
        ->whereIn('channel', ['stand', 'online'])
        ->name('products.workflow.index');
    Route::resource('products', ProductController::class)->except('show');
    Route::post('/products/{product}/workflow-action', [ProductController::class, 'workflowAction'])
        ->name('products.workflow.action');
    Route::get('/productsstatus', [ProductController::class, 'changestatus']);

    Route::post('/products/batch-print', [ProductController::class, 'batchPrint'])
        ->name('products.batch-print');
    Route::post('/products/batch-print-records', [ProductPrintController::class, 'storeBatch'])
        ->name('products.batch-print-records.store');
    Route::get('/products/{product}/print-history', [ProductPrintController::class, 'history'])
        ->name('products.print-history');

    Route::resource('specifications', SpecificationController::class);
    Route::get('/specificationsstatus', [SpecificationController::class, 'changestatus']);

    Route::get('/productsearch', [ProductController::class, 'search_product'])->name('product_search');
    Route::get('/products-generate-qr/{text}/{format?}', [ProductController::class, 'generateProductQR'])->name('products.generateqr');

    Route::get('/productscreatedemo', function () {
        // Static sample data for the product form prototype. No database records are used here.
        $sampleCategories = [
            'water-pump' => ['name' => 'Water Pump', 'group' => 'Garden'],
            'bathtub' => ['name' => 'Bathtub', 'group' => 'Sanitary'],
            'ceiling-board' => ['name' => 'Ceiling Board', 'group' => 'Roofing & Ceiling'],
        ];

        $sampleAttributes = ['Power', 'Maximum Head', 'Flow Rate', 'Inlet Size', 'Outlet Size', 'Weight', 'Material', 'Color'];
        $sampleBrands = ['IM Dayuan', 'Cotto', 'DECO', 'Ispa', 'TOTO', 'Zhangshi'];
        $sampleStatuses = ['Draft', 'Active'];

        return view('products.createdemo', compact('sampleCategories', 'sampleAttributes', 'sampleBrands', 'sampleStatuses'));
    });

});

require __DIR__.'/auth.php';
