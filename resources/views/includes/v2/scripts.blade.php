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

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
    function showCustomAlert(type = 'success', message = 'Berhasil!') {
        const iconMap = {
            success: 'ri-play-circle-line',
            danger: 'ri-service-line',
            warning: 'ri-edit-box-line',
            info: 'ri-table-line',
            primary: 'ri-home-7-line',
            secondary: 'ri-star-line',
            light: 'ri-code-s-slash-fill',
            dark: 'ri-html5-line',
        };

        const icon = iconMap[type] || 'ri-information-line';

        const alertId = 'alert-' + Date.now();

        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} text-${type} d-flex align-items-center shadow-sm mb-2" role="alert">
                <i class="${icon} fs-18 me-2"></i>
                <span>${message}</span>
            </div>
        `;

        $('#customAlertWrapper').append(alertHtml);

        // Auto remove after 3 seconds
        setTimeout(() => {
            $('#' + alertId).fadeOut(500, function() {
                $(this).remove();
            });
        }, 3000);
    }

</script>