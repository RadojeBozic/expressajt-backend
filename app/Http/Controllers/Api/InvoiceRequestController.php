<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 
use App\Models\InvoiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // ✅ Samo jedan create i odmah pozivamo generatePDF()
        $invoice->generatePDF();

        return response()->json([
            'message' => 'Zahtev za profakturu je uspešno sačuvan.',
            'invoice' => $invoice
        ]);
    }

    public function download($id)
        {
            $invoice = InvoiceRequest::findOrFail($id);

        // 👇 Generiši PDF svaki put pre preuzimanja
        $invoice->generatePDF();
            
            
            $file = "invoices/invoice-$id.pdf";

            if (!Storage::exists($file)) {
                return response()->json(['error' => 'PDF nije pronađen.'], 404);
            }

            return Storage::download($file);
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
/* 
    public function generatePDF()
    {
        $file = "invoices/invoice-{$this->id}.pdf";

        if (Storage::exists($file)) {
            return;
        }

        $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this]);
        Storage::put($file, $pdf->output());
    } */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,paid,cancelled'
        ]);

        $invoice = InvoiceRequest::findOrFail($id);
        $invoice->status = $request->status;
        $invoice->save();

        return response()->json(['message' => 'Status uspešno ažuriran.']);
    }

    public function destroy($id)
    {
        $invoice = InvoiceRequest::findOrFail($id);
        $invoice->delete();

        // Obriši PDF fajl ako postoji
        $file = "invoices/invoice-$id.pdf";
        if (\Storage::exists($file)) {
            \Storage::delete($file);
        }

        return response()->json(['message' => 'Zahtev za fakturu je uspešno obrisan.']);
    }
   


}
