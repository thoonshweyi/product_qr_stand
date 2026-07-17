<?php

use App\Http\Controllers\BranchesController;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\StatusesController;
use App\Http\Controllers\UsersController;
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
    // return view('welcome');
    return redirect()->route('dashboards.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboards', [DashboardsController::class, 'index'])->name('dashboards.index');

    Route::resource('users', UsersController::class);
    Route::resource('branches', BranchesController::class);
    Route::resource('statuses', StatusesController::class);

    Route::resource('roles', RolesController::class);


    Route::resource('products', ProductController::class);
    Route::get('/productsearch', [ProductController::class, 'search_product'])->name('product_search');
    Route::get('/products/{id}/generateqr', [ProductController::class, 'generateQR'])->name('products.generateqr');


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
