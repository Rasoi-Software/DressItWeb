@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center px-3">
                        <h6 class="text-white text-capitalize mb-0">Look Detail</h6>
                        <a href="{{ route('admin.looks.index') }}" class="btn btn-light btn-sm">Back</a>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <h4>Goal: {{ $look->set_goal }}</h4>
                    <p><strong>Location:</strong> {{ $look->location }}</p>
                    <p><strong>Description:</strong> {{ $look->description }}</p>

                    <h5 class="mt-4">Media</h5>
                    <div class="row">
                        @forelse ($look->media as $media)
                        <div class="col-md-3">
                            @if($media->media_type == 'image')
                                <img src="{{ $media->getMediaUrlAttribute() }}" class="img-fluid mb-2" alt="Image">
                            @elseif($media->media_type == 'video')
                                <video src="{{ $media->getMediaUrlAttribute() }}" controls class="img-fluid mb-2"></video>
                            @endif
                        </div>
                        @empty
                        <p>No media found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
