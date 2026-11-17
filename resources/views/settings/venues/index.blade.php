@extends('layouts.app')

@section('title', 'Venues')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-hotel text-primary me-2"></span>Venues Directory
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('venues.create') }}">
                    <span class="fas fa-plus text-primary me-1"></span> Add Venue
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Venue Name</th>
                        <th>Type</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th class="text-center">Bookings</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($venues as $venue)
                    <tr>
                        <td class="fw-bold text-primary">{{ $venue->name }}</td>
                        <td>
                            @php
                                $typeColor = match($venue->venue_type) {
                                    'Marriage Hall' => 'primary',
                                    'Banquet Hall' => 'info',
                                    'Hotel' => 'success',
                                    'Home' => 'warning',
                                    'Outdoor Ground' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge badge-subtle-{{ $typeColor }}">{{ $venue->venue_type }}</span>
                        </td>
                        <td class="fs-11 text-600">{{ $venue->address ?? '—' }}</td>
                        <td class="fs-11">{{ $venue->contact_number ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-subtle-primary">{{ $venue->bookings_count }}</span>
                        </td>
                        <td>
                            @if($venue->is_active)
                            <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                            <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('venues.toggle', $venue) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-falcon-{{ $venue->is_active ? 'warning' : 'success' }} btn-sm me-1" type="submit" title="{{ $venue->is_active ? 'Deactivate' : 'Activate' }}">
                                    <span class="fas fa-{{ $venue->is_active ? 'pause' : 'play' }}"></span>
                                </button>
                            </form>
                            <a class="btn btn-falcon-default btn-sm" href="{{ route('venues.edit', $venue) }}" title="Edit">
                                <span class="fas fa-edit"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-500">No venues recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($venues->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $venues->links() }}
    </div>
    @endif
</div>
@endsection
