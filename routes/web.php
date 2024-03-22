<?php

use App\Http\Controllers\GamesController;
use App\Http\Controllers\TypeaheadController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RanksController;
use App\Models\Game;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', function () {
    $returnURL = Session::get('returnURL', Request::url().'/../');
    return RCAuth::redirectToLogin($returnURL);
})->name('login');

Route::get('logout', function () {
    RCAuth::logout();
    $returnURL = Request::url().'/../';
    return RCAuth::redirectToLogout($returnURL);
})->name('logout');

Route::middleware('force_login')->group(function () {
    Route::get('/', [GamesController::class, 'submitScore']);
    Route::post('storeScore', [GamesController::class, 'saveScore']);
    Route::get('finduser/', [TypeaheadController::class, 'user_search']);
    Route::get('my_games', [GamesController::class, 'myGames']);
    Route::get('ranks', [RanksController::class, 'showRanks']);
    Route::get('update_ranks', [RanksController::class, 'updateRanks']);

    Route::prefix('adminoptions')->group(function(){
        Route::middleware('force_admin')->group(function () {
            Route::get('all_games', [AdminController::class, 'allGames']);
            Route::get('export_all', [AdminController::class, 'exportAll']);
            Route::get('export', [AdminController::class, 'exportStudentOnly']);
        });
    });
});
