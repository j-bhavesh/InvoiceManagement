@extends('layouts.app')

@section('title', 'Add Client')
@section('page-title', 'Add New Client')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-plus mr-2 text-primary"></i>Client Details
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    @include('users._form')
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Create Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
