<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Terms & Conditions | BudgetKu</title>
    <meta name="description" content="Terms and Conditions for BudgetKu, your personal finance management application.">
    <meta name="keywords" content="BudgetKu, Terms and Conditions, Personal Finance, Application">
    <meta name="author" content="BudgetKu Team">
    <meta name="robots" content="index, follow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('v2/css/style.css') }}">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white bg-opacity-25 fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand me-xl-5 me-3" href="landing-page.html">
                <img src="{{ asset('v2/images/logo.svg') }}" alt="logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4 {{ request()->is('/') ? 'active' : ''}}"
                            href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fs-16 fw-medium text-body hover px-0 px-md-2 mx-1 mx-xl-0 px-xl-4 {{ request()->is('terms-and-conditions') ? 'active' : '' }}"
                            href="{{ route('terms') }}">Terms & Conditions</a>
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

    <div class="container pt-5 terms-conditions">
        <h1 class="mb-4 fw-bold">Terms & Conditions</h1>
        <p class="mb-4">Welcome to BudgetKu. These Terms and Conditions outline the rules and regulations for the use of
            our application and services.</p>

        <h2 class="mt-5 mb-3 h4">1. Acceptance of Terms</h2>
        <p>By accessing or using BudgetKu, you agree to be bound by these Terms. If you disagree with any part, you may
            not use the service.</p>

        <h2 class="mt-5 mb-3 h4">2. Changes to Terms</h2>
        <p>We may update or change these Terms from time to time without prior notice. It is your responsibility to
            check this page regularly for any updates.</p>

        <h2 class="mt-5 mb-3 h4">3. Use of Service</h2>
        <ul class="mb-4">
            <li>BudgetKu is intended for personal finance management only.</li>
            <li>You agree not to use the service for any illegal or unauthorized purposes.</li>
            <li>Misuse of the service may result in termination of your access.</li>
        </ul>

        <h2 class="mt-5 mb-3 h4">4. User Accounts</h2>
        <p>You are responsible for maintaining the confidentiality of your login credentials and for all activities that
            occur under your account.</p>

        <h2 class="mt-5 mb-3 h4">5. Intellectual Property</h2>
        <p>All content and software associated with BudgetKu are the property of BudgetKu and its licensors and are
            protected by copyright and intellectual property laws.</p>

        <h2 class="mt-5 mb-3 h4">6. Limitation of Liability</h2>
        <p>We are not liable for any direct, indirect, incidental, or consequential damages resulting from the use or
            inability to use the service.</p>

        <h2 class="mt-5 mb-3 h4">7. Termination</h2>
        <p>We reserve the right to suspend or terminate your access to BudgetKu at any time, without notice, for conduct
            that we believe violates these Terms.</p>

        <h2 class="mt-5 mb-3 h4">8. Governing Law</h2>
        <p>These Terms shall be governed by and construed in accordance with the laws of Indonesia.</p>

        <h2 class="mt-5 mb-3 h4">9. Contact Us</h2>
        <p>If you have any questions about these Terms, you can contact us at <strong>cs@budgetku.com</strong>.</p>

        <p class="mt-5 text-muted">Last updated: <strong>April 8, 2025</strong></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>