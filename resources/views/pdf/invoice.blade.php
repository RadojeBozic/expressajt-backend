<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Profaktura #{{ $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; font-size: 14px; }
        .header { margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .info, .footer { margin-top: 20px; }
        .section { margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🧾 Profaktura #{{ $invoice->id }}</h1>
        <p>Datum: {{ $invoice->created_at->format('d.m.Y.') }}</p>
    </div>

    <div class="section">
        <strong>Račun izdaje:</strong><br>
        Radoje Božić pr Računarsko programiranje EXPRESS WEB Beograd-Palilula<br>
        VITEZOVA KARAĐORĐEVE ZVEZDE 50, 11050 Beograd<br>
        MB: 68170168 | PIB: 115191597<br>
        Račun: 160-xxx-xxxxx-xx
    </div>

    <div class="section">
        <strong>Kupac:</strong><br>
        Ime: {{ $invoice->name }}<br>
        Email: {{ $invoice->email }}
    </div>

    <div class="section">
        <strong>Valuta:</strong> {{ strtoupper($invoice->currency) }}<br>
        <strong>Iznos:</strong> 
        @if ($invoice->currency === 'rsd')
            {{ number_format($invoice->amount * 117.5 / 100, 2, ',', '.') }} RSD
        @else
            {{ number_format($invoice->amount / 100, 2, ',', '.') }} EUR
        @endif
    </div>

    <div class="footer">
        Hvala na poverenju.<br>
        Rok za plaćanje: 7 dana.
    </div>
</body>
</html>
