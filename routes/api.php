<?php

use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['as' => 'api.'], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::get('pengguna/dt', [UserController::class, 'dt'])->name('pengguna.dt');
        Route::get('departemen/dt', [DepartemenController::class, 'dt'])->name('departemen.dt');
        Route::get('ruangan/dt', [RuanganController::class, 'dt'])->name('ruangan.dt');


        Route::apiResources([
            'pengguna' => UserController::class,
            'departemen' => DepartemenController::class,
            'ruangan' => RuanganController::class,
        ], [
            'only' => ['store', 'edit', 'update', 'destroy'],
            'parameters' => [
                'departemen' => 'departemen'
            ]
        ]);
    });
});
