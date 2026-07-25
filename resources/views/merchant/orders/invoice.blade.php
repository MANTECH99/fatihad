<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            color: #1f2937;
            background: #fff;
            position: relative;
            min-height: 100vh;
        }



        /* --- EN-TÊTE (Numéro et Date en haut) --- */
        .header-top-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #4b5563;
            margin-bottom: 20px;
        }

        /* --- LOGO CENTRÉ ET INFOS DESSOUS --- */
        .header-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 30px;
        }

        .header-logo {
            max-width: 120px;
            margin-bottom: 15px;
        }
        .header-logo img {
            width: 100%;
            height: auto;
            display: block;
        }

        .header-brand h1 {
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 8px; /* Un peu d'espace sous le nom */
            text-transform: uppercase;
            color: #0f3b5e;
        }

        /* C'est ici que l'on change l'espacement des lignes */
        .header-brand p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            line-height: 1.8; /* <--- Augmentez ou diminuez ce chiffre (ex: 1.4, 2.0, 2.5) */
        }
        .header-brand .email {
            font-weight: normal;
        }

        /* --- BANDEAU FACTURE --- */
        .invoice-banner {
            background-color: #3b3b3b !important;
            color: #ffffff !important;
            padding: 12px 0;
            border-radius: 30px;
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .invoice-banner h2 {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
            text-transform: uppercase;
            color: #ffffff !important;
        }

        /* --- DÉTAILS DE LA FACTURE (MODIFICATION ICI) --- */
        .order-details {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            line-height: 1.8;
            padding-left: 5px;
            margin-bottom: 20px;
        }
        .order-details-left p {
            margin: 0;
        }
        .order-details-right {
            text-align: right;
        }

        /* Badge paiement */
        .paid-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }
        .paid { background: #d1fae5; color: #065f46; }
        .pending { background: #fef3c7; color: #92400e; }
        .failed { background: #fee2e2; color: #991b1b; }

        /* --- TABLEAU DES PRODUITS --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e5e7eb;
            font-size: 12px;
            text-transform: uppercase;
            color: #6b7280;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
        }

        /* --- TOTAUX --- */
        .totals { text-align: right; }
        .totals p {
            margin: 0;
            font-size: 14px;
            line-height: 2.2; /* <--- C'est ici qu'on augmente l'espace entre les lignes */
        }
        .totals .grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
            margin-top: 15px; /* Un peu plus d'espace avant le total global */
        }

        /* --- FOOTER --- */
        .footer {
            position: absolute; /* Le sort du flux normal */
            bottom: 0;          /* Le colle tout en bas de la page */
            left: 0;            /* Aligné à gauche */
            right: 0;           /* Aligné à droite (pour centrer) */
            text-align: center;
            font-size: 12px;
            color: #9ca3af;

            padding: 16px 40px; /* Padding haut/bas 16px, gauche/droite 40px pour ne pas toucher les bords */
            box-sizing: border-box;
        }

        @media print {
            body { padding: 0; }
        }
        /* Pour pousser la date et la signature à droite */
        /* Pour pousser la date et la signature à droite */
        .signature-right {
            text-align: right;
            margin-top: 30px;
            font-size: 15px;
            line-height: 1.8;
        }

        /* Conteneur pour le cachet et la signature */
        .stamp-container {
            display: flex;
            justify-content: flex-end; /* Aligne les images à droite */
            align-items: center;       /* Aligne les images sur la même ligne */
            gap: 20px;                 /* Espace entre le cachet et la signature */
            margin-top: 10px;          /* Espace sous le mot "Responsable" */
        }

        .stamp-container img {
            max-height: 80px; /* Ajuste la hauteur ici si besoin */
            width: auto;
            display: block;
        }

         .left-amount {
            text-align: left;
             margin-top: 20px;
        }


    </style>
</head>
<body>

<!-- EN-TÊTE : NUMÉRO ET DATE EN HAUT -->
<div class="header-top-meta">
    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
    <span>Facture {{ $order->order_number }}</span>
</div>

<!-- LOGO CENTRÉ + INFOS DESSOUS -->
<div class="header-brand">
    <div class="header-logo">
        @if($shop->logo)
            @php
                $logoPath = storage_path('app/public/' . $shop->logo);
                $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
            @endphp
            @if($logoBase64)
                <img src="data:image/{{ pathinfo($shop->logo, PATHINFO_EXTENSION) }};base64,{{ $logoBase64 }}" alt="Logo">
            @endif
        @endif
    </div>

    <h1>{{ $shop->name }}</h1>
    <p>{{ $shop->city ?? 'Dakar - Sénégal' }}</p>
    <p>{{ $shop->whatsapp_phone ?? '' }}</p>
    <p class="email">{{ $shop->contact_email ?? '' }}</p>
</div>

<!-- BANDEAU FACTURE -->
<div class="invoice-banner">
    <h2>FACTURE</h2>
</div>

<!-- DÉTAILS DE LA FACTURE (MODIFICATION ICI) -->
<div class="order-details">
    <div class="order-details-left">
        <p>Client : {{ $order->customer_name }}</p>
        <p>{{ $order->customer_email }}</p>
        <p>{{ $order->customer_address }}</p>
    </div>
    <div class="order-details-right">
        <p>Facture N° : {{ $order->order_number }}</p>
        <p>Date d'émission : {{ $order->created_at->format('M d, Y') }}</p>
        @if($order->payment_status === 'paid')
          Statut paiement  :  <span class="paid-badge paid">PAYÉE</span>
        @elseif($order->payment_status === 'pending')
            Statut  :      <span class="paid-badge pending">EN ATTENTE</span>
        @else
            <span class="paid-badge failed">{{ strtoupper($order->payment_status) }}</span>
        @endif
    </div>
</div>

<!-- TABLEAU DES ARTICLES -->
<table>
    <thead>
    <tr>
        <th>DESCRIPTION</th>
        <th>QTÉ</th>
        <th>PRIX UNITAIRE</th>
        <th>TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->price, 0, ',', ' ') }} FCFA</td>
            <td>{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- TOTAUX -->
<div class="totals">
    <p>Sous-total : {{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</p>
    <p>Livraison : {{ number_format($order->delivery_fee, 0, ',', ' ') }} FCFA</p>
    @if($order->payment_fee > 0)
        <p>Frais paiement mobile : {{ number_format($order->payment_fee, 0, ',', ' ') }} FCFA</p>
    @endif
    <p class="grand-total">TOTAL : {{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
</div>
<div class="left-amount">
    <p>La facture est arrêtée à la somme de {{ numberToWordsFr($order->total) }} FRANC CFA.</p>
</div>

<div class="signature-right">
    <p>Fait à Dakar le {{ $order->created_at->format('d/m/Y') }}</p>
    <p>Le responsable</p>

    <!-- CACHET ET SIGNATURE -->
    <div class="stamp-container">
        @if($shop->stamp)
            <!-- On utilise l'accesseur stamp_url que tu as probablement défini dans ton modèle Shop -->
            <img src="{{ asset('storage/' . $shop->stamp) }}" alt="Cachet de l'entreprise" style="max-height: 80px;">
        @else
            <!-- Si pas de cachet, on n'affiche rien -->
        @endif
    </div>
</div>
<!-- FOOTER -->
<div class="footer">
    <p>© {{ date('Y') }} Seneshop. Tous droits réservés.</p>
</div>

<script>window.print();</script>
</body>
</html>
