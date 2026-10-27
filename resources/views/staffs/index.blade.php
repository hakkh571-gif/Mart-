@extends('layouts.app')

@content
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-people-fill text-primary me-2"></i>គ្រប់គ្រងបុគ្គលិក</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
            <i class="bi bi-person-plus-fill me-1"></i> បន្ថែមបុគ្គលិកថ្មី
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>ឈ្មោះបុគ្គលិក</th>
                            <th>លេខទូរស័ព្ទ</th>
                            <th>តួនាទី</th>
                            <th>ប្រាក់ខែ</th>
                            <th>ថ្ងៃចូលធ្វើការ</th>
                            <th class="text-end pe-4">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs as $index => $staff)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ strtoupper(substr($staff->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $staff->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        <i class="bi bi-telephone me-1"></i>{{ $staff->phone ?? 'គ្មាន' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $staff->role ?? 'Cashier' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($staff->salary ?? 0, 2) }}</span>
                                </td>
                                <td>{{ isset($staff->created_at) ? \Carbon\Carbon::parse($staff->created_at)->format('d-M-Y') : 'N/A' }}</td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('staffs.edit', $staff->id) }}" class="btn btn-sm btn-outline-warning me-1" title="កែប្រែ">
                                        <i class="bi bi-pencil-square"></i> កែប្រែ
                                    </a>
                                    
                                    <form action="{{ route('staffs.destroy', $staff->id) }}" method="POST" class="d-inline" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបទិន្នន័យបុគ្គលិកនេះមែនទេ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="លុប">
                                            <i class="bi bi-trash-fill"></i> លុប
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-person-x display-6 d-block mb-2"></i>
                                    មិនទាន់មានទិន្នន័យបុគ្គលិកនៅឡើយទេ!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal បន្ថែមបុគ្គលិកថ្មី -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="addStaffModalLabel"><i class="bi bi-person-plus-fill me-2"></i>បន្ថែមបុគ្គលិកថ្មី</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('staffs.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">ឈ្មោះបុគ្គលិក <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="ឧទាហរណ៍៖ សុខ ជា" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-semibold">លេខទូរស័ព្ទ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="ឧទាហរណ៍៖ 012345678" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">តួនាទី</label>
                        <select class="form-select" id="role" name="role">
                            <option value="Cashier">Cashier</option>
                            <option value="Manager">Manager</option>
                            <option value="Barista">Barista</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="salary" class="form-label fw-semibold">ប្រាក់ខែ ($)</label>
                        <input type="number" step="0.01" class="form-control" id="salary" name="salary" placeholder="250.00">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcontent