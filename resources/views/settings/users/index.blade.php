@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="card mb-3">
    <div class="card-header bg-light">
        <div class="row flex-between-center">
            <div class="col-4 col-sm-auto d-flex align-items-center pe-0">
                <h5 class="fs-9 mb-0 text-nowrap py-2 py-xl-0">
                    <span class="fas fa-users-cog text-primary me-2"></span>User Management
                </h5>
            </div>
            <div class="col-8 col-sm-auto text-end ps-2">
                <a class="btn btn-falcon-default btn-sm" href="{{ route('users.create') }}">
                    <span class="fas fa-plus text-primary me-1"></span> Add New User
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive scrollbar">
            <table class="table table-hover table-striped overflow-hidden mb-0">
                <thead class="bg-200 text-900">
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Status</th>
                        <th scope="col">Joined Date</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="list">
                    @forelse($users as $user)
                    <tr>
                        <td class="fw-bold text-primary">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'admin')
                            <span class="badge badge-subtle-primary"><i class="fas fa-shield-alt me-1"></i> Admin</span>
                            @else
                            <span class="badge badge-subtle-info"><i class="fas fa-user me-1"></i> Staff</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                            <span class="badge badge-subtle-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @else
                            <span class="badge badge-subtle-danger"><i class="fas fa-ban me-1"></i> Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-end">
                            <a class="btn btn-falcon-default btn-sm me-1" href="{{ route('users.edit', $user) }}" title="Edit User">
                                <span class="fas fa-edit"></span>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-falcon-danger btn-sm" type="submit" title="Delete User">
                                    <span class="fas fa-trash-alt"></span>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-500">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-light py-2">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
