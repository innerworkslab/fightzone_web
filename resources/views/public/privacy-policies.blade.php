@extends('layouts.public')

@section('content')
    <section class="page-shell policy-page">
        <p class="eyebrow">Fight Zone Fitness</p>
        <h1 class="page-title">Privacy Policy</h1>
        <p class="lede">How Fight Zone handles the information that helps us deliver your training experience.</p>
        <p class="updated">Last updated: August 27, 2026</p>

        <div class="policy-layout">
            <aside class="policy-index" aria-label="Privacy policy sections">
                <span>On this page</span>
                <a href="#information">Information we collect</a>
                <a href="#use">How we use information</a>
                <a href="#notifications">Push notifications</a>
                <a href="#video">Video services</a>
                <a href="#disclosure">How we disclose information</a>
                <a href="#security">Data security</a>
            </aside>

            <article class="policy-content">
                <p>Fight Zone Gym (&ldquo;Fight Zone,&rdquo; &ldquo;we,&rdquo; &ldquo;our,&rdquo; or &ldquo;us&rdquo;) operates the Fight Zone mobile application (the &ldquo;App&rdquo;).</p>
                <p>This Privacy Policy explains what information we collect, how we use and disclose it, how long we retain it, and the choices available to you when you use the App.</p>

                <section id="information">
                    <h2>1. Information We Collect</h2>
                    <h3>Account information</h3>
                    <p>When you create or manage an account, we collect:</p>
                    <ul><li>Your name</li><li>Phone number</li><li>Password and authentication credentials</li><li>Account identifier</li><li>Account creation date</li><li>Account verification and status information</li><li>Profile image, if this feature is available and you choose to provide one</li></ul>
                    <p>Passwords are used only for authentication and account security. You should not share your password with anyone.</p>

                    <h3>Fitness and training information</h3>
                    <p>To personalize your Fight Zone experience and manage your training profile, we collect information such as:</p>
                    <ul><li>Gender</li><li>Body weight</li><li>Boxing stance</li><li>Boxing experience level</li><li>Jump-rope experience level</li><li>Fitness experience level</li></ul>
                    <p>This information supports fitness and training functionality. The App does not provide medical diagnosis or treatment.</p>

                    <h3>Membership and activity information</h3>
                    <p>When you use the App or Fight Zone Gym services, we may collect and maintain:</p>
                    <ul><li>Assigned classes and class-access periods</li><li>Course and lesson progress</li><li>Completed instructional videos</li><li>Course-level access requests and notes you submit</li><li>Gym membership and package information</li><li>Point balance</li><li>Purchase and transaction history for physical gym services</li><li>Notification history and read status</li><li>Gym check-in records</li><li>QR-code check-in information</li></ul>
                    <p>Payments for gym memberships and other physical services are completed outside the App. The App does not collect payment-card numbers, banking credentials, or Apple App Store or Google Play payment information.</p>

                    <h3>Device and technical information</h3>
                    <p>The App and our service providers may automatically receive:</p>
                    <ul><li>Firebase installation and push-notification identifiers</li><li>Apple Push Notification Service tokens</li><li>Device type, operating system, and App version</li><li>IP address</li><li>Network request dates and times</li><li>Technical logs, error information, and security-related events</li></ul>
                    <p>We use this information to operate the App, deliver notifications, secure accounts, diagnose technical problems, and prevent misuse.</p>
                </section>

                <section id="use"><h2>2. How We Use Information</h2><p>We use the information described above to:</p><ul><li>Register, authenticate, and manage user accounts</li><li>Maintain member and fitness profiles</li><li>Provide classes, lessons, techniques, and instructional videos</li><li>Record lesson completion and training progress</li><li>Manage class access, memberships, packages, and physical-service purchases</li><li>Generate QR codes and process on-site gym check-ins</li><li>Send service-related and training notifications</li><li>Respond to support inquiries</li><li>Protect the security and integrity of the App</li><li>Diagnose errors and improve reliability</li><li>Comply with legal obligations and enforce our terms</li></ul><p>We do not use personal information for third-party advertising or cross-app behavioral tracking.</p></section>

                <section id="notifications"><h2>3. Push Notifications</h2><p>With your permission, Fight Zone sends push notifications about classes, memberships, training activities, account events, and other Fight Zone services.</p><p>The App uses Firebase Cloud Messaging and, on Apple devices, Apple Push Notification Service. These services use installation or device tokens to route notifications to your device.</p><p>You may disable notifications at any time through your device settings. Disabling notifications does not prevent you from using the App, but you may miss important updates.</p></section>

                <section id="video"><h2>4. Video Services</h2><p>Fight Zone uses YouTube and Vimeo to host and play Fight Zone instructional videos.</p><p>When you play a video, YouTube, Google, or Vimeo may receive technical information such as your IP address, device information, player interactions, cookies, or similar identifiers according to their own policies. Fight Zone does not require you to sign in to a personal YouTube or Vimeo account.</p><p>For more information, review:</p><ul><li><a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Google Privacy Policy</a></li><li><a href="https://www.youtube.com/t/terms" target="_blank" rel="noopener noreferrer">YouTube Terms of Service</a></li><li><a href="https://vimeo.com/privacy" target="_blank" rel="noopener noreferrer">Vimeo Privacy Policy</a></li></ul></section>

                <section id="disclosure"><h2>5. How We Disclose Information</h2><p>We may disclose information to:</p><ul><li>Authorized Fight Zone staff who manage accounts, classes, memberships, purchases, and gym check-ins</li><li>Hosting, infrastructure, and technical service providers operating on our behalf</li><li>Google Firebase for push-notification delivery</li><li>Apple for push-notification delivery on Apple devices</li><li>YouTube and Vimeo when their video players are used</li><li>Government authorities or other parties when required by law or necessary to protect safety, rights, and security</li><li>A successor organization if Fight Zone undergoes a merger, acquisition, reorganization, or transfer of assets, subject to applicable law</li></ul><p>Service providers may use information only to perform services for us or as otherwise described in their own privacy policies.</p></section>

                <section><h2>6. Sale and Advertising</h2><p>Fight Zone does not sell or rent personal information.</p><p>The App does not contain third-party advertising and does not use personal information to track users across unrelated applications or websites for advertising purposes.</p></section>
                <section><h2>7. Data Storage and International Transfers</h2><p>Information may be processed on Fight Zone systems and by service providers operating in countries outside Myanmar. Privacy and data-protection laws in those countries may differ from those in your country.</p><p>We take reasonable steps to ensure that information is handled securely and in accordance with this Privacy Policy and applicable law.</p></section>
                <section id="security"><h2>8. Data Security</h2><p>We use reasonable administrative and technical safeguards designed to protect personal information. These measures include access controls, authenticated API access, and secure device storage for authentication tokens where supported.</p></section>
            </article>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .updated { margin: 22px 0 0; color: var(--muted); font-size: 13px; }
    .policy-layout { display: grid; grid-template-columns: 220px minmax(0, 740px); justify-content: space-between; gap: 72px; margin-top: 66px; }
    .policy-index { position: sticky; top: 28px; align-self: start; display: grid; gap: 9px; padding-left: 15px; border-left: 2px solid var(--line); font-size: 13px; }
    .policy-index span { margin-bottom: 5px; color: var(--ink); font-weight: 750; }
    .policy-index a { color: var(--muted); text-decoration: none; }
    .policy-index a:hover { color: var(--orange-dark); }
    .policy-content { font-size: 16px; }
    .policy-content section { padding-top: 28px; scroll-margin-top: 25px; }
    .policy-content h2 { margin: 0 0 24px; font-size: 25px; line-height: 1.2; letter-spacing: -.025em; }
    .policy-content h3 { margin: 30px 0 10px; font-size: 17px; }
    .policy-content p { margin: 0 0 16px; color: #3f3a36; }
    .policy-content ul { margin: 0 0 18px; padding-left: 22px; color: #3f3a36; }
    .policy-content li { margin: 6px 0; padding-left: 3px; }
    .policy-content a { color: var(--orange-dark); font-weight: 650; }
    @media (max-width: 800px) { .policy-layout { grid-template-columns: 1fr; gap: 35px; margin-top: 48px; } .policy-index { position: static; grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 16px; border: 1px solid var(--line); border-left: 3px solid var(--orange); } .policy-index span { grid-column: 1 / -1; } }
    @media (max-width: 470px) { .policy-index { grid-template-columns: 1fr; } .policy-content { font-size: 15px; } .policy-content h2 { font-size: 23px; } }
</style>
@endpush
