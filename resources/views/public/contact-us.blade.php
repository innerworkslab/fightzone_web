@extends('layouts.public')

@section('content')
    <section class="contact-hero">
        <img src="{{ asset('images/fight-zone-training-contact.png') }}" alt="Fight Zone athletes training with focus mitts">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <p class="eyebrow">Fight Zone Fitness</p>
            <h1>Let&rsquo;s keep you moving.</h1>
            <p>Questions, feedback, or technical support - our team is here to help.</p>
        </div>
    </section>

    <section class="page-shell contact-content">
        <div class="contact-intro">
            <p class="eyebrow">Contact Us</p>
            <h2>How can we help?</h2>
            <p>Reach out to the Innerworks team about Fight Zone Fitness. We&rsquo;ll make sure your question reaches the right person.</p>
        </div>

        <div class="contact-cards">
            <a class="contact-card primary-card" href="mailto:labs.innerworks@gmail.com?subject=Fight%20Zone%20Fitness%20Support">
                <span class="card-label">Email Support</span>
                <strong>labs.innerworks@gmail.com</strong>
                <span class="card-action">Send us a message <b aria-hidden="true">&rarr;</b></span>
            </a>
            <div class="contact-card">
                <span class="card-label">Business Hours</span>
                <strong>Monday - Friday</strong>
                <span>9:00 AM - 5:00 PM MMT</span>
            </div>
            <div class="contact-card">
                <span class="card-label">Typical Response</span>
                <strong>Within 24 to 48 hours</strong>
                <span>We&rsquo;ll reply as soon as we can.</span>
            </div>
        </div>

        <div class="support-note">
            <span class="note-mark">FZ</span>
            <p>For the quickest help, include the phone number on your Fight Zone account and a short description of what happened.</p>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .contact-hero { position: relative; min-height: 460px; overflow: hidden; color: white; background: #201c19; }
    .contact-hero img { position: absolute; width: 100%; height: 100%; object-fit: cover; object-position: center; }
    .hero-overlay { position: absolute; inset: 0; background: linear-gradient(90deg, rgba(15, 13, 11, .91) 0%, rgba(15, 13, 11, .67) 39%, rgba(15, 13, 11, .17) 80%); }
    .hero-content { position: relative; max-width: 1180px; margin: 0 auto; padding: 112px 28px 90px; }
    .hero-content .eyebrow { color: #ff8a5e; }
    .hero-content h1 { max-width: 590px; margin: 0; font-size: clamp(44px, 6vw, 76px); line-height: .98; letter-spacing: -.055em; }
    .hero-content p:not(.eyebrow) { max-width: 480px; margin: 23px 0 0; color: #eee9e4; font-size: 18px; }
    .contact-content { padding-top: 82px; }
    .contact-intro { display: grid; grid-template-columns: minmax(180px, .7fr) minmax(0, 1.5fr); gap: 20px 64px; align-items: start; max-width: 860px; }
    .contact-intro .eyebrow { grid-row: span 2; padding-top: 9px; }
    .contact-intro h2 { margin: 0; font-size: 37px; line-height: 1.05; letter-spacing: -.04em; }
    .contact-intro p:not(.eyebrow) { grid-column: 2; margin: 0; color: var(--muted); font-size: 17px; }
    .contact-cards { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 14px; margin-top: 68px; }
    .contact-card { display: flex; min-height: 192px; flex-direction: column; justify-content: space-between; padding: 25px; border: 1px solid var(--line); border-radius: 8px; color: var(--muted); font-size: 14px; }
    .contact-card strong { display: block; color: var(--ink); font-size: 18px; line-height: 1.25; letter-spacing: -.015em; }
    .card-label { color: #746e67; font-size: 12px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
    .primary-card { border-color: var(--orange); background: var(--orange); color: #fff5f1; text-decoration: none; transition: background .2s ease, transform .2s ease; }
    .primary-card:hover { background: var(--orange-dark); transform: translateY(-2px); }
    .primary-card strong, .primary-card .card-label { color: white; }
    .card-action { font-weight: 750; } .card-action b { margin-left: 4px; font-size: 18px; }
    .support-note { display: flex; align-items: center; gap: 18px; max-width: 790px; margin-top: 70px; padding-top: 28px; border-top: 1px solid var(--line); color: var(--muted); }
    .support-note p { margin: 0; font-size: 14px; }
    .note-mark { display: grid; width: 40px; height: 40px; flex: 0 0 auto; place-items: center; border: 1px solid var(--ink); font-size: 11px; font-weight: 900; letter-spacing: .06em; }
    @media (max-width: 820px) { .contact-cards { grid-template-columns: 1fr; } .contact-card { min-height: 150px; } }
    @media (max-width: 600px) { .contact-hero { min-height: 410px; } .hero-content { padding: 90px 20px 70px; } .hero-overlay { background: linear-gradient(90deg, rgba(15, 13, 11, .88), rgba(15, 13, 11, .42)); } .contact-content { padding-top: 60px; } .contact-intro { grid-template-columns: 1fr; gap: 14px; } .contact-intro .eyebrow { grid-row: auto; padding-top: 0; } .contact-intro p:not(.eyebrow) { grid-column: auto; } .contact-intro h2 { font-size: 32px; } .contact-cards { margin-top: 48px; } .support-note { margin-top: 48px; align-items: flex-start; } }
</style>
@endpush
