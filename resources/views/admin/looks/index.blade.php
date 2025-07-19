@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                        <h6 class="text-white text-capitalize mb-0">Looks List</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table table-bordered table-striped align-items-center mb-0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>User</th>
                                    <th>Goal</th>
                                    <th>Location</th>
                                    <th>Description</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($looks as $look)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div>
                                                <img src="{{ $look->user->profile_image ?? '' }}" class="avatar avatar-sm me-3 border-radius-lg" alt="user">
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $look->user->name ?? 'N/A' }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $look->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $look->set_goal }}</td>
                                    <td>{{ $look->location }}</td>
                                    <td>{{ Str::limit($look->description, 50) }}</td>
                                    <td>{{ $look->created_at->format('d M, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.looks.show', $look->id) }}" class="btn btn-info btn-sm">View</a>

                                        <form action="{{ route('admin.looks.destroy', $look->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No looks found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $looks->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Keep your JS for charts if needed, or remove --}}
@endpush
