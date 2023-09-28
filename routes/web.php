<?php

use App\Http\Livewire\Form;
use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;

\Illuminate\Support\Facades\Route::get('form', Form::class);

Route::get('/' , [HomeController::class, 'index'])->name('welcome');

Route::get('/search' , [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('search');

Route::get('/categories' , [\App\Http\Controllers\Api\CategoryController::class, 'index'])->name('categoryIndex');
Route::get('/categories/{id}' , [\App\Http\Controllers\Api\CategoryController::class, 'show'])->name('categoryShow');


Route::get('/products' , [\App\Http\Controllers\Api\ProductController::class, 'index'])->name('productIndex');
Route::get('/products/{id}' , [\App\Http\Controllers\Api\ProductController::class, 'show'])->name('productShow');
