<template>
  <div class="container my-5 py-3">
    <div class="mw-800 m-auto bg-white rounded-3 p-4 p-md-5 shadow-sm">
      <h1 class="mb-3">Privacy Policy</h1>
      <p class="text-muted">Last updated: {{ updated }}</p>

      <p>At BudgetKu, we take your privacy seriously. This Privacy Policy explains what data we collect, how we use it, and the choices you have.</p>

      <h3>What We Collect</h3>
      <ul>
        <li>Account information: name, email address.</li>
        <li>App usage metadata (diagnostics and performance metrics).</li>
        <li>Encrypted financial content (see E2EE below).</li>
      </ul>

      <h3>End‑to‑End Encryption (E2EE)</h3>
      <p>Your sensitive financial data can be protected with end‑to‑end encryption. Keys are generated and kept on your device; BudgetKu never sees your plaintext data. You are in full control of your keys. Optionally, you can enable device/session remember to conveniently re‑unlock, without exposing plaintext to our servers.</p>

      <h3>How We Use Data</h3>
      <ul>
        <li>To provide and improve the service.</li>
        <li>To maintain security, prevent abuse, and support customers.</li>
        <li>To comply with legal obligations.</li>
      </ul>

      <h3>Cookies</h3>
      <p>We use essential cookies for authentication, security, and preferences. See our <a href="/cookie-policy">Cookie Policy</a> for details.</p>

      <h3>Data Retention</h3>
      <p>Account information is retained while your account is active. You may delete your account at any time from the app; associated data will be removed, subject to legal retention requirements.</p>

      <h3>Your Rights</h3>
      <ul>
        <li>Access, update, or delete your account data.</li>
        <li>Export or request removal of your information, where applicable.</li>
      </ul>

      <h3>Contact</h3>
      <p>Questions? Email us at <a href="mailto:cs@budgetku.com">cs@budgetku.com</a>.</p>
    </div>
  </div>
</template>

<script setup>
const updated = new Date().toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
</script>

