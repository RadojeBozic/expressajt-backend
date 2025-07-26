<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\FreeSiteRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VulnerabilityReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ovde se registruju sve API rute za aplikaciju.
| Grupisano po funkcionalnostima i zaštićeno gde je potrebno.
*/

// 🔓 JAVNE RUTE
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/contact', [ContactController::class, 'store']);
Route::get('/test', fn() => response()->json(['status' => 'Laravel radi!']));

// 📨 Poruke (kontakt forma)
Route::post('/contact', [MessageController::class, 'store']);

// 🌐 Free/Pro Prezentacije
Route::prefix('free-site-request')->group(function () {
    Route::post('/', [FreeSiteRequestController::class, 'store']);            // Kreiranje prezentacije
    Route::get('/{slug}', [FreeSiteRequestController::class, 'show']);        // Pregled prezentacije
    Route::middleware('auth:sanctum')->get('/all-site-requests', [FreeSiteRequestController::class, 'index']); // Lista svih prezentacija (za admina)


    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/{slug}', [FreeSiteRequestController::class, 'update']);      // Izmena
        Route::delete('/{slug}', [FreeSiteRequestController::class, 'destroy']);  // Brisanje
    });
});

// 🔒 ZAŠTIĆENE RUTE (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // 🧑‍💼 Auth korisnik
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // 🧾 Poruke korisnika
    Route::get('/my-messages', fn(Request $request) => $request->user()->messages()->latest()->get());

    // 🧑‍🔧 Admin rute za korisnike
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{id}/role', [UserController::class, 'changeRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // 📥 Poruke za admina
    Route::get('/messages', [MessageController::class, 'index']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);
});

    // 🌐 Free/Pro Prezentacije - samo za admina
    Route::middleware('auth:sanctum')->get('/all-site-requests', [FreeSiteRequestController::class, 'index']);

    // ✅ Promena statusa prezentacije
    Route::patch('/free-site-request/{slug}/status', [FreeSiteRequestController::class, 'changeStatus'])->middleware('auth:sanctum');

    // ✅ Pregled jedne prezentacije
    Route::get('/site-request/{slug}', [FreeSiteRequestController::class, 'show']);

    Route::post('/report-vulnerability', [VulnerabilityReportController::class, 'store']);
    Route::post('/vulnerabilities', [VulnerabilityReportController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vulnerabilities', [VulnerabilityReportController::class, 'index']);
    Route::delete('/vulnerabilities/{id}', [VulnerabilityReportController::class, 'destroy']);
});



