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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ovde se registruju sve API rute za aplikaciju.
| Grupisane su po funkcionalnostima i obezbeđene gde je potrebno.
*/

//
// 🔓 JAVNE RUTE
//
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', fn() => response()->json(['status' => 'Laravel radi!']));

// Kontakt forma
Route::post('/contact', [ContactController::class, 'store']);
Route::post('/contact', [MessageController::class, 'store']);


// 🌐 Free/Pro prezentacije – kreiranje i pregled (javno dostupno)
Route::prefix('free-site-request')->group(function () {
    Route::get('/all-site-requests', [FreeSiteRequestController::class, 'index']); // ❗ VAŽNO: mora pre /{slug}
    Route::post('/', [FreeSiteRequestController::class, 'store']);
    Route::get('/{slug}', [FreeSiteRequestController::class, 'show']);
});

// 🐞 Ranjivosti (javno prijavljivanje)
Route::post('/report-vulnerability', [VulnerabilityReportController::class, 'store']);
Route::post('/vulnerabilities', [VulnerabilityReportController::class, 'store']);

// ✅ Pregled prezentacije preko alternativne rute (slug)
Route::get('/site-request/{slug}', [FreeSiteRequestController::class, 'show']);

//
// 🔐 ZAŠTIĆENE RUTE (auth:sanctum)
//
Route::middleware('auth:sanctum')->group(function () {

    // 👤 Autentifikovani korisnik
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ❌ Samostalno brisanje naloga (osim admina)
    Route::delete('/user', function (Request $request) {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'superadmin'])) {
            return response()->json(['message' => 'Admin i SuperAdmin nalozi ne mogu biti obrisani ovom metodom.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Vaš nalog je uspešno obrisan.']);
    });

    // 📩 Moje poruke
    Route::get('/my-messages', fn(Request $request) => $request->user()->messages()->latest()->get());

    //
    // 🛠 ADMIN RUTE
    //

    // 👥 Korisnici
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{id}/role', [UserController::class, 'changeRole']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // 📥 Poruke korisnika (admin)
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']); // ⬅️ ovo mora da postoji
});

    // 🌐 FreeSiteRequest – izmena i brisanje
    Route::prefix('free-site-request')->group(function () {
        Route::put('/{slug}', [FreeSiteRequestController::class, 'update']);
        Route::delete('/{slug}', [FreeSiteRequestController::class, 'destroy']);
        Route::patch('/{slug}/status', [FreeSiteRequestController::class, 'changeStatus']);
    });

    // 🛡️ Ranjivosti
    Route::get('/vulnerabilities', [VulnerabilityReportController::class, 'index']);
    Route::delete('/vulnerabilities/{id}', [VulnerabilityReportController::class, 'destroy']);
});

    Route::post('/ai/suggest', [AIController::class, 'suggest']);
    Route::post('/ai/chat', [AIController::class, 'chat']);

    Route::get('/demo-sites', [FreeSiteRequestController::class, 'demoSites']);
    Route::post('/send-presentation/{slug}', [FreeSiteRequestController::class, 'sendToClient']);

    // 🛒 Stripe naplata
    Route::middleware('auth:sanctum')->post('/stripe/checkout', [StripeController::class, 'checkout']);
    

    // 🧾 Zahtevi za fakture
    Route::middleware('auth:sanctum')->put('/invoice-request/{id}/status', [InvoiceRequestController::class, 'updateStatus']);
    Route::middleware('auth:sanctum')->delete('/invoice-request/{id}', [InvoiceRequestController::class, 'destroy']);



    Route::post('/invoice-request', [InvoiceRequestController::class, 'store']);
    Route::get('/invoice-request/{id}/pdf', [InvoiceRequestController::class, 'download']);
    Route::middleware('auth:sanctum')->get('/my-invoices', [InvoiceRequestController::class, 'userInvoices']);
    Route::middleware('auth:sanctum')->get('/admin/invoices', [InvoiceRequestController::class, 'allInvoices']);

    //
    // 🧪 TEST / DEBUG (opciono obriši kasnije)
    //
    Route::get('/test-all-sites', [FreeSiteRequestController::class, 'index']);
    Route::get('/test-site-request/{slug}', [FreeSiteRequestController::class, 'show']);
