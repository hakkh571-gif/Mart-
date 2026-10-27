@extends('layouts.app')

@content
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="bi bi-pencil-square me-2"></i>កែប្រែព័ត៌មានបុគ្គលិក
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('staffs.update', $staff->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">ឈ្មោះបុគ្គលិក <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $staff->name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label fw-semibold">លេខទូរស័ព្ទ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $staff->phone ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label fw-semibold">តួនាទី</label>
                            <select class="form-select" id="role" name="role">
                                <option value="Cashier" {{ (isset($staff->role) && $staff->role == 'Cashier') ? 'selected' : '' }}>Cashier</option>
                                <option value="Manager" {{ (isset($staff->role) && $staff->role == 'Manager') ? 'selected' : '' }}>Manager</option>
                                <option value="Barista" {{ (isset($staff->role) && $staff->role == 'Barista') ? 'selected' : '' }}>Barista</option>
                                <option value="Staff" {{ (isset($staff->role) && $staff->role == 'Staff') ? 'selected' : '' }}>Staff</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="salary" class="form-label fw-semibold">ប្រាក់ខែ ($)</label>
                            <input type="number" step="0.01" class="form-control" id="salary" name="salary" value="{{ old('salary', $staff->salary ?? 0) }}">
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('staffs.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> ត្រឡប់ក្រោយ
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold">
                                <i class="bi bi-check-circle me-1"></i> បច្ចុប្បន្នភាពព័ត៌មាន
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endcontent