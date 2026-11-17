@extends('layouts.app')

@section('title', 'Add Decoration Category')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-plus text-primary me-2"></span>Add Decoration Category</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('decoration-categories.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('decoration-categories.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name">Category / Stage Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Basic Stage, Luxury Stage, Floral Arch" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="base_price">Base / Starting Price ({{ $globalSettings->currency_symbol ?? 'Rs.' }}) <span class="text-danger">*</span></label>
                    <input class="form-control @error('base_price') is-invalid @enderror" id="base_price" type="number" step="0.01" name="base_price" value="{{ old('base_price', '0') }}" required />
                    @error('base_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="is_active">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="description">Specifications &amp; Inclusions</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Details of backdrop materials, sofa type, floral coverage, lighting specs...">{{ old('description') }}</textarea>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Save Decoration Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
