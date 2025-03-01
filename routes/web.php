<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::prefix('admin')->name('admin.')->controller(DashboardController::class)->group(function () {
    Route::get('/', 'index');
});
