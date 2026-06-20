@extends('layouts.app')

@section('title', 'Member Detail')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-7 reveal">
            <div class="card-3d tilt p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="auth-orb"><i class="bi bi-person-fill"></i></div>
                    <div>
                        <h2 class="h4 fw-bold mb-0">{{ $member->name }}</h2>
                        <span class="badge rounded-pill {{ $member->role === 'Ketua' ? 'text-bg-primary' : 'text-bg-secondary' }}">
                            {{ $member->role }}
                        </span>
                    </div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">NIM</dt>
                    <dd class="col-sm-9 fw-semibold">{{ $member->nim }}</dd>

                    <dt class="col-sm-3 text-muted">Class</dt>
                    <dd class="col-sm-9">{{ $member->class }}</dd>

                    <dt class="col-sm-3 text-muted">Role</dt>
                    <dd class="col-sm-9">{{ $member->role }}</dd>

                    <dt class="col-sm-3 text-muted">Added</dt>
                    <dd class="col-sm-9">{{ $member->created_at?->format('d M Y, H:i') }}</dd>
                </dl>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    <a href="{{ route('members.index') }}" class="btn btn-light border">Back to list</a>
                </div>
            </div>
        </div>
    </div>
@endsection
