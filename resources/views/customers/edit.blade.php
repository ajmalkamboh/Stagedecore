@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-user-edit text-primary me-2"></span>Edit Customer: {{ $customer->name }}</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('customers.show', $customer) }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Customer Profile
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('customers.update', $customer) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name">Customer Full Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name', $customer->name) }}" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="mobile_number">Primary Mobile Number <span class="text-danger">*</span></label>
                    <input class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" type="text" name="mobile_number" value="{{ old('mobile_number', $customer->mobile_number) }}" required />
                    @error('mobile_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="alternate_mobile">Alternate / WhatsApp Number</label>
                    <input class="form-control @error('alternate_mobile') is-invalid @enderror" id="alternate_mobile" type="text" name="alternate_mobile" value="{{ old('alternate_mobile', $customer->alternate_mobile) }}" />
                    @error('alternate_mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-9">
                    <label class="form-label fw-bold" for="address">Customer Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="is_active">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', (string)(int)$customer->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', (string)(int)$customer->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="notes">Internal Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Update Customer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
