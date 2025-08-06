<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceRequest;

class InvoiceRequestController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'currency' => 'required|in:rsd,eur',
            'amount' => 'required|integer|min:1',
        ]);

        $invoice = InvoiceRequest::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'currency' => $validated['currency'],
            'amount' => $validated['amount'],
            'status' => 'pending'
        ]);

        // Generiši PDF odmah nakon kreiranja zahteva
        $invoice = InvoiceRequest::create([...]);
        $invoice->generatePDF();

        return response()->json([
            'message' => 'Zahtev za profakturu je uspešno sačuvan.',
            'invoice' => $invoice
        ]);
    }
    public function download($id)
    {
        $invoice = InvoiceRequest::findOrFail($id);

        $pdfPath = storage_path("app/invoices/invoice_{$invoice->id}.pdf");

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'PDF nije pronađen.'], 404);
        }

        return response()->download($pdfPath);
    }

    public function userInvoices(Request $request)
    {
        return InvoiceRequest::where('email', $request->user()->email)->latest()->get();
    }
    public function allInvoices()
{
    if (auth()->user()?->role !== 'admin' && auth()->user()?->role !== 'superadmin') {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    return InvoiceRequest::latest()->get();
}

}
