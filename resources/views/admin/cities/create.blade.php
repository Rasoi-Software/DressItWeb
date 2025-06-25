@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0 rounded-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
            <h6 class="text-white text-capitalize mb-0">Create City</h6>
          </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.cities.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">City Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control shadow-sm" placeholder="Enter city name" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary">← Back</a>
                    <button type="submit" class="btn btn-success">Save City</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
