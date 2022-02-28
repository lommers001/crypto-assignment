<?php

use App\Http\Controllers\PortfolioController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [PortfolioController::class, 'index']);
Route::post('/create', [PortfolioController::class, 'create']);
Route::post('/edit', [PortfolioController::class, 'edit']);
Route::post('/delete', [PortfolioController::class, 'delete']);
