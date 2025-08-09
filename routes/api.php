<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\FreeSiteRequestController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\VulnerabilityReportController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\InvoiceRequestController;
use App\Http\Controllers\PlausibleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ovde se registruju sve API rute za aplikaciju.
| Grupisane su po funkcionalnostima i obezbeđene gde je potrebno.
*/

/* -------------------- JAVNE RUTE -------------------- */

// Autentifikacija
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Test ruta
Route::get('/test', fn() => response()->json(['status' => 'Laravel radi!']));

// Kontakt forma
Route::post('/contact', [ContactController::class, 'store']);

// Free/Pro prezentacije – javne rute
Route::prefix('free-site-request')->group(function () {
    Route::get('/all-site-requests', [FreeSiteRequestController::class, 'index']); // mora pre /{slug}
    Route::post('/', [FreeSiteRequestController::class, 'store']);
    Route::get('/{slug}', [FreeSiteRequestController::class, 'show']);
});

// Prijava ranjivosti
Route::post('/report-vulnerability', [VulnerabilityReportController::class, 'store']);

// Alternativna ruta za prikaz prezentacije
Route::get('/site-request/{slug}', [FreeSiteRequestController::class, 'show']);

/* -------------------- ZAŠTIĆENE RUTE -------------------- */
Route::middleware('auth:sanctum')->group(function () {

    // Autentifikovani korisnik
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Samostalno brisanje naloga (osim admina i superadmina)
    Route::delete('/user', function (Request $request) {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Admin i SuperAdmin nalozi ne mogu biti obrisani ovom metodom.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Vaš nalog je uspešno obrisan.']);
    });

    // Poruke
    Route::get('/my-messages', fn(Request $request) => $request->user()->messages()->latest()->get());
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);

    // Korisnici (admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{id}/role', [UserController::class, 'changeRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // FreeSiteRequest – admin izmene
    Route::prefix('free-site-request')->group(function () {
        Route::put('/{slug}', [FreeSiteRequestController::class, 'update']);
        Route::delete('/{slug}', [FreeSiteRequestController::class, 'destroy']);
        Route::patch('/{slug}/status', [FreeSiteRequestController::class, 'changeStatus']);
    });

    // Prijave ranjivosti – admin
    Route::get('/vulnerabilities', [VulnerabilityReportController::class, 'index']);
    Route::delete('/vulnerabilities/{id}', [VulnerabilityReportController::class, 'destroy']);

    // Stripe naplata
    Route::post('/stripe/checkout', [StripeController::class, 'checkout']);

    // Fakture
    Route::put('/invoice-request/{id}/status', [InvoiceRequestController::class, 'updateStatus']);
    Route::delete('/invoice-request/{id}', [InvoiceRequestController::class, 'destroy']);
    Route::get('/my-invoices', [InvoiceRequestController::class, 'userInvoices']);
    Route::get('/admin/invoices', [InvoiceRequestController::class, 'allInvoices']);
});

/* -------------------- OSTALE RUTE -------------------- */

// AI rute
Route::post('/ai/suggest', [AIController::class, 'suggest']);
Route::post('/ai/chat', [AIController::class, 'chat']);

// Demo sajtovi
Route::get('/demo-sites', [FreeSiteRequestController::class, 'demoSites']);
Route::post('/send-presentation/{slug}', [FreeSiteRequestController::class, 'sendToClient']);

// Kreiranje fakture (javno)
Route::post('/invoice-request', [InvoiceRequestController::class, 'store']);
Route::get('/invoice-request/{id}/pdf', [InvoiceRequestController::class, 'download']);

// Plausible Analytics
Route::post('/plausible/query', [PlausibleController::class, 'query']);

// Test/Debug rute
Route::get('/test-all-sites', [FreeSiteRequestController::class, 'index']);
Route::get('/test-site-request/{slug}', [FreeSiteRequestController::class, 'show']);

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'time'   => now()->toIso8601String(),
        'app'    => config('app.name'),
    ]);
});
