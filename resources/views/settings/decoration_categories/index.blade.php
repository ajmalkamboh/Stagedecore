@extends('layouts.app')

@section('title', 'Decoration Categories')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-holly-berry text-primary me-2"></span>Decoration Categories &amp; Stage Packages
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('decoration-categories.create') }}">
                    <span class="fas fa-plus text-primary me-1"></span> Add Decoration Category
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th class="text-end">Base Price</th>
                        <th class="text-center">Total Bookings</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="fw-bold text-primary">{{ $category->name }}</td>
                        <td class="fs-11 text-600">{{ $category->description ?? '—' }}</td>
                        <td class="text-end fw-bold text-success">
                            {{ $globalSettings->currency_symbol ?? 'Rs.' }} {{ number_format($category->base_price, 0) }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-subtle-primary">{{ $category->bookings_count }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                            <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('decoration-categories.toggle', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-falcon-{{ $category->is_active ? 'warning' : 'success' }} btn-sm me-1" type="submit" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                    <span class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></span>
                                </button>
                            </form>
                            <a class="btn btn-falcon-default btn-sm" href="{{ route('decoration-categories.edit', $category) }}" title="Edit">
                                <span class="fas fa-edit"></span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-500">No decoration categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($categories->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
