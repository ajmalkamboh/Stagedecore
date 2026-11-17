@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-auto">
                <h5 class="fs-9 mb-0"><span class="fas fa-user-edit text-primary me-2"></span>Edit User: {{ $user->name }}</h5>
            </div>
            <div class="col-auto">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('users.index') }}">
                    <span class="fas fa-arrow-left me-1"></span> Back to Users
                </a>
            </div>
        </div>
    </div>
    <div class="card-body bg-body-tertiary">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="name">Full Name <span class="text-danger">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required />
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold" for="email">Email Address <span class="text-danger">*</span></label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" for="password">Password (Leave blank to keep current)</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" minlength="6" />
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" for="role">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff (Operations, Bookings, Payments, Expenses)</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (Full System Access)</option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold" for="is_active">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', (string)(int)$user->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', (string)(int)$user->is_active) == '0' ? 'selected' : '' }}>Inactive / Deactivated</option>
                    </select>
                    @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 text-end mt-4">
                    <button class="btn btn-primary px-4" type="submit">
                        <span class="fas fa-save me-1"></span> Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
