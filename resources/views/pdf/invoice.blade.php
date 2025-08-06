<!DOCTYPE html>
<html lang="sr">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 14px; color: #111; }
    .title { font-size: 20px; font-weight: bold; margin-bottom: 10px; }
    .section { margin-bottom: 20px; }
    .label { font-weight: bold; }
    .box { border: 1px solid #ccc; padding: 10px; border-radius: 6px; margin-top: 5px; }
  </style>
</head>
<body>
  <div class="title">𐇾 Profaktura #{{ $invoice->id }}</div>

  @php
    $exchangeRate = 117.5;

    // Ako je valuta EUR, konvertuj u RSD za prikaz
    if ($invoice->currency === 'eur') {
        $convertedAmount = $invoice->amount * $exchangeRate;
        $formattedOriginal = number_format($invoice->amount / 100, 2, ',', '.');
        $formattedConverted = number_format($convertedAmount / 100, 2, ',', '.');
    } else {
        // Ako je korisnik odabrao RSD – pretpostavljamo da je amount u evrima (centima)
        // i radimo konverziju
        $convertedAmount = $invoice->amount * $exchangeRate;
        $formattedOriginal = number_format($convertedAmount / 100, 2, ',', '.');
        $formattedConverted = null;
    }
  @endphp

  <div class="section">
    <span class="label">Datum:</span> {{ \Carbon\Carbon::parse($invoice->created_at)->format('d.m.Y.') }}
  </div>

  <div class="section">
    <span class="label">Račun izdaje:</span>
    <div class="box">
      Radoje Božić pr Računarsko programiranje EXPRESS WEB Beograd-Palilula<br>
      VITEZOVA KARAĐORĐEVE ZVEZDE 50, 11050 Beograd<br>
      MB: 68170168 | PIB: 115191597<br>
      Račun: 160-xxx-xxxxx-xx
    </div>
  </div>

  <div class="section">
    <span class="label">Kupac:</span>
    <div class="box">
      Ime: {{ $invoice->name }}<br>
      Email: {{ $invoice->email }}<br>
      Valuta: {{ strtoupper($invoice->currency) }}
    </div>
  </div>

  <div class="section">
    <span class="label">Iznos za uplatu:</span><br>
    @if ($invoice->currency === 'eur')
      {{ $formattedOriginal }} EUR ({{ $formattedConverted }} RSD)
    @else
      {{ $formattedOriginal }} RSD
    @endif
  </div>

  <div class="section">
    <span class="label">Rok za plaćanje:</span> 7 dana od dana izdavanja.<br>
    <span class="label">Napomena:</span> Kurs konverzije 1 EUR = 117,5 RSD (NBS srednji kurs).
  </div>

  <div class="section">
    <p>Uplatu izvršiti na račun: 265-xxxxxxxxxxxx-xxi u pozivu na broj upisati broj Profakture.</p>
    <p>Hvala na poverenju.<br>Faktura je validna bez pečata i potpisa.<br>
    Preduzetnik nije u sistemu PDV-a.</p>
  </div>
</body>
</html>
