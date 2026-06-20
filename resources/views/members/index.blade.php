@extends('layouts.app')

@section('title', 'Group Members')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <p class="text-muted mb-0">{{ $members->count() }} member(s) registered.</p>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add Member
        </a>
    </div>

    <div class="card-3d p-0 overflow-hidden reveal">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Name</th>
                        <th>NIM</th>
                        <th>Class</th>
                        <th>Role</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $member->name }}</td>
                            <td>{{ $member->nim }}</td>
                            <td><span class="badge text-bg-light border">{{ $member->class }}</span></td>
                            <td>
                                <span class="badge rounded-pill {{ $member->role === 'Ketua' ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $member->role }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('members.show', $member) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('members.edit', $member) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('members.destroy', $member) }}" class="d-inline"
                                      onsubmit="return confirm('Delete {{ $member->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No members yet. <a href="{{ route('members.create') }}">Add the first one</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
