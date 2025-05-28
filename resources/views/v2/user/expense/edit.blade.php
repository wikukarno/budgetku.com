@extends('layouts.v2.app')

@section('title', 'Edit Expense')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <h3 class="mb-0">
                    Edit Expense
                </h3>
            </div>

            <form id="formdata" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Category Name
                            </label>
                            <select class="form-select form-control" name="category_finances_uuid" required id="select2Categories">
                                <option selected>Select</option>
                                @forelse ($categories as $item)
                                    <option value="{{ $item->uuid }}" {{ $data->category_finances_uuid == $item->uuid ? 'selected' : '' }}>{{ $item->name_category_finances }}</option>
                                @empty
                                    <option value="">No data available</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Name
                            </label>
                            <input type="text" class="form-control" name="name_item" placeholder="Name of item" required value="{{ $data->name_item }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Date
                            </label>
                            <input type="date" class="form-control" name="purchase_date" required value="{{ $data->purchase_date }}">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Price
                            </label>
                            <input type="text" id="rupiah" class="form-control" name="price" placeholder="Price" required value="{{ $data->price }}">
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Payment Method
                            </label>
                            <select class="form-select form-control" required name="payment_methods_uuid" id="select2Payment">
                                <option selected>Select</option>
                                @forelse ($paymentMethods as $item)
                                    <option value="{{ $item->uuid }}" {{ $data->payment_methods_uuid == $item->uuid ? 'selected' : '' }}>{{ $item->name }}</option>
                                @empty
                                <option value="">No data available</option>
                                @endforelse
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            @if ($data->bukti_pembayaran)
                                <img src="{{ asset('storage/' . $data->bukti_pembayaran) }}" alt="Proof of Payment" class="img-fluid mb-2" style="max-width: 200px;">
                            @else
                                <p>No proof of payment uploaded</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            <label class="label">
                                Proof of Payment
                                &nbsp;(max 2MB)
                            </label>
                            <input type="file" class="form-control" name="bukti_pembayaran" placeholder="Upload file">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="d-flex flex-wrap justify-content-end gap-3">
                            <a href="{{ route('customer.expense.index') }}" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
                            <button type="submit" class="btn btn-primary py-2 px-4 fw-medium fs-16"> <i
                                    class="ri-add-line text-white fw-medium"></i>
                                Update</button>
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
        // Inisialisasi Select2
        $('#select2Categories, #select2Payment').select2({
            placeholder: 'Select',
        });

        $('#formdata').on('submit', function (e) {
            e.preventDefault();

            // 🔁 Bersihkan error sebelumnya
            $('.text-danger.validation-error').remove();
            $(this).find('.is-invalid').removeClass('is-invalid');

            let isValid = true;

            // 🔍 Cek semua input/select yang required
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

            // ❌ Stop jika ada yang kosong
            if (!isValid) {
                showCustomAlert('danger', 'Please fill all required fields.');
                return;
            }

            const formData = new FormData(this);
            const url = "{{ route('customer.expense.update', $data->uuid) }}";
            const submitButton = $(this).find('button[type="submit"]');
            const originalText = submitButton.html();

            // 🔄 Tampilkan loading
            submitButton.prop('disabled', true);
            submitButton.html('<i class="ri-loader-4-line spin me-2"></i>Processing...');

            axios.post(url, formData)
                .then(function (response) {
                    if (response.data.status === true) {
                        showCustomAlert('success', response.data.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('customer.expense.index') }}";
                        }, 2000);
                    } else {
                        showCustomAlert('danger', response.data.message || 'Failed to update data.');
                        submitButton.prop('disabled', false).html(originalText);
                    }
                })
                .catch(function (error) {
                    showCustomAlert('danger', error.response?.data?.message || 'An error occurred. Please try again.');
                    submitButton.prop('disabled', false).html(originalText);
                });
        });
    </script>
@endpush

