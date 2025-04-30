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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

    .count {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 4px 12px !important;
        min-width: 60px !important;
        text-align: center !important;
        border-radius: 999px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        line-height: 1 !important;
        white-space: nowrap !important;
        height: 30px !important;
        /* FIX: biar sejajar */
    }

    /* Select 2 custom */

    /* Tinggi dan padding sama dengan input form biasa */
    .select2-container .select2-selection--single {
        height: 45px !important;
        padding: 8px 14px;
        border: 1px solid #ced4da;
        border-radius: 0.5rem;
        font-size: 1rem;
        line-height: 1.5;
        transition: all 0.2s ease-in-out;
    }

    /* Text di dalam select2 */
    .select2-selection__rendered {
        line-height: 28px !important;
        color: #495057;
    }

    /* Icon dropdown */
    .select2-selection__arrow {
        height: 100% !important;
        top: 10px;
        right: 8px;
    }

    /* Saat fokus: ubah border jadi ungu dan ada efek shadow */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #7c3aed !important; /* Ungu seperti input */
        box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25); /* efek glowing */
        outline: 0;
    }

    /* Search input di dropdown */
    .select2-container .select2-search--dropdown .select2-search__field {
        height: 40px;
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        font-size: 1rem;
    }

    /* Saat search input focus */
    .select2-container .select2-search--dropdown .select2-search__field:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25);
        outline: none;
    }

    /* Warna highlight ungu saat item di-hover atau terseleksi */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #7c3aed !important; /* warna ungu */
        color: #fff;
    }

    /* Warna background ungu saat item dipilih (yang aktif) */
    .select2-container--default .select2-results__option[aria-selected="true"] {
        background-color: #ede9fe !important; /* ungu muda */
        color: #7c3aed !important; /* ungu teks */
    }

    /* Saat search field sedang fokus */
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 0.25rem rgba(124, 58, 237, 0.25);
        outline: none;
    }

    /* Hapus efek inner box tambahan & fix double border */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #7c3aed;
        box-shadow: none !important; /* hilangkan shadow extra */
        outline: none;
        background-color: #fff; /* biar gak tumpang tindih */
    }

    /* Saat fokus tetap ungu tapi 1 border aja */
    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border: 1px solid #7c3aed;
        box-shadow: none !important;
        outline: none;
    }

    /* Sembunyikan arrow default Select2 */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        display: none !important;
    }

    /* Tambahkan custom arrow dengan pseudo-element */
    .select2-container--default .select2-selection--single {
        position: relative;
    }

    .select2-container--default .select2-selection--single::after {
        content: "▾"; /* atau bisa pakai FontAwesome / Feather */
        font-size: 1rem;
        color: #6c757d;
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        pointer-events: none;
    }

    /* Tambahan biar tidak keganggu sama padding */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-right: 2rem !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        display: none !important; /* hide the default arrow triangle */
    }

    /* Tambahkan custom arrow pakai background SVG Remix */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        background-image: url("data:image/svg+xml,%3Csvg fill='gray' viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M8.292 9.293a1 1 0 011.416 0L12 11.586l2.292-2.293a1 1 0 011.416 1.414l-3 3a1 1 0 01-1.416 0l-3-3a1 1 0 010-1.414z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 1rem 1rem;
        width: 30px;
        height: 100%;
        position: absolute;
        top: 0;
        right: 0;
        pointer-events: none;
    }

</style>
