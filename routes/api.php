<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RFIDController;

Route::post('/rfid/tap', [RFIDController::class, 'store']);
