@extends('layouts.v2.app')

@section('title', 'My Account')
    
@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card bg-primary border-0 rounded-3 welcome-box style-two mb-4 position-relative">
                <div class="card-body py-38 px-4">
                    <div class="mb-5">
                        <h3 class="text-white fw-semibold">Welcome Back, <span class="text-danger-div">
                            {{ Auth::user()->name }}!</span></h3>
                        <p class="text-light">
                            Have a nice and blessed day.
                        </p>
                    </div>
    
                    {{-- <div class="d-flex align-items-center flex-wrap gap-4 gap-xxl-5">
                        <div class="d-flex align-items-center welcome-status-item style-two">
                            <div class="flex-shrink-0">
                                <i class="material-symbols-outlined">airplay</i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="text-white fw-semibold mb-0 fs-16">75h</h5>
                                <p class="text-light">Hours Spent</p>
                            </div>
                        </div>
    
                        <div class="d-flex align-items-center welcome-status-item style-two">
                            <div class="flex-shrink-0">
                                <i class="material-symbols-outlined icon-bg two">local_library</i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="text-white fw-semibold mb-0 fs-16">15</h5>
                                <p class="text-light">Course Completed</p>
                            </div>
                        </div>
                    </div> --}}
                </div>
                <img src="{{ asset('v2/images/welcome-2.gif') }}" class="welcome-2 d-none d-sm-block" alt="welcome">
            </div>
        </div>
        {{-- <div class="col-lg-6">
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-4">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <span>Total Projects</span>
                            <h3 class="mb-0 fs-20">22.5k+</h3>
                            <div class="py-3">
                                <div class="wh-77 lh-97 text-center m-auto bg-primary bg-opacity-25 rounded-circle">
                                    <i class="material-symbols-outlined fs-32 text-primary">auto_stories</i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-12">This Month</span>
                                <i class="material-symbols-outlined text-success">timeline</i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <span>Total Orders</span>
                            <h3 class="mb-0 fs-20">25k+</h3>
                            <div class="py-3">
                                <div class="wh-77 lh-97 text-center m-auto bg-primary-div bg-opacity-25 rounded-circle">
                                    <i class="material-symbols-outlined fs-32 text-primary-div">orders</i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-12">This Month</span>
                                <i class="material-symbols-outlined text-danger">trending_down</i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-4">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <span>Total Revenue</span>
                            <h3 class="mb-0 fs-20">$34.5m+</h3>
                            <div class="py-3">
                                <div class="wh-77 lh-97 text-center m-auto bg-danger bg-opacity-25 rounded-circle">
                                    <i class="material-symbols-outlined fs-32 text-danger">payments</i>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-12">This Month</span>
                                <i class="material-symbols-outlined text-success">trending_up</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <div class="row">
        <div class="col-xxl-12">
            <div class="row">
                <div class="col-xxl-12 col-md-6 col-lg-4">
                    <div class="card bg-white border-0 rounded-3 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0">Account Settings</h3>
                            </div>
    
                            {{-- Tabs --}}
                            <ul class="nav nav-tabs mb-4" id="settingsTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="account-tab" data-bs-toggle="tab"
                                        data-bs-target="#account" type="button" role="tab">Account Settings</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab"
                                        data-bs-target="#password" type="button" role="tab">Change Password</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="delete-tab" data-bs-toggle="tab" data-bs-target="#delete"
                                        type="button" role="tab">Delete Account</button>
                                </li>
                            </ul>
    
                            <div class="tab-content" id="settingsTabContent">
                                {{-- Account Settings --}}
                                <div class="tab-pane fade show active" id="account" role="tabpanel">
                                    <div class="mb-4">
                                        <h4 class="fs-20 mb-1">Profile</h4>
                                        <p class="fs-15">Update your photo and personal details here.</p>
                                    </div>
                                    <form id="account-form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group mb-4">
                                                    <label class="label text-secondary">Name</label>
                                                    <div class="form-group position-relative">
                                                        <input type="text" name="name" value="{{ Auth::user()->name }}"
                                                            class="form-control text-dark ps-5 h-55" required>
                                                        <i
                                                            class="ri-user-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-4">
                                                    <label class="label text-secondary">Email Address</label>
                                                    <div class="form-group position-relative">
                                                        <input type="email" name="email" value="{{ Auth::user()->email }}"
                                                            class="form-control text-dark ps-5 h-55" readonly>
                                                        <i
                                                            class="ri-mail-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group mb-4">
                                                    <label class="label text-secondary">Parent Email (Optional)</label>
                                                    <div class="form-group position-relative">
                                                        <input type="email" name="email_parrent" value="{{ Auth::user()->email_parrent }}"
                                                            class="form-control text-dark ps-5 h-55">
                                                        <i
                                                            class="ri-mail-unread-line position-absolute top-50 start-0 translate-middle-y fs-20 text-gray-light ps-20"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                                        </div>
                                    </form>
                                </div>
    
                                {{-- Change Password --}}
                                <div class="tab-pane fade" id="password" role="tabpanel">
                                    <div class="mb-4">
                                        <h4 class="fs-20 mb-1">Change Password</h4>
                                        <p class="fs-15">Update your account password securely.</p>
                                    </div>
                                    <form id="change-password-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="label">Current Password</label>
                                            <input type="password" name="current_password" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="label">New Password</label>
                                            <input type="password" name="new_password" class="form-control" required>
                                        </div>
                                        <div class="mb-4">
                                            <label class="label">Confirm New Password</label>
                                            <input type="password" name="new_password_confirmation" class="form-control" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </form>
                                </div>
    
                                {{-- Delete Account --}}
                                <div class="tab-pane fade" id="delete" role="tabpanel">
                                    <div class="mb-4">
                                        <h4 class="fs-20 text-danger mb-1">Delete Account</h4>
                                        <p class="fs-15">Once deleted, your account and all data will be permanently
                                            removed. This action cannot be undone.</p>
                                    </div>
                                    <form id="delete-account-form">
                                        @csrf
                                        <p class="text-muted mb-3">
                                            Once deleted, your account and all associated data will be <strong>permanently removed</strong>. This action
                                            cannot be undone.
                                        </p>
                                        <button type="submit" class="btn btn-danger text-white">
                                            Delete Account
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-scripts')
    <script>
        document.getElementById('account-form').addEventListener('submit', function(e) {
            e.preventDefault();
    
            let formData = new FormData(this);
    
            axios.post("{{ route('customer.account.update', Auth::id()) }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                params: {
                    _method: 'PUT'
                }
            }).then(response => {
                console.log(response);
                if (response.data.status) {
                    showCustomAlert('success', response.data.message);
                } else {
                    showCustomAlert('danger', 'Failed to update profile');
                }
            }).catch(error => {
                console.error(error);
                showCustomAlert('danger', 'Something went wrong!');
            });
        });

        document.getElementById('change-password-form').addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);

            axios.post("{{ route('customer.account.password.update') }}", formData, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                params: {
                    _method: 'PUT'
                }
            }).then(response => {
                if (response.data.status) {
                    showCustomAlert('success', response.data.message);
                    document.getElementById('change-password-form').reset();
                } else {
                    showCustomAlert('danger', response.data.message);
                }
            }).catch(error => {
                showCustomAlert('danger', error.response.data.message || 'Something went wrong!');
            });
        });

        document.getElementById('delete-account-form').addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: 'Your account will be permanently deleted!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post("{{ route('customer.account.delete') }}", null, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    params: {
                        _method: 'DELETE'
                    }
                }).then(response => {
                    if (response.data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('login') }}";
                        });
                    } else {
                        Swal.fire('Error', response.data.message, 'error');
                    }
                }).catch(() => {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                });
            }
        });
    });

    </script>
@endpush