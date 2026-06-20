@extends('layouts.app')

@section('title', 'Edit Member')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6 reveal">
            <div class="card-3d p-4 p-md-5">
                <h2 class="h5 fw-bold mb-4"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit {{ $member->name }}</h2>
                <form method="POST" action="{{ route('members.update', $member) }}" novalidate>
                    @method('PUT')
                    @include('members._form', ['submitLabel' => 'Update Member'])
                </form>
            </div>
        </div>
    </div>
@endsection
