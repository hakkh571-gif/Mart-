@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 600px;">
    <div class="card border-0 shadow-sm rounded-3 mt-4">
        <div class="card-body p-4">
            <h4 class="mb-4 text-secondary fw-bold">➕ បន្ថែមភេសជ្ជៈថ្មី</h4>

            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <!-- ឈ្មោះភេសជ្ជៈ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">ឈ្មោះភេសជ្ជៈ</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="ឧ. កាហ្វេទឹកដោះគោ">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- ប្រភេទ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">ប្រភេទ</label>
                    <select name="category" class="form-select @error('category') is-invalid @enderror">
                        <option value="">-- ជ្រើសរើសប្រភេទ --</option>
                        <option value="កាហ្វេ">កាហ្វេ</option>
                        <option value="តែ">តែ</option>
                        <option value="ទឹកផ្លែឈើ">ទឹកផ្លែឈើ</option>
                    </select>
                    @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- តម្លៃ -->
                <div class="mb-3">
                    <label class="form-label fw-bold">តម្លៃ ($)</label>
                    <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" placeholder="0.00">
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- ចំនួនស្តុក -->
                <div class="mb-3">
                    <label class="form-label fw-bold">ចំនួនស្តុក (កែវ)</label>
                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock') }}" placeholder="0">
                    @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- ប៊ូតុង Submit -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">ត្រឡប់ក្រោយ</a>
                    <button type="submit" class="btn btn-primary px-4">រក្សាទុក</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection