<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderItemController;
use Faker\Guesser\Name;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index');
    });


    Route::prefix('product')->name('product.')->controller(ProductController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');

        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/update/{id}', 'update')->name('update');
        // Route::get('/delete/{id}', 'delete')->name('delete');
    });
    Route::prefix('order')->name('order.')->controller(OrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::put('/status/update/{id}', 'status_update')->name('status.update');

        Route::get('admin/orders/{id}/details', 'show_details')->name('details');

        Route::get('/ordered-items', 'ordered_items')->name('ordered_items');

        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::post('/update/{id}', 'update')->name('update');
        Route::get('/delete/{id}', 'delete')->name('delete');
    });
});

Route::get('/admin/orders/{id}/items', [OrderController::class, 'getOrderItems']);

Route::post('/generate-worker-pdf', [OrderController::class, 'generateWorkerPdf'])->name('admin.generate.worker.pdf');
Route::post('/generate-ordered-items-pdf', [OrderController::class, 'ordered_items_pdf'])->name('admin.generate.ordered_items.pdf');
