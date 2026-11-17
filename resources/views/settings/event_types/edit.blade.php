@extends('layouts.app')

@section('title', 'Edit Event Type')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-edit text-primary me-2"></span>Edit Event Type: {{ $eventType->name }}</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('event-types.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('event-types.update', $eventType) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold" for="name">Event Type Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name', $eventType->name) }}" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" for="is_active">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', (string)(int)$eventType->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', (string)(int)$eventType->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $eventType->description) }}</textarea>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Update Event Type
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
