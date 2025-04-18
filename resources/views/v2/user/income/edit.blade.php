@extends('layouts.v2.app')

@section('title', 'Edit Income')

@section('content')
<div class="card bg-white border-0 rounded-3 mb-4">
    <div class="card-body p-0">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                <h3 class="mb-0">
                    Edit Income
                </h3>
            </div>

            <form id="formdata" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Income
                            </label>
                            <input type="text" id="rupiah" class="form-control" name="salary" placeholder="Salary or other" required value="{{ $data->salary }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Date
                            </label>
                            <input type="date" class="form-control" name="date" required value="{{ $data->date }}">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group mb-4">
                            <label class="label">
                                <span class="text-danger">*</span>
                                Type
                            </label>
                            <select class="form-select form-control" required name="tipe">
                                <option selected>Select</option>
                                @forelse ($categoryIncome as $item)
                                    <option value="{{ $item->id }}" {{ $data->tipe == $item->id ? 'selected' : '' }}>{{ $item->name_category_incomes }}</option>
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
                            <textarea rows="5" class="form-control" name="description" placeholder="Type here......" required>{{ $data->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="d-flex flex-wrap justify-content-end gap-3">
                            <a href="{{ route('customer.income.index') }}" class="btn btn-danger py-2 px-4 fw-medium fs-16 text-white">Cancel</a>
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

<script>
    $('#formdata').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var submitButton = $(this).find('button[type="submit"]');
        var originalText = submitButton.html();

        // 🔸 Disable tombol dan ubah isi jadi loading
        submitButton.prop('disabled', true);
        submitButton.html('<i class="ri-loader-4-line spin me-2"></i>Processing...');

        axios.post("{{ route('customer.income.update', $data->id) }}", formData)
            .then(function(response) {
                if (response.data.status == true) {
                    showCustomAlert('success', response.data.message);
                    setTimeout(function() {
                        window.location.href = '{{ route('customer.income.index') }}';
                    }, 2000);
                } else {
                    showCustomAlert('error', response.data.message);
                    submitButton.prop('disabled', false).html(originalText);
                }
            })
            .catch(function(error) {
                if (error.response) {
                    showCustomAlert('error', error.response.data.message);
                } else {
                    showCustomAlert('error', 'An error occurred. Please try again.');
                }
                submitButton.prop('disabled', false).html(originalText);
            });
    });
</script>
@endpush