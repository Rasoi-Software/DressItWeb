<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class LookController extends Controller
{
    // ✅ Create Look
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'set_goal' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $look = Look::create([
            'user_id' => auth()->id(),
            'set_goal' => $request->set_goal,
            'description' => $request->description,
            'location' => $request->location,
        ]);

        // Upload media
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('looks/media', 'public');
                $mimeType = $file->getMimeType();
                $type = str_contains($mimeType, 'video') ? 'video' : 'image';

                $look->media()->create([
                    'media_path' => $path,
                    'media_type' => $type,
                ]);
            }
        }

        return returnSuccess('Look created successfully.', $look->load('media'));
    }

    // ✅ Get All Looks
    public function index()
    {
        $looks = Look::with('media')->where('user_id', auth()->id())->latest()->get();
        return returnSuccess('Looks fetched successfully.', $looks);
    }
    // ✅ Get All Looks
    public function all_looks($user_id = null)
    {
        $query = Look::with('media')->latest();

        if (!is_null($user_id)) {
            $query->where('user_id', $user_id);
        }

        $looks = $query->get();

        return returnSuccess('Looks fetched successfully.', $looks);
    }

    // ✅ Get All Looks
    public function search_look(Request $request)
    {
        $search = $request->q;

        $looks = Look::with('media')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('set_goal', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->get();

        return returnSuccess('Looks fetched successfully.', $looks);
    }


    // ✅ Show Single Look
    public function show($id)
    {
        $look = Look::with('media')->where('id', $id)->where('user_id', auth()->id())->first();

        if (!$look) {
            return returnError('Look not found.');
        }

        return returnSuccess('Look retrieved.', $look);
    }

    // ✅ Update Look
    public function update(Request $request, $id)
    {
        $look = Look::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$look) {
            return returnError('Look not found.');
        }
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'set_goal' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }


        // Update look fields
        $look->set_goal = $request->set_goal;
        $look->description = $request->description;
        $look->location = $request->location;
        $look->save();

        // Upload new media if provided
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('looks/media', 'public');
                $mimeType = $file->getMimeType();
                $type = str_contains($mimeType, 'video') ? 'video' : 'image';

                $look->media()->create([
                    'media_path' => $path,
                    'media_type' => $type,
                ]);
            }
        }

        return returnSuccess('Look updated successfully.', $look->load('media'));
    }

    // ✅ Delete Look
    public function destroy($id)
    {
        $look = Look::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$look) {
            return returnError('Look not found.');
        }

        // Delete media files from storage
        foreach ($look->media as $media) {
            Storage::disk('public')->delete($media->media_path);
            $media->delete();
        }

        $look->delete();

        return returnSuccess('Look deleted successfully.');
    }
}
