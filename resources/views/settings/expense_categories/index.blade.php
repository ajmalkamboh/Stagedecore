@extends('layouts.app')

@section('title', 'Expense Categories')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-tags text-primary me-2"></span>Expense Categories
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('expense-categories.create') }}">
                    <span class="fas fa-plus text-primary me-1"></span> Add Expense Category
                </a>
            </div>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="card-body bg-body-tertiary border-bottom py-2">
        <form method="GET" action="{{ route('expense-categories.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ $search }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    @if($search)
                    <a href="{{ route('expense-categories.index') }}" class="btn btn-falcon-default" title="Clear Search">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-200">
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th class="text-center">Associated Expenses</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="fw-bold text-primary">{{ $category->name }}</td>
                        <td class="fs-11 text-600">{{ $category->description ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-subtle-primary">{{ $category->expenses_count }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                            <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('expense-categories.toggle', $category) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-falcon-{{ $category->is_active ? 'warning' : 'success' }} btn-sm me-1" type="submit" title="{{ $category->is_active ? 'Deactivate' : 'Activate' }}">
                                    <span class="fas fa-{{ $category->is_active ? 'pause' : 'play' }}"></span>
                                </button>
                            </form>
                            <a class="btn btn-falcon-default btn-sm me-1" href="{{ route('expense-categories.edit', $category) }}" title="Edit">
                                <span class="fas fa-edit"></span>
                            </a>
                            @if($category->expenses_count === 0)
                            <form action="{{ route('expense-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this expense category?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-falcon-danger btn-sm" type="submit" title="Delete">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-500">No expense categories found.</td>
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
