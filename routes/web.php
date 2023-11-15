<?php

use App\Http\Controllers\ProfileController;
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







Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return view('dashboard');
});

// registrasi
Route::prefix('registrasi')->group(function () {
    Route::get('/pasien-masuk', function () {
        return view('pages.registrasi.pasien-masuk');
    })->name('registrasi.pasien-masuk');
    Route::get('/pasien-keluar', function () {
        return view('pages.registrasi.pasien-keluar');
    })->name('registrasi.pasien-keluar');
});
// admin
Route::prefix('admin')->group(function(){
    Route::get('/pengguna', function () {
        return view('pages.pengguna.index');
    });
    Route::get('/pengguna/create', function () {
        return view('pages.pengguna.create');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
