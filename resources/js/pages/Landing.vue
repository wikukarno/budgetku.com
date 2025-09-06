<template>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white bg-opacity-25 fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand me-xl-5 me-3" href="landing-page.html">
                <img :src="asset('v2/images/logo.svg')" alt="logo" class="light-logo" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4 active"
                            href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4"
                            href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4"
                            href="#faq">FAQ’s</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4"
                            href="#footer">Contact</a>
                    </li>
                </ul>
                <div class="othres">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary-div py-2 px-4 fw-medium fs-16 rounded-3">
                            <i class="ri-login-box-line fs-18 position-relative top-2"></i>
                            <span class="ms-1">
                                Try for Free
                            </span>
                        </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Banner -->
    <!-- Start Banner Area -->
    <div class="banner-area bg-img pb-0" id="home">
        <div class="container position-relative z-1">
            <div class="banner-content text-center pb-75">
                <h1 class="fs-60 mb-3 pb-md-3">
                    Smart Money Management—Anytime, Anywhere with BudgetKu
                </h1>
                <p class="fs-18 m-auto mb-3 pb-md-3 mw-740">
                    Take full control of your finances with BudgetKu, the smart way to track, manage, and optimize your budget effortlessly.
                </p>
                <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4 fs-16 fw-medium rounded-3">
                    <i class="ri-user-line fs-18"></i>
                    <span class="ms-1">Get started - It is free</span>
                </a>
                <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success d-inline-flex align-items-center px-3 py-2">
                        <i class="material-symbols-outlined me-1" style="font-size:18px;">lock</i>
                        Privacy-first: End-to-End Encryption
                    </span>
                    <span class="text-muted small d-none d-md-inline">Only you hold the keys</span>
                </div>
            </div>

            <img :src="asset('v2/images/landing/shape-3.png')" class="shape shape-7" alt="shape">
            <img :src="asset('v2/images/landing/shape-4.png')" class="shape shape-8" alt="shape">
        </div>
    </div>
    <!-- End Banner Area -->

    <!-- Key Features -->
    <div class="key-features-area pt-150 pb-125 position-relative z-2" id="features">
      <div class="container">
        <div class="section-title">
          <span class="top-title"><span>Key Features</span></span>
          <h2>BudgetKu: The Ultimate Solution for Smart Money Management</h2>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-4 col-md-6" v-for="f in features" :key="f.title">
            <div class="key-features-single-item">
              <i class="material-symbols-outlined wh-87 d-inline-block" :class="f.iconClass">{{ f.icon }}</i>
              <h3>{{ f.title }}</h3>
              <p>{{ f.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tailor Area (Why BudgetKu) -->
    <div class="tailor-area position-relative z-1">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-5 text-center">
            <div class="tailor-img">
              <img :src="asset('v2/images/brand.svg')" alt="tailor" />
            </div>
          </div>
          <div class="col-lg-6">
            <div class="tailor-content">
              <h2>Simplify Your Finances: Smarter Budgeting, Better Decisions</h2>
              <ul class="ps-0 mb-0 list-unstyled">
                <li v-for="item in tailorItems" :key="item.title">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <i class="material-symbols-outlined fs-20 text-primary">done_outline</i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h3>{{ item.title }}</h3>
                      <p>{{ item.desc }}</p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <img :src="asset('v2/images/landing/shape-1.png')" class="shape shape-1" alt="shape" />
      <img :src="asset('v2/images/landing/shape-2.png')" class="shape shape-2" alt="shape" />
    </div>

    <!-- FAQ Area -->
    <div class="faq-arae position-relative z-1 pt-125" id="faq">
      <div class="container">
        <div class="section-title mw-630">
          <span class="top-title"><span>FAQ’s</span></span>
          <h2>Frequently Asked Questions</h2>
          <p class="mb-0">Have questions? We have answers! Explore our FAQ section to find out more about BudgetKu and how it can help you manage your finances better.</p>
        </div>
        <div class="accordion faq-wrapper mw-740 m-auto" id="accordionExample">
          <div class="accordion-item mb-3 border-0 bg-white">
            <h2 class="accordion-header">
              <button class="accordion-button text-secondary bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne" role="button">What is BudgetKu?</button>
            </h2>
            <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <p>BudgetKu is a smart financial management tool designed to help you track income, expenses, and budgeting efficiently—all in one place.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item mb-3 border-0 bg-white">
            <h2 class="accordion-header">
              <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqTwo" aria-expanded="false" aria-controls="faqTwo" role="button">What features does BudgetKu offer?</button>
            </h2>
            <div id="faqTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <p>BudgetKu provides real-time financial tracking, insightful analytics, expense categorization, and secure data protection to help you stay on top of your finances.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item mb-3 border-0 bg-white">
            <h2 class="accordion-header">
              <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqThree" aria-expanded="false" aria-controls="faqThree" role="button">How can BudgetKu benefit me?</button>
            </h2>
            <div id="faqThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <p>With BudgetKu, you can easily monitor your financial flow, set spending limits, and gain valuable insights to make smarter money decisions.</p>
              </div>
            </div>
          </div>
          <div class="accordion-item mb-3 border-0 bg-white">
            <h2 class="accordion-header">
              <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqSecurity" aria-expanded="false" aria-controls="faqSecurity" role="button">How does BudgetKu protect my data?</button>
            </h2>
            <div id="faqSecurity" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <p>BudgetKu uses end-to-end encryption (E2EE) so your financial data is encrypted on your device and only you hold the keys. We never see your plaintext data. You can also enable Two‑Factor Authentication (2FA) for additional account security.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Unlock CTA -->
    <div class="unlock-area ptb-150 position-relative z-1" id="admin">
      <div class="container">
        <div class="border-bottom pb-150">
          <div class="row">
            <div class="unlock-content">
              <h2>Unlock the Power of Smart Budgeting with BudgetKu.</h2>
              <p>Take control of your finances effortlessly with BudgetKu. Sign up today and discover how our intuitive platform can simplify your money management.</p>
              <Link href="/login" class="btn btn-primary py-2 px-4 fs-16 fw-medium rounded-3 text-white">
                <i class="ri-user-line fs-18"></i>
                <span class="ms-1">Get Started - It's Free</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
      <img :src="asset('v2/images/landing/shape-1.png')" class="shape shape-front shape-5 custom-shape" alt="shape" />
      <img :src="asset('v2/images/landing/shape-2.png')" class="shape shape-front shape-6 custom-shape" alt="shape" />
    </div>

    <!-- Footer (trimmed) with Privacy links using modals -->
    <div class="footers-area pb-125 position-relative z-2" id="footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-sm-6">
            <div class="footer-single-item mb-4">
              <a href="#home" class="footer-logo d-inline-block mb-4">
                <img :src="asset('v2/images/logo.svg')" alt="BudgetKu Logo" />
              </a>
              <p class="mb-4 pb-lg-2">Manage your finances effortlessly with BudgetKu. Track income, expenses, and budgets all in one place with real-time insights.</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-single-item mb-4 ms-lg-5 ps-lg-5">
              <h3 class="mb-md-4 mb-3 fw-semibold">Our Services</h3>
              <ul class="ps-0 mb-0 list-unstyled">
                <li class="mb-2 pb-1"><a href="#features" class="text-decoration-none">Personal Budgeting</a></li>
                <li class="mb-2 pb-1"><a href="#features" class="text-decoration-none">Expense Tracking</a></li>
                <li class="mb-2 pb-1"><a href="#features" class="text-decoration-none">Financial Insights</a></li>
                <li class="mb-0"><a href="#features" class="text-decoration-none">Smart Reports</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-single-item mb-4 ms-lg-5 ps-lg-4">
              <h3 class="mb-md-4 mb-3 fw-semibold">Quick Links</h3>
              <ul class="ps-0 mb-0 list-unstyled">
                <li class="mb-2 pb-1"><a href="#home" class="text-decoration-none">Home</a></li>
                <li class="mb-2 pb-1"><a href="#features" class="text-decoration-none">Features</a></li>
                <li class="mb-2 pb-1"><a href="#faq" class="text-decoration-none">FAQs</a></li>
                <li class="mb-0"><a href="#footer" class="text-decoration-none">Contact Us</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="footer-single-item mb-4">
              <h3 class="mb-md-4 mb-3 fw-semibold">Privacy & Policy</h3>
              <ul class="ps-0 mb-0 list-unstyled">
                <li class="mb-2 pb-1"><a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalTerms">Terms & Conditions</a></li>
                <li class="mb-2 pb-1"><a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalCookie">Cookie Policy</a></li>
                <li class="mb-2 pb-1"><a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalDataProtection">Data Protection</a></li>
                <li class="mb-0"><a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPrivacy">Privacy Policy</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="copyright-area bg-white text-center py-4">
      <div class="container">
        <p class="fs-14">© <span class="text-primary-div">budgetku.com</span> - Your Smart Financial Companion. All rights reserved.</p>
      </div>
    </div>

    <!-- Back To Top -->
    <button type="button" id="backtotop">
      <i class="ri-arrow-up-s-line"></i>
    </button>

    <!-- Policy Modals (same content as landing Blade) -->
    <PolicyModals />
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import PolicyModals from './components/PolicyModals.vue';

function asset(path){ return '/' + String(path).replace(/^\//,''); }

const features = [
  {
    icon: 'trending_up',
    iconClass: 'bg-primary bg-opacity-25 text-primary',
    title: 'Track Income & Expenses',
    desc: 'Effortlessly manage your cash flow and stay in control of your finances—no more guesswork.'
  },
  {
    icon: 'account_balance_wallet',
    iconClass: 'bg-primary-div bg-opacity-25 text-primary-div',
    title: 'Gain Real-Time Insights',
    desc: 'Get instant analytics and smart insights to make informed financial moves with confidence.'
  },
  {
    icon: 'lock',
    iconClass: 'bg-success bg-opacity-25 text-success',
    title: 'End-to-End Encryption (E2EE)',
    desc: 'Your data is encrypted on your device and only you hold the keys. Private by design.'
  }
];

const tailorItems = [
  { title: 'Clear Financial Overview', desc: 'Get a structured view of your income, expenses, and budget in one place—no more scattered financial tracking.' },
  { title: 'Personalized Insights', desc: 'Receive smart analytics and reports to help you manage money efficiently and make informed decisions.' },
  { title: 'Flexible & Practical Features', desc: 'Designed to suit various financial needs, whether for personal budgeting or managing daily expenses.' },
  { title: 'Smart Budget Planning', desc: 'Plan monthly budgets, track spending categories, and ensure your money goes where it matters most.' }
];

onMounted(() => {
  // Initialize ScrollSpy for navbar active states
  try {
    const BS = window.bootstrap;
    if (BS?.ScrollSpy) {
      // delay a tick to ensure DOM painted
      setTimeout(() => new BS.ScrollSpy(document.body, { target: '#navbar', offset: 100 }), 0);
    }
  } catch {}

  // Back-to-top toggle
  try {
    const btn = document.getElementById('backtotop');
    const onScroll = () => {
      if (!btn) return;
      if (window.scrollY > 200) btn.classList.add('active'); else btn.classList.remove('active');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    btn?.addEventListener('click', (e) => { e.preventDefault(); window.scrollTo({ top: 0, behavior: 'smooth' }); });
  } catch {}
});
</script>
