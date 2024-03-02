<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\SponsorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'guest'], function () {
    Route::get('unauthorized', [AuthController::class, 'unauthorized'])->name("unauthorized");
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function () {

    Route::get('cars/display', [CarController::class, 'getCars']);
    Route::post('cars/add', [CarController::class, 'addCar']);

    Route::get('sponsors/display', [SponsorController::class, 'getSponsors']);
    Route::post('sponsors/add', [SponsorController::class, 'addSponsor']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
