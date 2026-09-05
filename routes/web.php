<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController as AdminDeviceController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('/brands/{brand:slug}', [BrandController::class, 'show'])->name('brands.show');

Route::get('/search', [DeviceController::class, 'search'])
    ->middleware('throttle:device-search')
    ->name('devices.search');

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');

Route::get('/devices/{device:slug}', [DeviceController::class, 'show'])->name('devices.show');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::get('/news/{newsPost:slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');

Route::view('/privacy', 'legal.privacy')->name('privacy');

Route::view('/terms', 'legal.terms')->name('terms');


Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('guest')
    ->name('admin.login.submit');


Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/brands/search', [AdminBrandController::class, 'search'])
            ->name('brands.search');

        Route::resource('devices', AdminDeviceController::class)->except(['show']);

        Route::resource('brands', AdminBrandController::class)->except(['show']);

        Route::resource('news', AdminNewsController::class)->except(['show']);
    });
