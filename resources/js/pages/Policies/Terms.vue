<template>
  <div class="container my-5 py-3">
    <div class="mw-800 m-auto bg-white rounded-3 p-4 p-md-5 shadow-sm">
      <h1 class="mb-3">Terms & Conditions</h1>
      <p class="text-muted">Last updated: {{ updated }}</p>

      <h3>Acceptance of Terms</h3>
      <p>By using BudgetKu, you agree to these Terms & Conditions. If you do not agree, please discontinue use.</p>

      <h3>Use of Service</h3>
      <ul>
        <li>You are responsible for maintaining the confidentiality of your account.</li>
        <li>Do not abuse, interfere, or access the service using unauthorized methods.</li>
        <li>We may update features, suspend, or discontinue parts of the service with notice where reasonable.</li>
      </ul>

      <h3>Privacy</h3>
      <p>See our <a href="/privacy-policy">Privacy Policy</a> for details on how we handle your data, including optional end‑to‑end encryption.</p>

      <h3>Liability</h3>
      <p>BudgetKu is provided “as is” without warranties. To the extent permitted by law, BudgetKu is not liable for indirect or incidental damages.</p>

      <h3>Changes</h3>
      <p>We may revise these terms from time to time. Continued use constitutes acceptance of the updated terms.</p>

      <h3>Contact</h3>
      <p>Questions? Contact <a href="mailto:cs@budgetku.com">cs@budgetku.com</a>.</p>
    </div>
  </div>
</template>

<script setup>
const updated = new Date().toLocaleDateString('en-US', { month: 'long', day: '2-digit', year: 'numeric' });
</script>

