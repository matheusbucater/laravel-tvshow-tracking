<?php

use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\TvShowController;
use App\Models\Episode;
use App\Models\Season;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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

Route::group(['middleware' => ['auth', 'web']], function () {
    Route::get('/', [TvShowController::class, 'dashboard']);

    Route::get('/dashboard', [TvShowController::class, 'dashboard'])->name('dashboard');

    Route::get('/search', [TvShowController::class, 'search'])->name('search');

    Route::get('/{show_category}', [TvShowController::class, 'categories']);

    Route::get('/show/{id}', [TvShowController::class, 'seasons']);

    Route::get('show/{id}/season/{number}', [EpisodeController::class, 'details']);

    Route::get('show/{id}/season/', [TvShowController::class, 'searchSeason'])->name('season');

    Route::post('/show/{tv_id}/season/{season_number}/episode-unique/{episode_number}', [EpisodeController::class, 'addEpisode']);

    Route::post('/show/{tv_id}/season/{season_number}/episode/{episode_number}', [EpisodeController::class, 'handleEpisodes']);

    Route::post('/show/{tv_id}', [TvShowController::class, 'addTvShow']);

    Route::post('/show/{tv_id}/season/{season_number}', [EpisodeController::class, 'massChange']);
});

require __DIR__.'/auth.php';
