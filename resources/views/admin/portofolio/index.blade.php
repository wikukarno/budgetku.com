@extends('layouts.app')

@section('title', 'Portofolio')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-lg-flex d-md-flex align-items-center justify-content-between">
                <h4>Daftar Portofolio</h4>
                <a href="{{ route('portofolio.create') }}" class="btn btn-primary mt-3"><i
                        class="fa-solid fa-circle-plus"></i> &nbsp; Tambah Portofolio</a>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($portofolios as $item)
                    <div class="col-12 col-lg-4 mb-4">
                        <a href="{{ route('portofolio.edit', $item->id) }}">
                            <div class="card">
                                <img src="{{ asset(Storage::url($item->thumbnail)) }}" class="card-img-top img-fluid"
                                    alt="foto {{ $item->title }}" style="background-size: cover; height: 15rem">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $item->title }}</h5>
                                    <p class="card-text">{{ $item->kategori }} - {{ $item->created_at->isoFormat('D MMMM
                                        Y') }}</p>
                                    <a href="{{ $item->url }}" class="btn btn-success mt-3" target="_blank">Lihat
                                        Karya</a>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty

                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.portofolio.ajax')
{{-- @include('admin.portofolio.components.modal-update') --}}
@endsection