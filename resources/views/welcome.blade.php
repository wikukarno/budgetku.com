<!DOCTYPE html>
<html lang="zxx">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="BudgetKu - Smart Financial Management Made Easy">
    <meta name="description"
        content="Track your income, expenses, and budgeting effortlessly with BudgetKu. Gain real-time insights and take control of your finances today!">
    <meta name="keywords"
        content="budgeting, finance, money management, expense tracking, personal finance, financial planning">
    <meta name="author" content="BudgetKu">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://budgetku.com/">
    <meta property="og:title" content="BudgetKu - Smart Financial Management Made Easy">
    <meta property="og:description"
        content="Track your income, expenses, and budgeting effortlessly with BudgetKu. Gain real-time insights and take control of your finances today!">
    <meta property="og:image" content="https://budgetku.com/assets/images/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://budgetku.com/">
    <meta property="twitter:title" content="BudgetKu - Smart Financial Management Made Easy">
    <meta property="twitter:description"
        content="Track your income, expenses, and budgeting effortlessly with BudgetKu. Gain real-time insights and take control of your finances today!">
    <meta property="twitter:image" content="https://budgetku.com/assets/images/twitter-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('assets-v2/images/favicon.png') }}">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://budgetku.com/">

    <!-- Links Of CSS File -->
    <link rel="stylesheet" href="{{ asset('assets-v2/css/sidebar-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/simplebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/prism.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/sweetalert.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/quill.snow.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/google-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/fullcalendar.main.css') }}">
    <link rel="stylesheet" href="{{ asset('assets-v2/css/style.css') }}">
    <!-- Favicon -->
    <link rel="icon" sizes="32x32" type="image/png" href="{{ asset('assets-v2/images/favicon.png') }}">
    <!-- Title -->
    <title>
        BudgetKu - Application for Managing Your Budget
    </title>
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
                <img src="{{ asset('assets-v2/images/logo-sm-vertical.svg') }}" alt="logo">
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
                            href="#contact">Contact</a>
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
            </div>

            <img src="assets-v2/images/landing/shape-3.png" class="shape shape-7" alt="shape">
            <img src="assets-v2/images/landing/shape-4.png" class="shape shape-8" alt="shape">
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
                        <i
                            class="material-symbols-outlined wh-87 bg-danger bg-opacity-25 d-inline-block text-danger">
                            lock
                        </i>
                        <h3>
                            Privacy & Security
                        </h3>
                        <p>
                            Your data is encrypted and protected, ensuring your financial information stays private and secure.
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
                        <img src="assets-v2/images/brand.svg" alt="tailor">
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
                                        <h3>Secure & Private</h3>
                                        <p>Your financial data is protected with top-tier security, ensuring privacy and peace of mind.</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <img src="assets-v2/images/landing/shape-1.png" class="shape shape-1" alt="shape">
        <img src="assets-v2/images/landing/shape-2.png" class="shape shape-2" alt="shape">
    </div>
    <!-- End Tailor Area -->

    <!-- Start FAQ Area -->
    <div class="faq-arae position-relative z-1 pt-125" id="faq">
        <div class="container">
            <div class="section-title mw-630">
                <span class="top-title">
                    <span>FAQ’s</span>
                </span>
                <h2>Inspiring Feedback: What Users Love About Trezo Dashboard</h2>
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
                            How secure is my financial data on BudgetKu?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <p>Your privacy is our priority. BudgetKu uses advanced encryption and strict security measures to
                                protect your financial data, ensuring it remains private and accessible only to you.</p>
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
                        <a href="contact.html" class="btn btn-primary-div py-2 px-4 fs-16 fw-medium rounded-3 text-white">
                            <i class="ri-user-line fs-18"></i>
                            <span class="ms-1">Get Started - It's Free</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    
        <img src="assets-v2/images/landing/shape-1.png" class="shape shape-front shape-5" alt="shape">
        <img src="assets-v2/images/landing/shape-2.png" class="shape shape-front shape-6" alt="shape">
    </div>
    <!-- End Unlock Area -->

    <!-- Start Forter Area -->
    <div class="footers-area pb-125 position-relative z-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4">
                        <a href="#" class="footer-logo d-inline-block mb-4">
                            <img src="{{ asset('assets-v2/images/logo-sm-vertical.svg') }}" alt="BudgetKu Logo">
                        </a>
                        <p class="mb-4 pb-lg-2">Manage your finances effortlessly with BudgetKu. Track income, expenses, and
                            budgets all in one place with real-time insights.</p>
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
                                <a href="contact.html" class="text-decoration-none">Contact Us</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="footer-single-item mb-4">
                        <h3 class="mb-md-4 mb-3 fw-semibold">Privacy & Policy</h3>
                        <ul class="ps-0 mb-0 list-unstyled">
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Terms & Conditions</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Cookie Policy</a>
                            </li>
                            <li class="mb-2 pb-1">
                                <a href="#" class="text-decoration-none">Data Protection</a>
                            </li>
                            <li class="mb-0">
                                <a href="#" class="text-decoration-none">Privacy Policy</a>
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

    <!-- Start Back To Up Area -->
    <button type="button" id="backtotop">
        <i class="ri-arrow-up-s-line"></i>
    </button>
    <!-- End Back To Up Area -->

    <!-- Link Of JS File -->
    <script src="{{ asset('assets-v2/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('assets-v2/js/dragdrop.js') }}"></script>
    <script src="{{ asset('assets-v2/js/rangeslider.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets-v2/js/quill.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/data-table.js') }}"></script>
    <script src="{{ asset('assets-v2/js/prism.js') }}"></script>
    <script src="{{ asset('assets-v2/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets-v2/js/fullcalendar.main.js') }}"></script>
    <script src="{{ asset('assets-v2/js/custom/apexcharts.js') }}"></script>
    <script src="{{ asset('assets-v2/js/custom/custom.js') }}"></script>
</body>

</html>