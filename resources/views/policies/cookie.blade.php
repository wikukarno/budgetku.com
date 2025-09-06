<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cookie Policy - BudgetKu</title>
  <link rel="icon" type="image/png" href="{{ asset('v2/images/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('v2/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('v2/css/google-icon.css') }}">
  <link rel="stylesheet" href="{{ asset('v2/css/remixicon.css') }}">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="/">
        <img src="{{ asset('v2/images/logo.svg') }}" alt="BudgetKu" height="26">
      </a>
    </div>
  </nav>
  <main class="container my-5 py-3">
    <div class="mw-800 m-auto bg-white rounded-3 p-4 p-md-5 shadow-sm">
      <h1 class="mb-3">Cookie Policy</h1>
      <p class="text-muted">Last updated: {{ now()->format('F d, Y') }}</p>

      <p>This Cookie Policy explains how BudgetKu uses cookies and similar technologies.</p>

      <h3>What Are Cookies?</h3>
      <p>Cookies are small text files stored on your device to help websites function, remember preferences, and improve user experience.</p>

      <h3>Types of Cookies We Use</h3>
      <ul>
        <li><strong>Essential</strong>: required for authentication, security, and core functionality.</li>
        <li><strong>Preferences</strong>: remember your settings (e.g., theme).</li>
        <li><strong>Analytics</strong>: help us understand usage and improve the product (aggregated).</li>
      </ul>

      <h3>E2EE‑Related Cookies</h3>
      <p>When you choose to remember encryption on your device, we store an encrypted wrap of your session key in a cookie to enable convenient unlocking. We never store plaintext keys.</p>

      <h3>Managing Cookies</h3>
      <p>You can control cookies via your browser settings. Disabling essential cookies may impact functionality.</p>

      <h3>Contact</h3>
      <p>For questions, contact <a href="mailto:cs@budgetku.com">cs@budgetku.com</a>.</p>
    </div>
  </main>
</body>
</html>

