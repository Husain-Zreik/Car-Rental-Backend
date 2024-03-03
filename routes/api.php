<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'guest'], function () {
    Route::get('unauthorized', [AuthController::class, 'unauthorized'])->name("unauthorized");
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'user', 'middleware' => 'auth:api'], function () {

    Route::post('transactions/add', [TransactionController::class, 'addTransaction']);
    Route::get('transactions/display', [TransactionController::class, 'getTransactions']);

    Route::get('dashboard', [DashboardController::class, 'getDashboardInfo']);

    Route::post('rentals/add', [RentalController::class, 'addRent']);

    Route::post('clients/add', [ClientController::class, 'addClient']);
    Route::get('clients/{id}', [ClientController::class, 'getClientDetails']);

    Route::post('cars/add', [CarController::class, 'addCar']);
    Route::get('cars/display', [CarController::class, 'getCars']);
    Route::get('cars/{id}', [CarController::class, 'getCarDetails']);

    Route::get('sponsors/display', [SponsorController::class, 'getSponsors']);
    Route::post('sponsors/add', [SponsorController::class, 'addSponsor']);

    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});
