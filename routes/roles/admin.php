<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

Route::resource('pengguna', App\Http\Controllers\Admin\UserController::class);

Route::controller(App\Http\Controllers\Admin\ChartController::class)
    ->group(function(){

        Route::get('kunjungan', 'visit')->name('chart.visit');
        Route::get('rawat-inap', 'inpatient')->name('chart.inpatient');
        Route::get('barber-johnson', 'barberJohnson')->name('chart.barber-johnson');
    });


Route::get('/ruangan', function(){
    return view('pages.rooms.index');
})->name('room.index');

Route::get('/ruangan/create', function(){
    return view('pages.rooms.create');
})->name('room.create');