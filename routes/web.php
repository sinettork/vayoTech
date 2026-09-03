<?php

use App\Http\Controllers\CompareController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;

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
