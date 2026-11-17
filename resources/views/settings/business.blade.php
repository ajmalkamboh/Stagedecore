@extends('layouts.app')

@section('title', 'Business Settings')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-cog text-primary me-2"></span>Business Profile &amp; Settings
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm me-1" href="{{ route('backup.download') }}">
                    <span class="fas fa-download text-primary me-1"></span> Backup Database (SQL)
                </a>
                <span class="badge badge-subtle-success fs-10"><i class="fas fa-check-circle me-1"></i> Active Profile</span>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('settings.business.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <!-- Business Name -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="business_name">Business Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('business_name') is-invalid @enderror" id="business_name" type="text" name="business_name" value="{{ old('business_name', $settings->business_name) }}" required />
                    @error('business_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Owner Name -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="owner_name">Owner / Proprietor Name</label>
                    <input class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" type="text" name="owner_name" value="{{ old('owner_name', $settings->owner_name) }}" />
                    @error('owner_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="mobile_number">Primary Mobile Number <span class="text-danger">*</span></label>
                    <input class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" type="text" name="mobile_number" value="{{ old('mobile_number', $settings->mobile_number) }}" placeholder="0300-1234567" required />
                    @error('mobile_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alternate Mobile -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="alternate_number">Alternate Contact / WhatsApp</label>
                    <input class="form-control @error('alternate_number') is-invalid @enderror" id="alternate_number" type="text" name="alternate_number" value="{{ old('alternate_number', $settings->alternate_number) }}" placeholder="0321-7654321" />
                    @error('alternate_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="email">Business Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', $settings->email) }}" />
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="address">Shop / Office Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address', $settings->address) }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Currency & Symbol -->
                <div class="col-md-3">
                    <label class="form-label fw-bold" for="currency">Currency Code <span class="text-danger">*</span></label>
                    <input class="form-control @error('currency') is-invalid @enderror" id="currency" type="text" name="currency" value="{{ old('currency', $settings->currency) }}" required />
                    @error('currency')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold" for="currency_symbol">Currency Symbol <span class="text-danger">*</span></label>
                    <input class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol" type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings->currency_symbol) }}" required />
                    @error('currency_symbol')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Logo Upload -->
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="logo">Business Logo (Optional)</label>
                    <input class="form-control @error('logo') is-invalid @enderror" id="logo" type="file" name="logo" accept="image/*" />
                    @if($settings->logo_path)
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo" class="img-thumbnail" style="max-height: 50px;">
                        <span class="fs-10 text-600 ms-2">Current Logo</span>
                    </div>
                    @endif
                    @error('logo')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Receipt Footer -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="receipt_footer">Receipt &amp; Slip Footer Message</label>
                    <textarea class="form-control @error('receipt_footer') is-invalid @enderror" id="receipt_footer" name="receipt_footer" rows="2">{{ old('receipt_footer', $settings->receipt_footer) }}</textarea>
                    <div class="fs-11 text-500 mt-1">Appears at the bottom of customer booking confirmations and payment receipts.</div>
                    @error('receipt_footer')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Terms and Conditions -->
                <div class="col-12">
                    <label class="form-label fw-bold" for="terms_and_conditions">Standard Booking Terms &amp; Conditions</label>
                    <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" id="terms_and_conditions" name="terms_and_conditions" rows="4">{{ old('terms_and_conditions', $settings->terms_and_conditions) }}</textarea>
                    <div class="fs-11 text-500 mt-1">Printed on the official Booking Confirmation Slip for client signatures.</div>
                    @error('terms_and_conditions')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
