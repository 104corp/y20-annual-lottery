<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function () {
    return response(['data' => 'success']);
});

Route::namespace('\App\Actions')->group(function () {
    Route::prefix('candidate')->group(function () {
        Route::get('/all', 'FillCandidates');
        Route::get('/no-win', 'GetCandidateNotWinning');
    });

    Route::prefix('award')->group(function () {
        Route::get('/all', 'FillAwards');
        Route::post('/create', 'CreateAward');
    });

    Route::prefix('winner')->group(function () {
        Route::get('/', 'GetWinner');
        Route::get('/all', 'GetAllWinners');
    });

    Route::post('/draw', 'Draw');
    Route::put('/withdraw', 'Withdraw');

    // Route::post('/init', 'Init');
    // Route::put('/flush', 'FlushWinners');
});

