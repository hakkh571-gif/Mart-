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
        <h4 class="fw-bold mb-0"><i class="bi bi-cup-straw text-primary me-2"></i>គ្រប់គ្រងស្តុកភេសជ្ជៈ</h4>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-circle me-1"></i> បន្ថែមភេសជ្ជៈថ្មី
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>ឈ្មោះភេសជ្ជៈ</th>
                            <th>ប្រភេទ (Category)</th>
                            <th>តម្លៃ (Price)</th>
                            <th>ស្តុក (Stock)</th>
                            <th class="text-end pe-4">សកម្មភាព (Actions)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td class="ps-4 fw-bold text-secondary">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $product->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $product->category ?? 'Drink' }}</span></td>
                                <td class="fw-bold text-success">${{ number_format($product->price ?? 0, 2) }}</td>
                                <td>
                                    @if(($product->stock ?? 0) <= 5)
                                        <span class="badge bg-danger">{{ $product->stock ?? 0 }} (ជិតអស់)</span>
                                    @else
                                        <span class="badge bg-success">{{ $product->stock ?? 0 }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <!-- ប៊ូតុង Edit (កែប្រែ) -->
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-warning me-1" title="កែប្រែ">
                                        <i class="bi bi-pencil-square"></i> កែប្រែ
                                    </a>
                                    
                                    <!-- ប៊ូតុង Delete (លុប) -->
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('តើអ្នកពិតជាចង់លុបភេសជ្ជៈនេះចេញពីស្តុកមែនទេ?');">
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                    មិនទាន់មានទិន្នន័យភេសជ្ជៈនៅក្នុងស្តុកនៅឡើយទេ!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal បន្ថែមភេសជ្ជៈថ្មី -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>បន្ថែមភេសជ្ជៈថ្មី</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">ឈ្មោះភេសជ្ជៈ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="ឧទាហរណ៍៖ កាហ្វេទឹកដោះគោ" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label fw-semibold">ប្រភេទ</label>
                        <input type="text" class="form-control" id="category" name="category" value="Drink" placeholder="Drink / Food">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label fw-semibold">តម្លៃ ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="1.50" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label fw-semibold">ចំនួនស្តុក <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stock" name="stock" value="100" required>
                        </div>
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