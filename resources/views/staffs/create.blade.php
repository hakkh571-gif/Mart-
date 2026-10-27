@extends('layouts.app')

@section('content')
<div class="container-fluid p-0" style="max-width: 700px;">
    <div class="mb-4">
        <h3 class="fw-bold text-dark m-0">➕ បន្ថែមបុគ្គលិកថ្មី</h3>
        <p class="text-muted small">សូមបំពេញព័ត៌មានខាងក្រោមដើម្បីបញ្ចូលបុគ្គលិក</p>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('staffs.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">ឈ្មោះបុគ្គលិក</label>
                    <input type="text" name="name" class="form-control form-control-lg fs-6" placeholder="ឧទាហរណ៍: សុខ ជា" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">តួនាទី (Position)</label>
                    <select name="position" class="form-select form-select-lg fs-6" required>
                        <option value="Cashier">Cashier (អ្នកគិតលុយ)</option>
                        <option value="Barista">Barista (អ្នកឆុង)</option>
                        <option value="Manager">Manager (អ្នកគ្រប់គ្រង)</option>
                        <option value="Staff">Staff (បុគ្គលិកទូទៅ)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-secondary">លេខទូរស័ព្ទ</label>
                    <input type="text" name="phone" class="form-control form-control-lg fs-6" placeholder="012 345 678" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary">ប្រាក់បៀវត្សរ៍ ($)</label>
                    <input type="number" step="0.01" name="salary" class="form-control form-control-lg fs-6" placeholder="250.00" required>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('staffs.index') }}" class="btn btn-light px-4">បោះបង់</a>
                    <button type="submit" class="btn btn-primary px-4">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection