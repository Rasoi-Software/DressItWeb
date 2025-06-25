@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded-4">
        <div class="card-header bg-gradient-dark text-white">
            <h6>Create New User</h6>
        </div>
        <div class="card-body">
        

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @include('admin.users.form', ['user' => null])

                <button type="submit" class="btn btn-success">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
