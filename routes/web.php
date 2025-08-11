<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SpaAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Ovde registruješ web rute aplikacije.
| Ove rute se učitavaju kroz RouteServiceProvider i dobijaju "web" middleware.
|
*/

// SPA auth (session-based preko Sanctum-a)
Route::post('/login',    [SpaAuthController::class, 'login'])->name('login');
Route::post('/register', [SpaAuthController::class, 'register']);
Route::post('/logout',   [SpaAuthController::class, 'logout']);

// (opciono) health / ping
Route::get('/ping', fn () => response()->json(['pong' => true]));
