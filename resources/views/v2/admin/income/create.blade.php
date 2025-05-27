@extends('layouts.v2.app')

@section('title', 'Income')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <h3 class="mb-0">Create a new income</h3>
            </div>

            <form id="formdata" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Income
                            </label>
                            <input type="text" id="rupiah" class="form-control" name="salary" placeholder="Salary or other" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Date
                            </label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Type
                            </label>
                            <select class="form-select form-control" required name="category_incomes_uuid">
                                <option selected>Select</option>
                                @forelse ($categoryIncome as $item)
                                    <option value="{{ $item->uuid }}">{{ $item->name_category_incomes }}</option>
                                @empty
                                    <option value="">No data available</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Description
                            </label>
                            <textarea rows="5" class="form-control" name="description" placeholder="Type here......" required></textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="d-flex flex-wrap justify-content-end gap-3">
                            <a href="{{ route('admin.income.index') }}" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
                            <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16"> <i
                                    class="ri-add-line text-white fw-medium"></i> Create</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('after-scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        $('#formdata').on('submit', function(e) {
            e.preventDefault();

            // 🔁 Hapus semua error sebelumnya
            $('.text-danger.validation-error').remove();
            $(this).find('.is-invalid').removeClass('is-invalid');

            let isValid = true;

            // 🔍 Cek setiap input/select/textarea yang required
            $(this).find('[required]').each(function () {
                const isSelect = $(this).is('select');
                const value = $(this).val();

                if (!value || (isSelect && (value === 'Select' || value === ''))) {
                    isValid = false;

                    $(this).addClass('is-invalid');
                    $(this).next('.validation-error').remove();
                    $(this).after(`<div class="text-danger validation-error mt-1">This field is required.</div>`);
                }
            });

            if (!isValid) {
                showCustomAlert('danger', 'Please fill all required fields.');
                return;
            }

            // ✅ Jika valid, lanjut submit
            let formData = new FormData(this);
            let submitButton = $(this).find('button[type="submit"]');
            let originalText = submitButton.html();

            submitButton.prop('disabled', true);
            submitButton.html('<i class="ri-loader-4-line spin me-2"></i>Processing...');

            axios.post('{{ route('admin.income.store') }}', formData)
                .then(function(response) {
                    if (response.data.status == true) {
                        showCustomAlert('success', response.data.message);
                        setTimeout(function() {
                            window.location.href = '{{ route('admin.income.index') }}';
                        }, 2000);
                    } else {
                        showCustomAlert('danger', response.data.message);
                        submitButton.prop('disabled', false).html(originalText);
                    }
                })
                .catch(function(error) {
                    showCustomAlert('danger', error.response?.data?.message || 'An error occurred. Please try again.');
                    submitButton.prop('disabled', false).html(originalText);
                });
        });
    </script>
@endpush

