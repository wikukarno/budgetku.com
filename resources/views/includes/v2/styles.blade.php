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
<link rel="icon" type="image/png" href="{{ asset('v2/images/favicon.png') }}">

<style>
    /* Align the search box and length dropdown in header */
    div.dataTables_wrapper div.dataTables_filter {
        float: right;
        text-align: right;
    }

    div.dataTables_wrapper div.dataTables_length {
        float: left;
        text-align: left;
    }

    /* Placeholder instead of "Search:" label */
    div.dataTables_wrapper div.dataTables_filter label::before {
        content: "";
    }

    div.dataTables_wrapper div.dataTables_filter label {
        display: flex;
    }

    div.dataTables_wrapper div.dataTables_filter input {
        margin-left: 0 !important;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    /* Pagination right-aligned */
    div.dataTables_wrapper div.dataTables_paginate {
        float: right;
        margin-top: 10px;
    }

    /* Fix info text */
    div.dataTables_wrapper div.dataTables_info {
        float: left;
        margin-top: 10px;
        font-size: 13px;
        color: #6c757d;
    }

    /* Align length & search to same row */
    div.dataTables_wrapper .dataTables_length,
    div.dataTables_wrapper .dataTables_filter {
        display: inline-block;
        vertical-align: middle;
        margin-bottom: 1rem;
    }

    /* Bungkus length & search jadi satu baris penuh, antara kiri-kanan */
    div.dataTables_wrapper .dt-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    /* Style dropdown lebih rapi */
    div.dataTables_wrapper .dataTables_length select {
        width: 120px;
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    /* Style search box */
    div.dataTables_wrapper .dataTables_filter input {
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    /* Hilangkan label “Show” dan “Search:” */
    div.dataTables_wrapper .dataTables_length label,
    div.dataTables_wrapper .dataTables_filter label {
        font-size: 0;
    }

    /* Pagination */
    /* Aktif page tombol warna ungu solid */
    .dataTables_wrapper .pagination .page-item.active .page-link {
        background-color: #8b5cf6 !important;
        /* ungu solid */
        border-color: #8b5cf6 !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    /* Tombol biasa */
    .dataTables_wrapper .pagination .page-link {
        color: #6c757d;
        border-radius: 6px;
        transition: 0.2s ease;
    }

    /* Hover tombol biasa */
    .dataTables_wrapper .pagination .page-link:hover {
        background-color: #f3f4f6;
        color: #000;
        border-color: #dee2e6;
    }

    /* Tambahan spacing */
    .dataTables_wrapper .pagination .page-item {
        margin: 0 2px;
    }

    @media (max-width: 768px) {
        .dataTables_length {
            width: 100% !important;
        }

        .dataTables_length label {
            display: block !important;
            width: 100% !important;
        }

        .dataTables_length select {
            width: 100% !important;
            padding: 10px 14px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        /* Optional: sembunyikan teks "entries" kalau perlu */
        .dataTables_length label::after {
            content: "" !important;
        }

        .dataTables_filter {
            width: 100% !important;
        }

        .dataTables_filter label {
            width: 100% !important;
            display: block !important;
        }

        .dataTables_filter input {
            width: 100% !important;
            box-sizing: border-box;
            padding: 10px 14px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
    }
</style>