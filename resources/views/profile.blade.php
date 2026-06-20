@extends('layouts.app')

@section('title', 'Group Profile')

@section('content')
    <div class="card-3d p-4 p-md-5 mb-4 reveal" style="background:linear-gradient(135deg,#4f46e5,#06b6d4);color:#fff">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div class="auth-orb" style="width:84px;height:84px;font-size:2.4rem"><i class="bi bi-mortarboard-fill"></i></div>
            </div>
            <div class="col">
                <div class="text-white-50 text-uppercase small fw-semibold">Group Name</div>
                <h2 class="fw-bold mb-1">{{ $groupName }}</h2>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <span class="badge text-bg-light text-dark px-3 py-2"><i class="bi bi-journal-bookmark me-1"></i> Course: {{ $course }}</span>
                    <span class="badge text-bg-light text-dark px-3 py-2"><i class="bi bi-people me-1"></i> {{ $members->count() }} Members</span>
                </div>
            </div>
        </div>
    </div>

    <h3 class="h6 fw-bold text-uppercase text-muted mb-3">Team Members</h3>
    <div class="row g-3 g-md-4">
        @forelse ($members as $member)
            <div class="col-12 col-sm-6 col-lg-4 reveal">
                <div class="card-3d tilt h-100 p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-grid place-content-center text-white fw-bold"
                             style="width:48px;height:48px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:grid;place-items:center">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $member->name }}</div>
                            <div class="small text-muted">{{ $member->nim }} · {{ $member->class }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge rounded-pill {{ $member->role === 'Ketua' ? 'text-bg-primary' : 'text-bg-secondary' }}">
                            {{ $member->role }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card-3d p-5 text-center text-muted">
                    No members yet. <a href="{{ route('members.create') }}">Add members</a> to populate the team.
                </div>
            </div>
        @endforelse
    </div>
@endsection
