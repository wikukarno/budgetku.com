<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="BudgetKu - Aplikasi Keuangan Pribadi Terbaik untuk Atur Pengeluaran & Pemasukan">
    <meta name="description"
        content="BudgetKu adalah aplikasi manajemen keuangan pribadi yang membantu kamu mencatat pemasukan, pengeluaran, dan mengatur anggaran bulanan dengan mudah. Dapatkan insight keuangan secara real-time dan kontrol arus kas kamu hari ini!">
    <meta name="keywords"
        content="aplikasi keuangan pribadi, catatan keuangan, budgeting, pengeluaran bulanan, aplikasi pengatur uang, manajemen keuangan, aplikasi keuangan Indonesia, pengatur pengeluaran, aplikasi budgeting terbaik, kelola uang harian">
    <meta name="author" content="BudgetKu">
    <meta name="robots" content="index, follow">
    <meta name="language" content="en">
    
    <!-- Favicon -->
    <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('v2/images/favicon.png') }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://budgetku.com/">

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="{{ asset('v2/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="{{ asset('v2/css/style.css') }}">
    <!-- Favicon -->
    <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('v2/images/favicon.png') }}">
    <!-- Title -->
    <title>BudgetKu - Smart Personal Finance Management App</title>
</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example2" data-bs-root-margin="0px 0px -40%"
    data-bs-smooth-scroll="true" class="scrollspy-example" tabindex="0">
    <!-- Start Preloader Area -->
    <div class="preloader" id="preloader">
        <div class="preloader">
            <div class="waviy position-relative">
                <span class="d-inline-block">B</span>
                <span class="d-inline-block">U</span>
                <span class="d-inline-block">D</span>
                <span class="d-inline-block">G</span>
                <span class="d-inline-block">E</span>
                <span class="d-inline-block">T</span>
                <span class="d-inline-block">K</span>
                <span class="d-inline-block">U</span>
            </div>
        </div>
    </div>
    <!-- End Preloader Area -->

    <!-- Start Navbar Area -->
    <nav class="navbar navbar-expand-lg bg-white bg-opacity-25 fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand me-xl-5 me-3" href="landing-page.html">
                <img src="{{ asset('v2/images/logo.svg') }}" alt="logo">
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
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-primary-div py-2 px-4 fw-medium fs-16 rounded-3">
                            <i class="ri-login-box-line fs-18 position-relative top-2"></i>
                            <span class="ms-1">
                                Try for Free
                            </span>
                        </a>
                    @endguest
                    @auth
                        @if (Auth::user()->roles == "Owner")
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary-div py-2 px-4 fw-medium fs-16 rounded-3">
                                <i class="ri-dashboard-line fs-18 position-relative top-2"></i>
                                <span class="ms-1">
                                    Dashboard
                                </span>
                            </a>
                        @else
                            <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-primary-div py-2 px-4 fw-medium fs-16 rounded-3">
                                <i class="ri-dashboard-line fs-18 position-relative top-2"></i>
                                <span class="ms-1">
                                    Dashboard
                                </span>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <!-- End Navbar Area -->

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

            <img src="v2/images/landing/shape-3.png" class="shape shape-7" alt="shape">
            <img src="v2/images/landing/shape-4.png" class="shape shape-8" alt="shape">
        </div>
    </div>
    <!-- End Banner Area -->

    <!-- Start Key Features Area -->
    <div class="key-features-area pt-150 pb-125 position-relative z-2" id="features">
        <div class="container">
            <div class="section-title">
                <span class="top-title">
                    <span>Key Features</span>
                </span>
                <h2>
                    BudgetKu: The Ultimate Solution for Smart Money Management
                </h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="key-features-single-item">
                        <i
                            class="material-symbols-outlined wh-87 bg-primary bg-opacity-25 d-inline-block text-primary">
                            trending_up
                        </i>
                        <h3>Track Income & Expenses</h3>
                        <p>
                            Effortlessly manage your cash flow and stay in control of your finances—no more guesswork.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="key-features-single-item">
                        <i
                            class="material-symbols-outlined wh-87 bg-primary-div bg-opacity-25 d-inline-block text-primary-div">
                            account_balance_wallet
                        </i>
                        <h3>
                            Gain Real-Time Insights
                        </h3>
                        <p>
                            Get instant analytics and smart insights to make informed financial moves with confidence.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="key-features-single-item">
                        <i class="material-symbols-outlined wh-87 bg-success bg-opacity-25 d-inline-block text-success">
                            lock
                        </i>
                        <h3>End-to-End Encryption (E2EE)</h3>
                        <p>
                            Your data is encrypted on your device and only you hold the keys. Budgets, categories, and sensitive information stay private—secure by design.
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <!-- End Key Features Area -->

    <!-- Start Tailor Area -->
    <div class="tailor-area position-relative z-1">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 text-center">
                    <div class="tailor-img">
                        <img src="{{ asset('v2/images/brand.svg') }}" alt="tailor">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="tailor-content">
                        <h2>
                            Simplify Your Finances: Smarter Budgeting, Better Decisions
                        </h2>
                        <ul class="ps-0 mb-0 list-unstyled">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="material-symbols-outlined fs-20 text-primary">done_outline</i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>Clear Financial Overview</h3>
                                        <p>
                                            Get a structured view of your income, expenses, and budget in one place—no more scattered financial tracking.
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="material-symbols-outlined fs-20 text-primary">done_outline</i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>Personalized Insights</h3>
                                        <p>
                                            Receive smart analytics and reports to help you manage money efficiently and make informed decisions.
                                        </p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="material-symbols-outlined fs-20 text-primary">done_outline</i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>Flexible & Practical Features</h3>
                                        <p>Designed to suit various financial needs, whether for personal budgeting or managing daily expenses.</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <i class="material-symbols-outlined fs-20 text-primary">done_outline</i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3>
                                            Privacy‑First & Secure (E2EE)
                                        </h3>
                                        <p>
                                            Your financial data is protected with end‑to‑end encryption — only you hold the keys. Enable 2FA for extra account security.
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <img src="v2/images/landing/shape-1.png" class="shape shape-1" alt="shape">
        <img src="v2/images/landing/shape-2.png" class="shape shape-2" alt="shape">
    </div>
    <!-- End Tailor Area -->

    <!-- Start FAQ Area -->
    <div class="faq-arae position-relative z-1 pt-125" id="faq">
        <div class="container">
            <div class="section-title mw-630">
                <span class="top-title">
                    <span>FAQ’s</span>
                </span>
                <h2>
                    Frequently Asked Questions
                </h2>
                <p class="mb-0">
                    Have questions? We have answers! Explore our FAQ section to find out more about BudgetKu and how it can
                    help you manage your finances better.
                </p>
            </div>

                <div class="accordion faq-wrapper mw-740 m-auto" id="accordionExample">
                    <div class="accordion-item mb-3 border-0 bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary bg-white" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" role="button">
                            What is BudgetKu?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>BudgetKu is a smart financial management tool designed to help you track income, expenses, and
                                budgeting efficiently—all in one place.</p>
                        </div>
                    </div>
                <div class="accordion-item mb-3 border-0 bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSecurity" aria-expanded="false" aria-controls="collapseSecurity" role="button">
                            How does BudgetKu protect my data?
                        </button>
                    </h2>
                    <div id="collapseSecurity" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>
                                BudgetKu uses end-to-end encryption (E2EE) so your financial data is encrypted on your device and only you hold the keys.
                                We never see your plaintext data. You can also enable Two‑Factor Authentication (2FA) for additional account security.
                            </p>
                        </div>
                    </div>
                </div>
                </div>
                <div class="accordion-item mb-3 border-0 bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" role="button">
                            What features does BudgetKu offer?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>BudgetKu provides real-time financial tracking, insightful analytics, expense categorization, and
                                secure data protection to help you stay on top of your finances.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item mb-3 border-0 bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" role="button">
                            How can BudgetKu benefit me?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>With BudgetKu, you can easily monitor your financial flow, set spending limits, and gain valuable
                                insights to make smarter money decisions.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item mb-3 border-0 bg-white">
                    <h2 class="accordion-header">
                        <button class="accordion-button text-secondary bg-white collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven" role="button">
                            Can I customize my budget categories?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>
                                Absolutely. You can create, edit, and manage your own categories easily.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End FAQ Area -->

    <!-- Start Unlock Area -->
    <div class="unlock-area ptb-150 position-relative z-1" id="admin">
        <div class="container">
            <div class="border-bottom pb-150">
                <div class="row">
                    <div class="unlock-content">
                        <h2>Unlock the Power of Smart Budgeting with BudgetKu.</h2>
                        <p>Take control of your finances effortlessly with BudgetKu. Sign up today and discover how our
                            intuitive platform can simplify your money management.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary py-2 px-4 fs-16 fw-medium rounded-3 text-white">
                            <i class="ri-user-line fs-18"></i>
                            <span class="ms-1">Get Started - It's Free</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    
        <img src="v2/images/landing/shape-1.png" class="shape shape-front shape-5 custom-shape" alt="shape">
        <img src="v2/images/landing/shape-2.png" class="shape shape-front shape-6 custom-shape" alt="shape">
    </div>
    <!-- End Unlock Area -->

    <!-- Start Forter Area -->
    <div class="footers-area pb-125 position-relative z-2" id="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4">
                        <a href="/" class="footer-logo d-inline-block mb-4">
                            <img src="{{ asset('v2/images/logo.svg') }}" alt="BudgetKu Logo"> 
                        </a>
                        <p class="mb-4 pb-lg-2">Manage your finances effortlessly with BudgetKu. Track income, expenses, and
                            budgets all in one place with real-time insights.</p>

                        <ul class="list-unstyled text-muted fs-14">
                            <li class="mb-1">
                                <i class="ri-mail-line me-2 text-primary"></i>
                                <a href="mailto:cs@budgetku.com" class="text-decoration-none text-muted">
                                    cs@budgetku.com
                                </a>
                            </li>
                            <li>
                                <i class="ri-phone-line me-2 text-primary"></i>
                                <a href="https://wa.me/6287831247352" target="_blank" class="text-decoration-none text-muted">
                                    +62 878-3124-7352
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4 ms-lg-5 ps-lg-5">
                        <h3 class="mb-md-4 mb-3 fw-semibold">Our Services</h3>
                        <ul class="ps-0 mb-0 list-unstyled">
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Personal Budgeting</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Expense Tracking</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Financial Insights</a>
                            </li>
                            <li class="mb-0">
                                <a href="#" class="text-decoration-none">Smart Reports</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4 ms-lg-5 ps-lg-4">
                        <h3 class="mb-md-4 mb-3 fw-semibold">Quick Links</h3>
                        <ul class="ps-0 mb-0 list-unstyled">
                            <li class="mb-2 pb-1">
                                <a href="landing-page.html" class="text-decoration-none">Home</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="features.html" class="text-decoration-none">Features</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="faqs.html" class="text-decoration-none">FAQs</a>
                            </li>
                            <li class="mb-0">
                                <a href="#footer" class="text-decoration-none">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4">
                        <h3 class="mb-md-4 mb-3 fw-semibold">Privacy & Policy</h3>
                        <ul class="ps-0 mb-0 list-unstyled">
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalTerms">Terms & Conditions</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalCookie">Cookie Policy</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalDataProtection">Data Protection</a>
                            </li>
                            <li class="mb-0">
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalPrivacy">Privacy Policy</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Forter Area -->

    <!-- Start CopyRight Area -->
    <div class="copyright-area bg-white text-center py-4">
        <div class="container">
            <p class="fs-14">© <span class="text-primary-div">budgetku.com</span> - Your Smart Financial Companion. All rights
                reserved.</p>
        </div>
    </div>
    <!-- End CopyRight Area -->

    <!-- Policy Modals (no-reload on landing page) -->
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
            <a href="{{ route('privacy.policy') }}" class="btn btn-link">Open full page</a>
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
            <a href="{{ route('terms') }}" class="btn btn-link">Open full page</a>
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
            <a href="{{ route('cookie.policy') }}" class="btn btn-link">Open full page</a>
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
            <a href="{{ route('data.protection') }}" class="btn btn-link">Open full page</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Start Back To Up Area -->
    <button type="button" id="backtotop">
        <i class="ri-arrow-up-s-line"></i>
    </button>
    <!-- End Back To Up Area -->

    <!-- Link Of JS File -->
    <script src="{{ asset('v2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('v2/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('v2/js/dragdrop.js') }}"></script>
    <script src="{{ asset('v2/js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('v2/js/sweetalert.js') }}"></script>
    <script src="{{ asset('v2/js/quill.min.js') }}"></script>
    <script src="{{ asset('v2/js/data-table.js') }}"></script>
    <script src="{{ asset('v2/js/prism.js') }}"></script>
    <script src="{{ asset('v2/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('v2/js/feather.min.js') }}"></script>
    <script src="{{ asset('v2/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('v2/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('v2/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('v2/js/fullcalendar.main.js') }}"></script>
    <script src="{{ asset('v2/js/custom/apexcharts.js') }}"></script>
    <script src="{{ asset('v2/js/custom/custom.js') }}"></script>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-F3R8SVSH01"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-F3R8SVSH01');
    </script>
</body>

</html>
