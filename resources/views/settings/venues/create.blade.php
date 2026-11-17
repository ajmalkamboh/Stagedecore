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
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Royal Palm Grand Hall, Client House DHA" required />
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
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
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
