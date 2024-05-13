<?php

	use App\Http\Controllers\MyImageController;
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

	Route::get('/', [MyImageController::class, 'index'])->name('images.index');
	Route::get('/images', [MyImageController::class, 'index'])->name('images.index');

	Route::get('/images/scan', [MyImageController::class, 'scanFolder'])->name('images.scan');

	Route::post('/images/{my_image}/update-notes', [App\Http\Controllers\MyImageController::class, 'updateNotes']);
	Route::get('/images/display/{my_image}/{width?}', [App\Http\Controllers\MyImageController::class, 'displayImage'])->name('image.display');
	Route::post('/images/{my_image}/upscale', [MyImageController::class, 'upscaleImage'])->name('image.upscale');

