@extends('layouts.app')

@section('title', 'Event Types')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-glass-cheers text-primary me-2"></span>Event Types
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('event-types.create') }}">
                    <span class="fas fa-plus text-primary me-1"></span> Add Event Type
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th class="text-center">Total Bookings</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($eventTypes as $et)
                    <tr>
                        <td class="fw-bold text-primary">{{ $et->name }}</td>
                        <td class="text-600">{{ $et->description ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-subtle-primary">{{ $et->bookings_count }}</span>
                        </td>
                        <td>
                            @if($et->is_active)
                            <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                            <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('event-types.toggle', $et) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-falcon-{{ $et->is_active ? 'warning' : 'success' }} btn-sm me-1" type="submit" title="{{ $et->is_active ? 'Deactivate' : 'Activate' }}">
                                    <span class="fas fa-{{ $et->is_active ? 'pause' : 'play' }}"></span>
                                </button>
                            </form>
                            <a class="btn btn-falcon-default btn-sm" href="{{ route('event-types.edit', $et) }}" title="Edit">
                                <span class="fas fa-edit"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-500">No event types found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($eventTypes->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $eventTypes->links() }}
    </div>
    @endif
</div>
@endsection
