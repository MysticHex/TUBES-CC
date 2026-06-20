@extends('layouts.app')

@section('title', 'Add Member')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-6 reveal">
            <div class="card-3d p-4 p-md-5">
                <h2 class="h5 fw-bold mb-4"><i class="bi bi-person-plus me-2 text-primary"></i>New Group Member</h2>
                <form method="POST" action="{{ route('members.store') }}" novalidate>
                    @include('members._form', ['submitLabel' => 'Save Member'])
                </form>
            </div>
        </div>
    </div>
@endsection
