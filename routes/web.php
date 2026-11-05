<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/categories');

Route::resource('categories', \App\Http\Controllers\CategoryController::class)
    ->except('show');
Route::resource('subcategories', \App\Http\Controllers\SubcategoryController::class)
    ->except('show');
Route::resource('products', \App\Http\Controllers\ProductController::class)
    ->except('show');
