<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Data Protection - BudgetKu</title>
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
      <h1 class="mb-3">Data Protection</h1>
      <p class="text-muted">Last updated: {{ now()->format('F d, Y') }}</p>

      <h3>Security by Design</h3>
      <p>BudgetKu implements industry‑standard security practices including HTTPS, secure session handling, access controls, and regular updates.</p>

      <h3>End‑to‑End Encryption (E2EE)</h3>
      <p>Where enabled, your sensitive financial content is encrypted on your device; only you hold the keys. BudgetKu cannot decrypt your data.</p>

      <h3>Two‑Factor Authentication (2FA)</h3>
      <p>Strengthen account access with time‑based one‑time codes and recovery codes.</p>

      <h3>Data Minimization & Retention</h3>
      <p>We collect only what is necessary to provide the service. You may delete your account at any time; data is removed subject to legal obligations.</p>

      <h3>Incident Response</h3>
      <p>We monitor for unusual activity and maintain procedures for detecting, reporting, and addressing security incidents.</p>

      <h3>Contact</h3>
      <p>Security questions? Email <a href="mailto:cs@budgetku.com">cs@budgetku.com</a>.</p>
    </div>
  </main>
</body>
</html>

