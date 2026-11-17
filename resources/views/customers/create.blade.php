@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-user-plus text-primary me-2"></span>Add New Customer</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('customers.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Customers
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        @if(session('duplicate_warning'))
        <div class="alert alert-warning border-2 mb-3" role="alert">
            <div class="d-flex">
                <div class="me-3"><span class="fas fa-exclamation-triangle fs-3 text-warning"></span></div>
                <div>
                    <h6 class="fw-bold mb-1">Duplicate Customer Warning</h6>
                    <p class="mb-2">{{ session('duplicate_warning')['message'] }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('customers.show', session('duplicate_warning')['id']) }}" class="btn btn-sm btn-dark">
                            <span class="fas fa-user me-1"></span> View Existing Customer
                        </a>
                        <button type="button" class="btn btn-sm btn-warning" onclick="document.getElementById('confirm_duplicate').value='1'; document.getElementById('customer_form').submit();">
                            <span class="fas fa-check me-1"></span> Confirm &amp; Add Anyway
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form id="customer_form" action="{{ route('customers.store') }}" method="POST">
            @csrf
            <input type="hidden" id="confirm_duplicate" name="confirm_duplicate" value="0">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name">Customer Full Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Tariq Mehmood, Mrs. Farzana" required autofocus />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="mobile_number">Primary Mobile Number <span class="text-danger">*</span></label>
                    <input class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" type="text" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="0300-1234567" required />
                    <div class="fs-11 text-500">Used for customer identification &amp; SMS receipts</div>
                    @error('mobile_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="alternate_mobile">Alternate / WhatsApp Number</label>
                    <input class="form-control @error('alternate_mobile') is-invalid @enderror" id="alternate_mobile" type="text" name="alternate_mobile" value="{{ old('alternate_mobile') }}" placeholder="0321-7654321" />
                    @error('alternate_mobile')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="address">Customer Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="House / Area / City address">{{ old('address') }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="notes">Internal Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Preferences, reference source, customer VIP status...">{{ old('notes') }}</textarea>
                    @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Save Customer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
