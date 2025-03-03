<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
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
});
