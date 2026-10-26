<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tautan->title ?? 'Tautan' }} | Taut Pinang</title>

    @if (!empty($tautan->styles))
        <!-- Dynamic Meta Tags from Styling -->
        <meta name="description"
            content="{{ $tautan->description ?? 'Halaman tautan resmi ' . ($tautan->title ?? 'Tautan') . ' - BPS Kota Tanjungpinang' }}">

        <!-- Open Graph Metadata -->
        <meta property="og:title" content="{{ $tautan->title . ' | Taut Pinang' }}">
        <meta property="og:description"
            content="{{ $tautan->description ?? 'Halaman tautan resmi ' . ($tautan->title ?? 'Tautan') . ' - BPS Kota Tanjungpinang' }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="Taut Pinang - BPS Kota Tanjungpinang">
        <meta property="og:locale" content="id_ID">

        <!-- Additional Open Graph Metadata -->
        @if (!empty($tautan->logo_url))
            <meta property="og:image" content="{{ asset($tautan->logo_url) }}">
            <meta property="og:image:alt" content="Logo {{ $tautan->title ?? 'Tautan' }}">
            <meta property="og:image:type" content="image/jpeg">
            <meta property="og:image:width" content="400">
            <meta property="og:image:height" content="400">
            <meta property="og:image:secure_url" content="{{ asset($tautan->logo_url) }}">
        @else
            <meta property="og:image" content="{{ asset('images/default-logo.png') }}">
            <meta property="og:image:alt" content="Logo Taut Pinang">
            <meta property="og:image:type" content="image/png">
            <meta property="og:image:width" content="400">
            <meta property="og:image:height" content="400">
            <meta property="og:image:secure_url" content="{{ asset('images/default-logo.png') }}">
        @endif

        <!-- Twitter Card Metadata -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $tautan->title . ' | Taut Pinang' }}">
        <meta name="twitter:description"
            content="{{ $tautan->description ?? 'Halaman tautan resmi ' . ($tautan->title ?? 'Tautan') . ' - BPS Kota Tanjungpinang' }}">
        @if (!empty($tautan->logo_url))
            <meta name="twitter:image" content="{{ asset($tautan->logo_url) }}">
            <meta name="twitter:image:alt" content="Logo {{ $tautan->title ?? 'Tautan' }}">
        @else
            <meta name="twitter:image" content="{{ asset('images/default-logo.png') }}">
            <meta name="twitter:image:alt" content="Logo Taut Pinang">
        @endif
        <meta name="twitter:site" content="@bps_tanjungpinang">
        <meta name="twitter:creator" content="@bps_tanjungpinang">

        <!-- Additional Meta Tags -->
        <meta name="author" content="BPS Kota Tanjungpinang">
        <meta name="keywords" content="{{ $tautan->title ?? 'Tautan' }}, tautan, link, BPS, Tanjungpinang">
        <meta name="robots" content="index, follow">
        <meta property="fb:app_id" content="">
        <meta name="theme-color" content="#002366">

        <!-- Structured Data (JSON-LD) -->
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebPage",
            "name": "{{ $tautan->title ?? 'Tautan' }} | Taut Pinang",
            "description": "{{ $tautan->description ?? 'Halaman tautan resmi ' . ($tautan->title ?? 'Tautan') . ' - BPS Kota Tanjungpinang' }}",
            "url": "{{ url()->current() }}",
            "publisher": {
                "@type": "Organization",
                "name": "BPS Kota Tanjungpinang",
                "url": "https://tanjungpinangkota.bps.go.id",
                "logo": {
                    "@type": "ImageObject",
                    "url": "{{ asset('images/default-logo.png') }}",
                    "width": 400,
                    "height": 400
                }
            },
            "image": @if (!empty($tautan->logo_url)) {
                "@type": "ImageObject",
                "url": "{{ asset($tautan->logo_url) }}",
                "width": 400,
                "height": 400,
                "caption": "Logo {{ $tautan->title ?? 'Tautan' }}"
            } @else {
                "@type": "ImageObject",
                "url": "{{ asset('images/default-logo.png') }}",
                "width": 400,
                "height": 400,
                "caption": "Logo Taut Pinang"
            } @endif,
            "dateModified": "{{ date('Y-m-d') }}",
            "inLanguage": "id-ID",
            "isPartOf": {
                "@type": "WebSite",
                "name": "Taut Pinang",
                "url": "https://bps2172.github.io/TautPinang"
            }
        }
        </script>
        @php
            // Check if QR Code is enabled and has custom styling
            $qrSettings = $tautan->styles['qrcode'] ?? [];
            $ogImage = '';

            if (($qrSettings['enabled'] ?? false) && !empty($tautan->title)) {
                // Generate OG Image with QR Code styling
                $bgColor = $qrSettings['backgroundColor'] ?? '#ffffff';
                $darkColor = $qrSettings['darkColor'] ?? '#000000';

                // Convert RGBA to hex if needed
                $bgColorHex = $bgColor;
                $darkColorHex = $darkColor;

                if (strpos($bgColor, 'rgba') === 0) {
                    preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+)/', $bgColor, $matches);
                    if (count($matches) >= 4) {
                        $r = intval($matches[1]);
                        $g = intval($matches[2]);
                        $b = intval($matches[3]);
                        $bgColorHex = sprintf('#%02x%02x%02x', $r, $g, $b);
                    }
                }

                if (strpos($darkColor, 'rgba') === 0) {
                    preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+)/', $darkColor, $matches);
                    if (count($matches) >= 4) {
                        $r = intval($matches[1]);
                        $g = intval($matches[2]);
                        $b = intval($matches[3]);
                        $darkColorHex = sprintf('#%02x%02x%02x', $r, $g, $b);
                    }
                }

                // Remove # for API compatibility
                $bgColorApi = ltrim($bgColorHex, '#');
                $darkColorApi = ltrim($darkColorHex, '#');

                // Build QR Code URL for OG Image
                $qrSize = 400;
                $data = urlencode($tautan->title);
                $ogImage = "https://api.qrserver.com/v1/create-qr-code/?size={$qrSize}x{$qrSize}&data={$data}&color={$darkColorApi}&bgcolor={$bgColorApi}&margin=2";
            } elseif (!empty($tautan->logo_url)) {
                // Fallback to traditional logo-based OG Image
                $ogImage = asset($tautan->logo_url);
            }
        @endphp

    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="icon" href="{{ asset('img/favicon_public.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <!-- Dynamic Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

            @if (!empty($tautan->styles['background']))
                background: linear-gradient({{ $tautan->styles['background']['direction'] ?? 135 }}deg,
                        {{ $tautan->styles['background']['gradientStart'] ?? '#002366' }},
                        {{ $tautan->styles['background']['gradientEnd'] ?? '#1D4E5F' }});
            @else
                background: linear-gradient(135deg, #002366, #1D4E5F);
            @endif
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
        }

        @if (
            !empty($tautan->styles['animations']['backgroundAnimation']['type']) &&
                $tautan->styles['animations']['backgroundAnimation']['type'] !== 'none')
            @php $bgAnim =$tautan->styles['animations']['backgroundAnimation'];
            $speed =$bgAnim['speed'] ?? '5';
            $intensity =($bgAnim['intensity'] ?? 50) / 100;
            $color1 =$bgAnim['color1'] ?? '#002366';
            $color2 =$bgAnim['color2'] ?? '#FFD700';
            $color3 =$bgAnim['color3'] ?? '#1D4E5F';
            $direction =$bgAnim['direction'] ?? '0';
            $size =$bgAnim['size'] ?? '40';
        @endphp

        @if ($bgAnim['type'] === 'gradient')
            body::before,
            body::after {
                content: '';
                position: fixed;
                top: -10%;
                left: -10%;
                width: 120%;
                height: 120%;
                z-index: 2;
                pointer-events: none;
                will-change: background-position;
                transform: translateZ(0);
            }

            body::before {
                background: linear-gradient({{ $direction }}deg,
                        {{ $color1 }} 0%,
                        {{ $color2 }} 35%,
                        {{ $color3 }} 70%,
                        {{ $color1 }} 100%);
                background-size: 300% 300%;
                opacity: {{ $intensity * 0.4 }};
                animation: gradientFlow1 {{ $speed }}s ease-in-out infinite;
                filter: blur(15px);
            }

            body::after {
                background: linear-gradient({{ $direction + 90 }}deg,
                        {{ $color2 }} 0%,
                        {{ $color3 }} 50%,
                        {{ $color1 }} 100%);
                background-size: 300% 300%;
                opacity: {{ $intensity * 0.3 }};
                animation: gradientFlow2 {{ $speed * 1.5 }}s ease-in-out infinite reverse;
                animation-delay: {{ $speed * 0.2 }}s;
                filter: blur(12px);
            }

            @keyframes gradientFlow1 {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            @keyframes gradientFlow2 {

                0%,
                100% {
                    background-position: 0% 0%;
                }

                50% {
                    background-position: 100% 100%;
                }
            }
        @endif

        @if ($bgAnim['type'] === 'particles')
            @php $baseSize =$size * 0.8;
        @endphp

        body::before,
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
            will-change: transform;
            transform: translateZ(0);
        }

        body::before {
            background:
                radial-gradient(circle {{ $baseSize }}px at 20% 30%, {{ $color1 }} 0%, transparent 70%),
                radial-gradient(circle {{ $baseSize * 0.9 }}px at 80% 20%, {{ $color2 }} 0%, transparent 70%),
                radial-gradient(circle {{ $baseSize * 1.1 }}px at 70% 80%, {{ $color3 }} 0%, transparent 70%);
            opacity: {{ $intensity * 0.35 }};
            animation: particleFloat1 {{ $speed }}s ease-in-out infinite;
            filter: blur(1px);
        }

        body::after {
            background:
                radial-gradient(circle {{ $baseSize * 0.95 }}px at 50% 60%, {{ $color3 }} 0%, transparent 70%),
                radial-gradient(circle {{ $baseSize * 1.05 }}px at 10% 50%, {{ $color1 }} 0%, transparent 70%),
                radial-gradient(circle {{ $baseSize }}px at 90% 40%, {{ $color2 }} 0%, transparent 70%);
            opacity: {{ $intensity * 0.3 }};
            animation: particleFloat2 {{ $speed * 1.3 }}s ease-in-out infinite;
            animation-delay: {{ $speed * 0.3 }}s;
            filter: blur(1.5px);
        }

        @keyframes particleFloat1 {

            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            50% {
                transform: translate3d(15px, -20px, 0);
            }
        }

        @keyframes particleFloat2 {

            0%,
            100% {
                transform: translate3d(0, 0, 0);
            }

            50% {
                transform: translate3d(-12px, 18px, 0);
            }
        }
        @endif

        @if ($bgAnim['type'] === 'waves')
            body::before,
            body::after {
                content: '';
                position: fixed;
                top: -30%;
                left: -30%;
                width: 160%;
                height: 160%;
                z-index: 2;
                pointer-events: none;
                border-radius: 40%;
            }

            body::before {
                background:
                    radial-gradient(ellipse 70% 50%, {{ $color1 }} 0%, {{ $color2 }} 40%, transparent 80%),
                    radial-gradient(ellipse 50% 70%, {{ $color3 }} 20%, {{ $color1 }} 60%, transparent 90%);
                opacity: {{ $intensity }};
                animation: waveFlow1 {{ $speed }}s ease-in-out infinite;
                filter: blur(40px);
            }

            body::after {
                background:
                    radial-gradient(ellipse 60% 80%, {{ $color2 }} 10%, {{ $color3 }} 50%, transparent 85%),
                    radial-gradient(ellipse 80% 60%, {{ $color1 }} 30%, {{ $color2 }} 70%, transparent 95%);
                opacity: {{ $intensity * 0.8 }};
                animation: waveFlow2 {{ $speed * 1.8 }}s ease-in-out infinite;
                animation-delay: {{ $speed * 0.2 }}s;
                filter: blur(50px);
            }

            @keyframes waveFlow1 {

                0%,
                100% {
                    transform: rotate({{ $direction }}deg) scale(1) translateX(0%);
                }

                25% {
                    transform: rotate({{ $direction }}deg) scale(1.2) translateX(5%);
                }

                50% {
                    transform: rotate({{ $direction }}deg) scale(1.4) translateX(15%);
                }

                75% {
                    transform: rotate({{ $direction }}deg) scale(1.1) translateX(8%);
                }
            }

            @keyframes waveFlow2 {

                0%,
                100% {
                    transform: rotate({{ $direction + 120 }}deg) scale(1.3) translateY(0%);
                }

                33% {
                    transform: rotate({{ $direction + 120 }}deg) scale(0.9) translateY(-10%);
                }

                66% {
                    transform: rotate({{ $direction + 120 }}deg) scale(1.1) translateY(-20%);
                }
            }
        @endif
        @endif

        .container {
            @if (!empty($tautan->styles['container']))
                @if ($tautan->styles['container']['backgroundType'] === 'gradient')
                    background: linear-gradient({{ $tautan->styles['container']['backgroundGradientDirection'] ?? 135 }}deg,
                            {{ $tautan->styles['container']['backgroundGradientStart'] ?? '#FFFFFF' }},
                            {{ $tautan->styles['container']['backgroundGradientEnd'] ?? '#F8FAFC' }});
                @else
                    background: {{ $tautan->styles['container']['backgroundColor'] ?? '#FFFFFF' }};
                @endif
                backdrop-filter: blur({{ $tautan->styles['container']['backdropBlur'] ?? 20 }}px);
                border-radius: {{ $tautan->styles['container']['borderRadius'] ?? 24 }}px;
            @else
                background: #FFFFFF;
                backdrop-filter: blur(20px);
                border-radius: 24px;
            @endif
            padding: 30px 25px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 350px;
            width: 100%;
            animation: fadeInUp {{ $tautan->styles['animations']['fadeInDuration'] ?? 0.6 }}s ease-out;
            position: relative;
            overflow: hidden;
            z-index: 10;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: {{ $tautan->styles['container']['topGradientHeight'] ?? 4 }}px;
            background: linear-gradient(90deg,
                    {{ $tautan->styles['container']['topGradientStart'] ?? '#FFD700' }},
                    {{ $tautan->styles['container']['topGradientEnd'] ?? '#002366' }});
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 20px auto 20px;
            border-radius: {{ $tautan->styles['logo']['borderRadius'] ?? 50 }}px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);

            @if (!empty($tautan->styles['logo']))
                @if ($tautan->styles['logo']['backgroundType'] === 'gradient')
                    background: linear-gradient({{ $tautan->styles['logo']['backgroundGradientDirection'] ?? 135 }}deg,
                            {{ $tautan->styles['logo']['backgroundGradientStart'] ?? '#FFFFFF' }},
                            {{ $tautan->styles['logo']['backgroundGradientEnd'] ?? '#F8FASC' }});
                @else
                    background: {{ $tautan->styles['logo']['backgroundColor'] ?? '#FFFFFF' }};
                @endif
                border: {{ $tautan->styles['logo']['borderWidth'] ?? 3 }}px {{ $tautan->styles['logo']['borderStyle'] ?? 'solid' }} {{ $tautan->styles['logo']['borderColor'] ?? '#FFD700' }};
            @else
                background: #FFFFFF;
                border: 3px solid #FFD700;
                border-radius: 50px;
            @endif
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .title {
            font-size: {{ $tautan->styles['title']['fontSize'] ?? 26 }}px;
            font-weight: {{ $tautan->styles['title']['fontWeight'] ?? 700 }};
            color: {{ $tautan->styles['title']['color'] ?? '#002366' }};
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .description {
            color: {{ $tautan->styles['description']['color'] ?? '#666666' }};
            font-size: {{ $tautan->styles['description']['fontSize'] ?? 15 }}px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .links {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .link-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 20px;

            @php
                // Get button styles with proper fallbacks
                $buttonBgColor = $tautan->styles['button']['backgroundColor'] ?? '#FFFFFF';
                $buttonTextColor = $tautan->styles['button']['color'] ?? '#002366';
                $buttonBorderRadius = $tautan->styles['button']['borderRadius'] ?? 12;
                $buttonBorderWidth = $tautan->styles['button']['borderWidth'] ?? 2;
                $buttonBorderStyle = $tautan->styles['button']['borderStyle'] ?? 'solid';
                $buttonBorderColor = $tautan->styles['button']['borderColor'] ?? '#EAEAEA';
                $hoverDuration = $tautan->styles['animations']['hoverDuration'] ?? 0.3;
            @endphp

            background: {{ $buttonBgColor }};
            color: {{ $buttonTextColor }};
            border-radius: {{ $buttonBorderRadius }}px;
            border: {{ $buttonBorderWidth }}px {{ $buttonBorderStyle }} {{ $buttonBorderColor }};

            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all {{ $hoverDuration }}s ease;
            position: relative;
            overflow: hidden;
        }

        .link-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        .link-button:hover {
            transform: translateY(-2px);

            @php
                // Get button hover styles with proper fallbacks
                $hoverBgStart = $tautan->styles['buttonHover']['backgroundStart'] ?? '#FFD700';
                $hoverBgEnd = $tautan->styles['buttonHover']['backgroundEnd'] ?? '#FFFFFF';
                $hoverTextColor = $tautan->styles['buttonHover']['color'] ?? '#002366';
                $hoverBorderWidth = $tautan->styles['buttonHover']['borderWidth'] ?? 2;
                $hoverBorderStyle = $tautan->styles['buttonHover']['borderStyle'] ?? 'solid';
                $hoverBorderColor = $tautan->styles['buttonHover']['borderColor'] ?? '#FFD700';
                $hoverGlowBlur = $tautan->styles['buttonHover']['glowBlur'] ?? 30;
                $hoverGlowColor = $tautan->styles['buttonHover']['glowColor'] ?? '#FFD700';
            @endphp

            background: linear-gradient(135deg, {{ $hoverBgStart }}, {{ $hoverBgEnd }});
            color: {{ $hoverTextColor }};
            border: {{ $hoverBorderWidth }}px {{ $hoverBorderStyle }} {{ $hoverBorderColor }};
            box-shadow: 0 0 {{ $hoverGlowBlur }}px {{ $hoverGlowColor }};
        }

        .footer {
            margin-top: {{ $tautan->styles['footer']['marginTop'] ?? 30 }}px;
            padding-top: {{ $tautan->styles['footer']['paddingTop'] ?? 20 }}px;
            border-top: 1px solid {{ $tautan->styles['footer']['borderColor'] ?? '#EEEEEE' }};
            color: {{ $tautan->styles['footer']['color'] ?? '#999999' }};
            font-size: {{ $tautan->styles['footer']['fontSize'] ?? 12 }}px;
        }

        .copy-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;

            @if (!empty($tautan->styles['container']))
                @if ($tautan->styles['container']['backgroundType'] === 'gradient')
                    background: {{ $tautan->styles['container']['backgroundGradientStart'] ?? '#3B82F6' }};
                @else
                    background: {{ $tautan->styles['container']['backgroundColor'] ?? '#3B82F6' }};
                @endif
                color: {{ $tautan->styles['title']['color'] ?? '#FFFFFF' }};
            @else
                background: #3B82F6;
                color: #FFFFFF;
            @endif
            border: none;
            padding: 12px;
            border-radius: 50%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s ease;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .copy-button:hover {
            transform: scale(1.1);

            @if (!empty($tautan->styles['buttonHover']['glowColor']))
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25), 0 0 {{ $tautan->styles['buttonHover']['glowBlur'] ?? 20 }}px {{ $tautan->styles['buttonHover']['glowColor'] }};
            @else
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25), 0 0 20px rgba(59, 130, 246, 0.5);
            @endif
        }

        @media (max-width: 768px) {
            #qr-code-container {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }

            .title {
                font-size: calc({{ $tautan->styles['title']['fontSize'] ?? 26 }}px * 0.85);
            }

            .link-button {
                padding: 14px 18px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <!-- Copy Link Button -->
    <button class="copy-button" onclick="copyCurrentUrl()">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
            </path>
        </svg>
    </button>

    <div class="container">
        @if (!empty($tautan->logo_url))
            <div class="logo">
                <img src="{{ asset($tautan->logo_url) }}" alt="{{ $tautan->title ?? 'Logo' }}"
                    onerror="this.style.display='none'">
            </div>
        @endif

        <h1 class="title">{{ $tautan->title ?? 'Judul Tautan' }}</h1>

        @if (!empty($tautan->description))
            <p class="description">{{ $tautan->description }}</p>
        @endif

        <div class="links">
            @if (!empty($tautan->links) && is_array($tautan->links))
                @foreach ($tautan->links as $link)
                    @php
                        // Cek active state - default true untuk backward compatibility
                        $isActive = $link['active'] ?? true;
                    @endphp
                    @if (!empty($link['judul']) && !empty($link['url']) && $isActive)
                        @php
                            // Gunakan logika yang SAMA seperti EditTautan->buildHtml()
                            $enableCustomStyling = $link['enableCustomStyling'] ?? false;

                            // Icon support
                            $icon = $link['icon'] ?? '';
                            $iconHtml = '';
                            if (!empty($icon)) {
                                $iconHtml = '<i class="' . $icon . ' link-icon"></i> ';
                            }

                            if ($enableCustomStyling) {
                                // Custom styling ENABLED - gunakan warna dari link ini
                                $linkBgColor = $link['backgroundColor'] ?? $tautan->styles['button']['backgroundColor'];
                                $linkTextColor = $link['textColor'] ?? $tautan->styles['button']['color'];
                                // Build inline style dengan warna dari link ini
                                $inlineStyle = "background: {$linkBgColor}; color: {$linkTextColor};";
                            } else {
                                // Custom styling DISABLED - gunakan global style dari styles['button']
                                // JANGAN gunakan inline style, biarkan CSS global yang handle
                                $inlineStyle = '';
                            }
                        @endphp
                        <a href="{{ $link['url'] }}"
                           class="link-button"
                           target="_blank"
                           rel="noopener noreferrer"
                           @if (!empty($inlineStyle))
                               style="{{ $inlineStyle }}"
                           @endif
                        >
                            {!! $iconHtml !!}{{ $link['judul'] }}
                        </a>
                    @endif
                @endforeach
            @endif
        </div>

        @if (!empty($tautan->footer_text_1) || !empty($tautan->footer_text_2))
            <div class="footer">
                @if (!empty($tautan->footer_text_1))
                    <p>{{ $tautan->footer_text_1 }}</p>
                @endif
                @if (!empty($tautan->footer_text_2))
                    <p>{{ $tautan->footer_text_2 }}</p>
                @endif
            </div>
        @endif
    </div>

    <!-- QR Code Script -->
    @if (!empty($tautan->styles['qrcode']['enabled']) && $tautan->styles['qrcode']['enabled'])
        <script>
            console.log('✅ QR Code enabled - creating QR');

            // Create QR Code immediately when page loads
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🔧 DOM loaded, creating QR Code');

                // QR Settings from database
                const qrSettings = {
                    position: '{{ $tautan->styles['qrcode']['position'] ?? 'bottom-right' }}',
                    size: parseInt('{{ $tautan->styles['qrcode']['size'] ?? 200 }}'),
                    backgroundColor: '{{ $tautan->styles['qrcode']['backgroundColor'] ?? '#ffffff' }}',
                    borderColor: '{{ $tautan->styles['qrcode']['borderColor'] ?? '#ffffff' }}',
                    darkColor: '{{ $tautan->styles['qrcode']['darkColor'] ?? '#000000' }}',
                    borderRadius: parseInt('{{ $tautan->styles['qrcode']['borderRadius'] ?? 12 }}'),
                    padding: parseInt('{{ $tautan->styles['qrcode']['padding'] ?? 10 }}'),
                    tooltipText: '{{ $tautan->styles['qrcode']['tooltipText'] ?? 'Scan QR untuk buka halaman ini' }}',
                    showOnMobile: {{ $tautan->styles['qrcode']['showOnMobile'] ? 'true' : 'false' }}
                };

                console.log('🎯 QR Settings from DB (FULL 3 COLORS):', qrSettings);
                console.log('🎨 QR Color Mapping:');
                console.log('  - Background Container (wrapper):', qrSettings.backgroundColor);
                console.log('  - Border Container (edge):', qrSettings.borderColor);
                console.log('  - Pattern QR (dots):', qrSettings.darkColor);
                console.log('  - API bgcolor:', qrSettings.backgroundColor);

                // Remove existing QR if any
                const existingQR = document.getElementById('qr-code-container');
                if (existingQR) {
                    existingQR.remove();
                }

                // Create QR container
                const qrContainer = document.createElement('div');
                qrContainer.id = 'qr-code-container';

                // Set position
                let positionStyle = '';
                switch (qrSettings.position) {
                    case 'top-left':
                        positionStyle = 'top: 80px; left: 20px;';
                        break;
                    case 'top-right':
                        positionStyle = 'top: 80px; right: 20px;';
                        break;
                    case 'bottom-left':
                        positionStyle = 'bottom: 20px; left: 20px;';
                        break;
                    default:
                        positionStyle = 'bottom: 20px; right: 20px;';
                }

                // Apply styles with exact database colors
                // ✅ PERBAIKAN: backgroundColor untuk QR image background, borderColor untuk container border
                qrContainer.style.cssText = `
                position: fixed;
                ${positionStyle}
                background: ${qrSettings.backgroundColor};
                padding: ${qrSettings.padding}px;
                border-radius: ${qrSettings.borderRadius}px;
                z-index: 1000;
                border: 2px solid ${qrSettings.borderColor};
                opacity: 1;
                cursor: pointer;
                user-select: none;
                display: flex;
                align-items: center;
                justify-content: center;
            `;

                // Mobile visibility
                if (!qrSettings.showOnMobile) {
                    if (window.innerWidth <= 768) {
                        qrContainer.style.display = 'none';
                    }
                    window.addEventListener('resize', function() {
                        qrContainer.style.display = window.innerWidth <= 768 ? 'none' : 'flex';
                    });
                }

                // Current URL for QR Code
                const currentUrl = window.location.href;
                console.log('🌐 QR URL:', currentUrl);

                // Create QR Code using API (more reliable)
                const qrImg = document.createElement('img');
                qrImg.alt = 'QR Code';
                qrImg.style.cssText = `
                width: ${qrSettings.size}px;
                height: ${qrSettings.size}px;
                display: block;
                border-radius: 4px;
                background: ${qrSettings.backgroundColor};
            `;

                // Convert RGBA to HEX for API
                function rgbaToHex(rgba) {
                    if (rgba.includes('rgba')) {
                        const match = rgba.match(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/);
                        if (match) {
                            const r = parseInt(match[1]);
                            const g = parseInt(match[2]);
                            const b = parseInt(match[3]);
                            return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
                        }
                    }
                    return rgba.includes('#') ? rgba.replace('#', '') : rgba;
                }

                const backgroundColorHex = rgbaToHex(qrSettings.backgroundColor);
                const borderColorHex = rgbaToHex(qrSettings.borderColor);
                const darkColorHex = rgbaToHex(qrSettings.darkColor);

                console.log('🎨 3 Colors - Background:', backgroundColorHex, 'Border:', borderColorHex, 'Dark:',
                    darkColorHex);

                // Use qrserver API with exact colors from database
                // backgroundColor is used as bgcolor, borderColor for container border
                qrImg.src =
                    `https://api.qrserver.com/v1/create-qr-code/?size=${qrSettings.size}x${qrSettings.size}&data=${encodeURIComponent(currentUrl)}&color=${darkColorHex.replace('#', '')}&bgcolor=${backgroundColorHex.replace('#', '')}&margin=1&t=${Date.now()}`;

                qrImg.onload = function() {
                    console.log('✅ QR Code loaded successfully');
                    qrContainer.appendChild(qrImg);
                    document.body.appendChild(qrContainer);

                    // Add hover tooltip
                    const tooltip = document.createElement('div');
                    tooltip.style.cssText = `
                        position: absolute;
                        bottom: 110%;
                        right: 0;
                        background: #000000;
                        color: white;
                        padding: 6px 10px;
                        border-radius: 6px;
                        font-size: 12px;
                        white-space: nowrap;
                        opacity: 0;
                        transform: translateY(10px);
                        transition: all 0.3s ease;
                        pointer-events: none;
                        z-index: 1001;
                    `;
                    tooltip.textContent = qrSettings.tooltipText;
                    qrContainer.appendChild(tooltip);

                    // Add hover events for tooltip
                    qrContainer.addEventListener('mouseenter', function() {
                        tooltip.style.opacity = '1';
                        tooltip.style.transform = 'translateY(-5px)';
                    });

                    qrContainer.addEventListener('mouseleave', function() {
                        tooltip.style.opacity = '0';
                        tooltip.style.transform = 'translateY(10px)';
                    });

                    console.log('🎉 QR Code with custom colors created successfully!');
                };

                qrImg.onerror = function() {
                    console.error('❌ QR Code failed to load, trying fallback...');
                    // Fallback to Google Charts API
                    qrImg.src =
                        `https://chart.googleapis.com/chart?chs=${qrSettings.size}x${qrSettings.size}&cht=qr&chl=${encodeURIComponent(currentUrl)}&chof=png`;
                };

                qrImg.onerror = function() {
                    console.error('❌ Both APIs failed, creating simple QR');
                    // Create a simple placeholder
                    qrImg.style.display = 'none';
                    const placeholder = document.createElement('div');
                    placeholder.style.cssText = `
                    width: ${qrSettings.size}px;
                    height: ${qrSettings.size}px;
                    background: white;
                    border: 2px solid black;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 12px;
                    text-align: center;
                    color: black;
                `;
                    placeholder.innerHTML = 'QR<br>Code';
                    qrContainer.appendChild(placeholder);
                    document.body.appendChild(qrContainer);
                };

                console.log('🚀 QR Code generation initiated');
            });
        </script>
    @else
        <script>
            console.log('❌ QR Code disabled in settings');
        </script>
    @endif

    <!-- CSS Hover Effects - Menggunakan data dari database -->
    <script>
        // Hapus JavaScript hover override - biarkan CSS hover berfungsi dengan data dari database
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ CSS Hover Effects aktif - menggunakan data dari database');
            console.log('🎨 Button Hover Settings:', {
                backgroundStart: '{{ $tautan->styles["buttonHover"]["backgroundStart"] ?? "#FFD700" }}',
                backgroundEnd: '{{ $tautan->styles["buttonHover"]["backgroundEnd"] ?? "#FFFFFF" }}',
                color: '{{ $tautan->styles["buttonHover"]["color"] ?? "#002366" }}',
                borderColor: '{{ $tautan->styles["buttonHover"]["borderColor"] ?? "#FFD700" }}',
                glowColor: '{{ $tautan->styles["buttonHover"]["glowColor"] ?? "#FFD700" }}',
                glowBlur: '{{ $tautan->styles["buttonHover"]["glowBlur"] ?? 30 }}px'
            });
        });
    </script>

    <!-- Copy Button JavaScript -->
    <script>
        function copyCurrentUrl() {
            const url = window.location.href;

            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    showCopySuccess();
                }).catch(() => {
                    fallbackCopy(url);
                });
            } else {
                fallbackCopy(url);
            }
        }

        function fallbackCopy(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showCopySuccess();
        }

        function showCopySuccess() {
            const btn = document.querySelector('.copy-button');
            const originalContent = btn.innerHTML;

            btn.innerHTML =
                '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
            btn.style.background = '#10B981';

            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.style.background = '';
            }, 2000);
        }
    </script>
</body>

</html>
