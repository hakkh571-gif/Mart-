@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold m-0"><i class="bi bi-gear-fill me-2"></i>ការកំណត់ប្រព័ន្ធ (Settings)</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm">
            <h5 class="fw-bold mb-3 border-bottom pb-2">ព័ត៌មានហាង</h5>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('settings.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">ឈ្មោះហាង</label>
                    <input type="text" name="store_name" class="form-control" value="{{ $setting->store_name ?? '' }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">អាសយដ្ឋាន</label>
                    <input type="text" name="address" class="form-control" value="{{ $setting->address ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">លេខទូរស័ព្ទ</label>
                    <input type="text" name="phone" class="form-control" value="{{ $setting->phone ?? '' }}">
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-2">
                    <i class="bi bi-save me-1"></i> រក្សាទុកការផ្លាស់ប្តូរ
                </button>
            </form>
        </div>
    </div>
</div>
@endsection