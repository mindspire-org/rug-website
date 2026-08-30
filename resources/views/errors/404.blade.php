<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | Costikyan Custom Carpet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lusitana:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #111111;
            color: #fff;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* ══════════════════════════════════════════
           ANIMATED BACKGROUND PATTERN
        ══════════════════════════════════════════ */
        .bg-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.03;
            background-image:
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(237,184,74,0.3) 35px, rgba(237,184,74,0.3) 36px),
                repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(232,101,26,0.2) 35px, rgba(232,101,26,0.2) 36px);
            animation: patternDrift 30s linear infinite;
        }

        @keyframes patternDrift {
            0% { background-position: 0 0, 0 0; }
            100% { background-position: 50px 50px, -50px 50px; }
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: orbFloat 8s ease-in-out infinite alternate;
        }
        .orb-1 { width: 400px; height: 400px; background: #E8651A; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 300px; height: 300px; background: #EDB84A; bottom: -50px; right: -50px; animation-delay: -3s; }
        .orb-3 { width: 250px; height: 250px; background: #8B6914; top: 40%; right: 20%; animation-delay: -5s; opacity: 0.08; }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, -30px) scale(1.1); }
        }

        /* ══════════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════════ */
        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 40px 24px;
            max-width: 640px;
        }

        /* ── Rug SVG Illustration ── */
        .rug-wrap {
            width: 220px;
            height: 220px;
            margin: 0 auto 40px;
            position: relative;
        }

        .rug-svg {
            width: 100%;
            height: 100%;
            animation: rugFloat 4s ease-in-out infinite;
        }

        @keyframes rugFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(1deg); }
        }

        .rug-fringe {
            animation: fringeWave 2s ease-in-out infinite;
            transform-origin: center;
        }

        @keyframes fringeWave {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.92); }
        }

        /* ── 404 Typography ── */
        .code {
            font-family: 'Lusitana', serif;
            font-size: clamp(80px, 18vw, 160px);
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(135deg, #fff 0%, #EDB84A 50%, #E8651A 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            margin-bottom: 8px;
            animation: codePulse 3s ease-in-out infinite;
        }

        @keyframes codePulse {
            0%, 100% { opacity: 1; filter: brightness(1); }
            50% { opacity: 0.85; filter: brightness(1.1); }
        }

        .divider {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #EDB84A, transparent);
            margin: 24px auto;
            animation: dividerExpand 2s ease-out infinite alternate;
        }

        @keyframes dividerExpand {
            0% { width: 40px; opacity: 0.5; }
            100% { width: 80px; opacity: 1; }
        }

        .title {
            font-family: 'Lusitana', serif;
            font-size: clamp(22px, 4vw, 32px);
            font-weight: 700;
            color: #fff;
            margin-bottom: 16px;
            line-height: 1.3;
        }

        .subtitle {
            font-size: 15px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 420px;
            margin: 0 auto 40px;
            font-weight: 300;
        }

        /* ── CTA Buttons ── */
        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 32px;
            border-radius: 3px;
            font-family: 'Lusitana', serif;
            font-size: 15px;
            font-weight: 400;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #E8651A;
            color: #fff;
            box-shadow: 0 4px 20px rgba(232,101,26,0.3);
        }
        .btn-primary:hover {
            background: #d55a15;
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232,101,26,0.4);
        }

        .btn-ghost {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-ghost:hover {
            border-color: rgba(255,255,255,0.5);
            background: rgba(255,255,255,0.05);
        }

        /* ── Small particles ── */
        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: #EDB84A;
            border-radius: 50%;
            opacity: 0.4;
            animation: particleRise 6s linear infinite;
        }

        @keyframes particleRise {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 0.4; }
            90% { opacity: 0.4; }
            100% { transform: translateY(-20px) scale(1); opacity: 0; }
        }

        /* ── Logo link ── */
        .logo-link {
            position: absolute;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            text-decoration: none;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        .logo-link:hover { opacity: 1; }
        .logo-text {
            font-family: 'Lusitana', serif;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }
        .logo-accent { color: #E8651A; }
    </style>
</head>
<body>

    {{-- Logo --}}
    <a href="{{ route('home') }}" class="logo-link">
        <span class="logo-text">COSTI<span class="logo-accent">K</span>YAN</span>
    </a>

    {{-- Background Effects --}}
    <div class="bg-pattern"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- Floating Particles --}}
    @for ($i = 0; $i < 12; $i++)
    <div class="particle" style="left: {{ rand(5, 95) }}%; animation-delay: {{ $i * 0.5 }}s; animation-duration: {{ rand(5, 9) }}s;"></div>
    @endfor

    {{-- Main Content --}}
    <div class="container">

        {{-- Rug SVG Illustration --}}
        <div class="rug-wrap">
            <svg class="rug-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                {{-- Shadow --}}
                <ellipse cx="100" cy="185" rx="75" ry="10" fill="rgba(0,0,0,0.3)" filter="blur(8px)"/>

                {{-- Bottom fringe --}}
                <g class="rug-fringe" style="animation-delay: 0.1s">
                    @for ($i = 0; $i < 30; $i++)
                    <line x1="{{ 32 + $i * 2.4 }}" y1="155" x2="{{ 32 + $i * 2.4 + (rand(-2,2)/10) }}" y2="{{ 168 + rand(0,8) }}" stroke="rgba(237,184,74,0.6)" stroke-width="0.8" stroke-linecap="round"/>
                    @endfor
                </g>

                {{-- Top fringe --}}
                <g class="rug-fringe" style="animation-delay: 0.2s">
                    @for ($i = 0; $i < 30; $i++)
                    <line x1="{{ 32 + $i * 2.4 }}" y1="45" x2="{{ 32 + $i * 2.4 + (rand(-2,2)/10) }}" y2="{{ 32 - rand(0,8) }}" stroke="rgba(237,184,74,0.6)" stroke-width="0.8" stroke-linecap="round"/>
                    @endfor
                </g>

                {{-- Main rug body --}}
                <rect x="35" y="45" width="130" height="110" rx="2" fill="url(#rugBody)" stroke="rgba(237,184,74,0.2)" stroke-width="1"/>

                {{-- Inner border --}}
                <rect x="45" y="55" width="110" height="90" rx="1" stroke="rgba(237,184,74,0.15)" stroke-width="0.5" fill="none"/>

                {{-- "404" woven into the rug --}}
                <text x="100" y="115" text-anchor="middle" font-family="Lusitana, serif" font-size="38" font-weight="700" fill="rgba(255,255,255,0.12)" letter-spacing="4">404</text>

                {{-- Decorative woven lines --}}
                @for ($i = 0; $i < 6; $i++)
                <line x1="50" y1="{{ 62 + $i * 12 }}" x2="150" y2="{{ 62 + $i * 12 }}" stroke="rgba(237,184,74,0.06)" stroke-width="0.5"/>
                @endfor

                {{-- Small pattern diamonds --}}
                @for ($row = 0; $row < 3; $row++)
                    @for ($col = 0; $col < 4; $col++)
                    <path d="M {{ 55 + $col * 25 }} {{ 70 + $row * 25 }} l 6 -6 l 6 6 l -6 6 z" fill="rgba(232,101,26,0.1)" stroke="rgba(232,101,26,0.2)" stroke-width="0.3"/>
                    @endfor
                @endfor

                {{-- Gradient definitions --}}
                <defs>
                    <linearGradient id="rugBody" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#1a1a1a"/>
                        <stop offset="100%" stop-color="#141414"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="code">404</div>
        <div class="divider"></div>
        <h1 class="title">This rug has been rolled up</h1>
        <p class="subtitle">
            The page you're looking for has moved, been renamed, or never existed. 
            Perhaps it was a custom design that hasn't been woven yet.
        </p>

        <div class="actions">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9z"/>
                    <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="2" points="9 22 9 12 15 12 15 22"/>
                </svg>
                Back to Home
            </a>
            <a href="{{ route('shop.index') }}" class="btn btn-ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M16 10a4 4 0 0 1-8 0"/>
                </svg>
                Browse Collection
            </a>
        </div>
    </div>

</body>
</html>
