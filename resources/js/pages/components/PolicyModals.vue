<template>
  <div>
    <div class="modal fade" id="modalPrivacy" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Privacy Policy</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>At BudgetKu, we take your privacy seriously. This Privacy Policy explains what data we collect, how we use it, and the choices you have.</p>
            <h6>What We Collect</h6>
            <ul>
              <li>Account information: name, email address.</li>
              <li>App usage metadata (diagnostics and performance metrics).</li>
              <li>Encrypted financial content (see E2EE below).</li>
            </ul>
            <h6>End‑to‑End Encryption (E2EE)</h6>
            <p>Your sensitive financial data can be protected with end‑to‑end encryption. Keys are generated and kept on your device; BudgetKu never sees your plaintext data.</p>
            <h6>Cookies</h6>
            <p>We use essential cookies for authentication, security, and preferences.</p>
          </div>
          <div class="modal-footer">
            <Link href="/privacy-policy" class="btn btn-link">Open full page</Link>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalTerms" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Terms & Conditions</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>By using BudgetKu, you agree to these Terms & Conditions. If you do not agree, please discontinue use.</p>
            <h6>Use of Service</h6>
            <ul>
              <li>Maintain confidentiality of your account.</li>
              <li>Do not abuse or interfere with the service.</li>
              <li>We may update or modify features with reasonable notice.</li>
            </ul>
          </div>
          <div class="modal-footer">
            <Link href="/terms-and-conditions" class="btn btn-link">Open full page</Link>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalCookie" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cookie Policy</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>We use cookies to enable core functionality, remember preferences, and improve user experience.</p>
            <h6>E2EE‑Related Cookies</h6>
            <p>When you choose to remember encryption, we store an encrypted wrap of your session key in a cookie. We never store plaintext keys.</p>
          </div>
          <div class="modal-footer">
            <Link href="/cookie-policy" class="btn btn-link">Open full page</Link>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalDataProtection" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Data Protection</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <h6>Security by Design</h6>
            <p>HTTPS, secure session handling, access controls, and regular updates.</p>
            <h6>End‑to‑End Encryption (E2EE)</h6>
            <p>Your financial content is encrypted on your device; only you hold the keys.</p>
            <h6>Two‑Factor Authentication (2FA)</h6>
            <p>Enable TOTP and recovery codes for extra protection.</p>
          </div>
          <div class="modal-footer">
            <Link href="/data-protection" class="btn btn-link">Open full page</Link>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

