<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription ?? 'Fight Zone Fitness - train with purpose.' }}">
    <title>{{ $title ?? 'Fight Zone Fitness' }}</title>
    <style>
        :root { --ink: #141210; --muted: #706a64; --line: #e6e0d9; --paper: #fbfaf8; --orange: #f26b3a; --orange-dark: #cb4d22; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; min-width: 320px; color: var(--ink); background: var(--paper); font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; line-height: 1.6; }
        a { color: inherit; }
        .site-header { height: 76px; display: flex; align-items: center; justify-content: space-between; max-width: 1180px; margin: 0 auto; padding: 0 28px; }
        .brand { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; font-size: 15px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; }
        .brand-mark { display: grid; width: 34px; height: 34px; place-items: center; background: var(--ink); color: white; font-size: 17px; font-weight: 900; transform: skew(-8deg); }
        .brand-mark span { transform: skew(8deg); }
        .site-nav { display: flex; align-items: center; gap: 24px; font-size: 14px; font-weight: 650; }
        .site-nav a { color: #5b5650; text-decoration: none; }
        .site-nav a:hover, .site-nav a[aria-current="page"] { color: var(--orange-dark); }
        .page-shell { max-width: 1180px; margin: 0 auto; padding: 42px 28px 80px; }
        .eyebrow { margin: 0 0 14px; color: var(--orange-dark); font-size: 12px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .page-title { max-width: 760px; margin: 0; font-size: clamp(38px, 6vw, 66px); line-height: 1.02; letter-spacing: -.045em; }
        .lede { max-width: 670px; margin: 20px 0 0; color: var(--muted); font-size: 18px; }
        .site-footer { border-top: 1px solid var(--line); }
        .footer-inner { display: flex; justify-content: space-between; gap: 24px; max-width: 1180px; margin: 0 auto; padding: 25px 28px; color: var(--muted); font-size: 13px; }
        .footer-links { display: flex; gap: 20px; }
        .footer-links a { text-decoration: none; }
        .footer-links a:hover { color: var(--orange-dark); }
        @media (max-width: 600px) { .site-header, .page-shell, .footer-inner { padding-left: 20px; padding-right: 20px; } .site-nav { gap: 16px; font-size: 13px; } .brand { font-size: 13px; } .footer-inner { align-items: flex-start; flex-direction: column; gap: 10px; } }
    </style>
    @stack('styles')
</head>
<body>
    <header class="site-header">
        <a class="brand" href="/" aria-label="Fight Zone Fitness home"><span class="brand-mark"><span>FZ</span></span>Fight Zone</a>
        <nav class="site-nav" aria-label="Public navigation">
            <a href="{{ route('privacy-policies') }}" @if (request()->routeIs('privacy-policies')) aria-current="page" @endif>Privacy</a>
            <a href="{{ route('contact-us') }}" @if (request()->routeIs('contact-us')) aria-current="page" @endif>Contact</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <span>&copy; {{ now()->year }} Fight Zone Fitness. Built to train with purpose.</span>
            <div class="footer-links">
                <a href="{{ route('privacy-policies') }}">Privacy Policy</a>
                <a href="{{ route('contact-us') }}">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>
