@extends('layouts.app')

@section('title', 'Add Venue')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-plus text-primary me-2"></span>Add Venue</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('venues.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('venues.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold" for="name">Venue Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Royal Palm Grand Hall, Barki Road Farmhouse" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" for="venue_type">Venue Type <span class="text-danger">*</span></label>
                    <select class="form-select @error('venue_type') is-invalid @enderror" id="venue_type" name="venue_type" required>
                        @foreach($venueTypes as $vt)
                        <option value="{{ $vt }}" {{ old('venue_type') == $vt ? 'selected' : '' }}>{{ $vt }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" for="contact_number">Venue Manager / Contact Number</label>
                    <input class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" type="text" name="contact_number" value="{{ old('contact_number') }}" placeholder="042-111... or 0300-..." />
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" for="is_active">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', '0') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Default Venue Commission -->
                <div class="col-12">
                    <div class="card border border-200">
                        <div class="card-header bg-light py-2">
                            <h6 class="mb-0 fs-10 text-700 fw-bold"><span class="fas fa-hand-holding-usd text-warning me-2"></span>Default Referral / Venue Commission (Optional)</h6>
                        </div>
                        <div class="card-body bg-body-tertiary py-3">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-10 fw-bold" for="default_commission_type">Commission Type</label>
                                    <select class="form-select form-select-sm" id="default_commission_type" name="default_commission_type">
                                        <option value="none" {{ old('default_commission_type') == 'none' ? 'selected' : '' }}>No Commission Required</option>
                                        <option value="percentage" {{ old('default_commission_type') == 'percentage' ? 'selected' : '' }}>Percentage of Booking Amount (%)</option>
                                        <option value="fixed" {{ old('default_commission_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount per Event (Rs.)</option>
                                    </select>
                                    <small class="text-muted">If this marquee/hall charges standard commission, select here to auto-apply on booking form.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-10 fw-bold" for="default_commission_rate">Default Commission Rate / Amount</label>
                                    <input class="form-control form-control-sm" id="default_commission_rate" type="number" step="0.01" min="0" name="default_commission_rate" value="{{ old('default_commission_rate', '0.00') }}" placeholder="e.g. 10 for 10% or 15000 for Rs. 15,000" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="address">Full Venue Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold" for="notes">Notes / Special Instructions (Ceiling height, setup entry timings, etc.)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Save Venue
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
