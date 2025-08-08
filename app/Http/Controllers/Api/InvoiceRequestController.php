<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\InvoiceRequest;

class InvoiceRequestController extends Controller
{
    # POST /api/invoice-request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email',
            'currency'               => 'required|in:rsd,eur',
            'items'                  => 'required|array|min:1',
            'items.*.name'           => 'required|string|max:255',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.unit_price_cents' => 'required|integer|min:0',
            'description'            => 'nullable|string|max:1000',
        ]);

        // Obračun total-a u EUR centima iz stavki
        $totalCents = 0;
        foreach ($validated['items'] as $it) {
            $totalCents += ((int)$it['qty']) * ((int)$it['unit_price_cents']);
        }

        // Kreiraj zahtev
        $invoice = InvoiceRequest::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'currency'    => strtolower($validated['currency']), // eur|rsd (display)
            'items'       => $validated['items'],
            'description' => $request->description,
            // amount je istorijski/legacy – održavamo ga istim kao total radi kompatibilnosti
            'amount'      => $totalCents,
            'total_cents' => $totalCents,
            'status'      => 'pending',
        ]);

        // Generiši PDF (ako već postoji preskače; vidi model)
        $invoice->generatePDF();

        return response()->json([
            'message' => 'Zahtev za profakturu je uspešno sačuvan.',
            'invoice' => $invoice,
        ], 201);
    }

    # GET /api/invoice-request/{id}/pdf
    public function download($id)
    {
        $invoice = InvoiceRequest::findOrFail($id);

        // Ako PDF ne postoji (ili želite uvek sveže) – generiši/regen
        $invoice->generatePDF();

        $file = "invoices/invoice-{$invoice->id}.pdf";
        if (!Storage::exists($file)) {
            return response()->json(['error' => 'PDF nije pronađen.'], 404);
        }

        // opciono: friendly filename
        $filename = "profaktura-{$invoice->id}.pdf";
        return Storage::download($file, $filename);
    }

    # GET /api/my-invoices  (auth:sanctum)
    public function userInvoices(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Po email-u vlasnika (kako smo dogovorili)
        return InvoiceRequest::where('email', $user->email)
            ->latest()
            ->get();
    }

    # GET /api/admin/invoices  (auth:sanctum)
    public function allInvoices(Request $request)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role ?? '', ['admin', 'superadmin'], true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return InvoiceRequest::latest()->get();
        // Ako želiš pagination:
        // return InvoiceRequest::latest()->paginate(25);
    }

    # PUT /api/invoice-request/{id}/status  (auth:sanctum)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,paid,cancelled',
        ]);

        $user = $request->user();
        if (!$user || !in_array($user->role ?? '', ['admin', 'superadmin'], true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $invoice = InvoiceRequest::findOrFail($id);
        $invoice->status = $request->status;
        $invoice->save();

        // (opciono) kada se status promeni – regeneriši PDF sa novim statusom
        $invoice->generatePDF(true);

        return response()->json(['message' => 'Status uspešno ažuriran.']);
    }

    # DELETE /api/invoice-request/{id}  (auth:sanctum)
    public function destroy(Request $request, $id)
    {
        $invoice = InvoiceRequest::findOrFail($id);

        $user = $request->user();
        $isOwner = $user && $user->email === $invoice->email;
        $isAdmin = $user && in_array($user->role ?? '', ['admin', 'superadmin'], true);

        // Dozvoli brisanje vlasniku (sopstveni zahtev) ili adminu
        if (!$isOwner && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Obriši PDF ako postoji
        $file = "invoices/invoice-{$invoice->id}.pdf";
        if (Storage::exists($file)) {
            Storage::delete($file);
        }

        $invoice->delete();

        return response()->json(['message' => 'Zahtev za profakturu je uspešno obrisan.']);
    }
}
