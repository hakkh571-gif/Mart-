@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title fw-bold mb-0">
                        <i class="bi bi-pencil-square me-2"></i>កែប្រែព័ត៌មានភេសជ្ជៈ
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- បន្ថែម enctype="multipart/form-data" ដើម្បី Upload រូបភាព -->
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">ឈ្មោះភេសជ្ជៈ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">ប្រភេទ</label>
                            <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $product->category ?? 'Drink') }}">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">តម្លៃ ($) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price', $product->price ?? 0) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="stock" class="form-label fw-semibold">ចំនួនស្តុក <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" required>
                            </div>
                        </div>

                        <!-- ផ្នែកបន្ថែមកន្លែង Upload និងបង្ហាញរូបភាព -->
                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold">រូបភាពភេសជ្ជៈ</label>
                            <input type="file" class="form-control mb-2" id="image" name="image" accept="image/*">
                            <small class="text-muted d-block mb-2">ទុកទទេ ប្រសិនបើមិនផ្លាស់ប្តូររូបភាពចាស់ទេ</small>

                            <!-- បង្ហាញរូបភាពចាស់ ប្រសិនបើមាន -->
                            @if(!empty($product->image) && file_exists(public_path('uploads/products/' . $product->image)))
                                <div class="mt-2">
                                    <span class="d-block text-muted small mb-1">រូបភាពបច្ចុប្បន្ន៖</span>
                                    <img src="{{ asset('uploads/products/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         class="img-thumbnail rounded" 
                                         style="max-height: 120px; object-fit: cover;">
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> ត្រឡប់ក្រោយ
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold">
                                <i class="bi bi-check-circle me-1"></i> បច្ចុប្បន្នភាពទិន្នន័យ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection