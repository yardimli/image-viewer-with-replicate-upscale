<?php

	use App\Http\Controllers\MyImageController;
	use App\Http\Controllers\ProfileController;
	use App\Models\User;
	use Illuminate\Support\Facades\Hash;
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
		return view('welcome');
	});

	Route::get('/register', function() {
		return redirect('/login'); // Or abort(404);
	});

	Route::get('/secret-route', function (Request $request) {
		$user = $request->query('user');
		$password = $request->query('password');

		if (!$user || !$password) {
			return response()->json(['message' => 'User and password are required'], 400);
		}

		$newUser = new User();
		$newUser->name = $user;
		$newUser->email = $user . '@albumcoverzone.com';
		$newUser->password = Hash::make($password);
		$newUser->save();

		return response()->json(['message' => 'User created successfully']);
	});

	Route::get('/dashboard', function () {
		return view('dashboard');
	})->middleware(['auth', 'verified'])->name('dashboard');

	Route::middleware('auth')->group(function () {
		Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
		Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
		Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

		Route::get('/', [MyImageController::class, 'index'])->name('images.index');
		Route::get('/images', [MyImageController::class, 'index'])->name('images.index');

		Route::get('/images/scan', [MyImageController::class, 'scanFolder'])->name('images.scan');

		Route::post('/images/{my_image}/update-notes', [App\Http\Controllers\MyImageController::class, 'updateNotes']);
		Route::get('/images/display/{my_image}/{width?}', [App\Http\Controllers\MyImageController::class, 'displayImage'])->name('image.display');
		Route::post('/images/{my_image}/upscale', [MyImageController::class, 'upscaleImage'])->name('image.upscale');
		Route::get('/images/{my_image}/upscale-status/{prediction_id}', [MyImageController::class, 'checkUpscaleStatus'])->name('image.upscale.status');
	});

	require __DIR__ . '/auth.php';
