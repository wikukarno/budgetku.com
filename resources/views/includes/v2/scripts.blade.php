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

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split   		= number_string.split(','),
            sisa     		= split[0].length % 3,
            rupiah     		= split[0].substr(0, sisa),
            ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : (rupiah ? prefix + rupiah : '');
    }

    const rupiahInput = document.getElementById('rupiah');

    rupiahInput.addEventListener('input', function(e) {
        let cursorPosition = this.selectionStart;
        let originalLength = this.value.length;

        this.value = formatRupiah(this.value, 'Rp. ');

        let newLength = this.value.length;
        cursorPosition = cursorPosition + (newLength - originalLength);

        this.setSelectionRange(cursorPosition, cursorPosition);
    });

    // Untuk mencegah input selain angka & koma
    rupiahInput.addEventListener('keydown', function(e) {
        const allowedKeys = [
            'Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete',
            'Home', 'End', ',', // koma
        ];

        if (
            allowedKeys.includes(e.key) ||
            (e.ctrlKey || e.metaKey) || // ctrl/command shortcuts
            /\d/.test(e.key) // angka
        ) {
            return;
        }

        e.preventDefault();
    });

    document.querySelector('input[name="bukti_pembayaran"]').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        // Reset feedback sebelumnya (optional)
        event.target.setCustomValidity('');

        if (file) {
            if (!allowedTypes.includes(file.type)) {
                showCustomAlert('danger', 'Invalid file type. Only JPEG, PNG, JPG, and WEBP are allowed.');
                event.target.value = ""; // Reset input
                return;
            }

            if (file.size > maxSize) {
                showCustomAlert('danger', 'File size exceeds 2MB.');
                event.target.value = ""; // Reset input
                return;
            }
        }
    });

    function logout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('logout') }}",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        showCustomAlert('success', 'Logout successful!');
                        setTimeout(function() {
                            window.location.href = "{{ route('login') }}";
                        }, 1000);
                    },
                    error: function(xhr, status, error) {
                        showCustomAlert('danger', 'Logout failed! Please try again.');
                    }
                });
            }
        })
    }
</script>