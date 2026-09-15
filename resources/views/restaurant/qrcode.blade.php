<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR code — {{ $restaurant->name }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">

    <style>
        :root {
            --ink: #121212;
            --ink-soft: #5C5C58;
            --red: #A9271E;
            --border: #DEDDD9;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #EFEFEC;
            color: var(--ink);
            font-family: 'Work Sans', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2.5rem 1rem 4rem;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: 1.75rem;
        }

        .toolbar a,
        .toolbar button {
            font: inherit;
            font-size: .8rem;
            font-weight: 600;
            padding: .6rem 1.1rem;
            border-radius: 2px;
            border: 1px solid var(--ink);
            background: #fff;
            color: var(--ink);
            text-decoration: none;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .toolbar button.primary { background: var(--ink); color: #fff; }
        .toolbar a:hover, .toolbar button:hover { background: var(--red); border-color: var(--red); color: #fff; }

        /* Affiche au format A5 portrait, centrée sur la page imprimée. */
        .poster {
            width: 148mm;
            max-width: 100%;
            background: #fff;
            border: 1px solid var(--border);
            padding: 16mm 14mm;
            text-align: center;
        }

        .brand {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.45rem;
            font-weight: 600;
            letter-spacing: -.02em;
        }

        .brand span { color: var(--red); }

        .rule {
            width: 40px;
            height: 1px;
            background: var(--red);
            margin: 1rem auto 1.5rem;
        }

        .name {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 2rem;
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -.02em;
            margin: 0 0 .4rem;
        }

        .meta {
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .14em;
            font-weight: 600;
            color: var(--ink-soft);
            margin: 0;
        }

        .qr {
            margin: 1.75rem auto 1.25rem;
            width: 62mm;
            height: 62mm;
            padding: 5mm;
            border: 1px solid var(--border);
            background: #fff;
        }

        .qr img { display: block; width: 100%; height: 100%; }

        .call {
            font-family: 'Fraunces', Georgia, serif;
            font-size: 1.05rem;
            font-weight: 600;
            margin: 0 0 .35rem;
        }

        .hint {
            font-size: .78rem;
            color: var(--ink-soft);
            line-height: 1.6;
            margin: 0 auto;
            max-width: 32ch;
        }

        .uses {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin: 1.5rem 0 1.25rem;
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
            color: var(--ink-soft);
        }

        .uses i { color: var(--red); display: block; font-size: 1.1rem; margin-bottom: .25rem; }

        .url {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            font-size: .68rem;
            color: var(--ink-soft);
            word-break: break-all;
        }

        @media print {
            @page { size: A5 portrait; margin: 0; }

            body { background: #fff; padding: 0; display: block; }
            .toolbar { display: none !important; }

            .poster {
                width: 148mm;
                height: 210mm;
                border: none;
                display: flex;
                flex-direction: column;
                justify-content: center;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button type="button" class="primary" onclick="window.print()">
        <i class="ri-printer-line"></i> Imprimer
    </button>
    <a href="{{ route('restaurants.qrcode.svg', $restaurant->slug) }}?download=1">
        <i class="ri-download-2-line"></i> Télécharger le QR (SVG)
    </a>
    <a href="{{ route('restaurants.show', $restaurant->slug) }}">
        <i class="ri-arrow-left-line"></i> Retour à la fiche
    </a>
</div>

<div class="poster">
    <div class="brand">O<span>Menu</span></div>
    <div class="rule"></div>

    <h1 class="name">{{ $restaurant->name }}</h1>
    <p class="meta">{{ $restaurant->cuisine_type ?: 'Restaurant' }} · {{ $restaurant->city }}</p>

    <div class="qr">
        <img src="{{ $qr }}" alt="QR code vers la carte de {{ $restaurant->name }}">
    </div>

    <p class="call">Scannez pour découvrir la carte</p>
    <p class="hint">
        Pointez l'appareil photo de votre téléphone vers ce code pour consulter
        les plats, commander ou réserver une table.
    </p>

    <div class="uses">
        <div><i class="ri-restaurant-2-line"></i> Carte</div>
        <div><i class="ri-shopping-bag-3-line"></i> Commande</div>
        <div><i class="ri-calendar-check-line"></i> Réservation</div>
    </div>

    <div class="url">{{ $url }}</div>
</div>

</body>
</html>
