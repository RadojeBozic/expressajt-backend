<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceRequest extends Model
{
    use HasFactory;
    protected $fillable = [
    'name',
    'email',
    'currency',
    'amount',
    'status',
];

public function generatePDF()
{
    // Kreiraj folder ako ne postoji
    if (!Storage::exists('invoices')) {
        Storage::makeDirectory('invoices');
    }

    // Generiši PDF
    $pdf = Pdf::loadView('pdf.invoice', ['invoice' => $this]);

    // Sačuvaj PDF
    $pdf->save(storage_path('app/invoices/invoice-' . $this->id . '.pdf'));
}
}
