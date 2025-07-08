<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LookController extends Controller
{
    // ✅ Create Look
    public function storeWithoutLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'set_goal' => 'required|string|max:255',
            'device_id' => 'required',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'media.*' => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $look = Look::create([
            'set_goal' => $request->set_goal,
            'description' => $request->description,
            'location' => $request->location,
            'device_id' => $request->device_id,
            'status' => 'draft'
        ]);



        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                $baseName = uniqid() . '_' . pathinfo($originalName, PATHINFO_FILENAME);
                $baseName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
                $baseName = strtolower($baseName);
                $fileName = $baseName . '.' . $extension;

                $filePath = 'looks/media/' . $fileName;

                try {
                    Storage::disk('s3')->put($filePath, file_get_contents($file), ['visibility' => 'public']);

                    $mimeType = $file->getMimeType();
                    $type = str_contains($mimeType, 'video') ? 'video' : 'image';

                    $look->media()->create([
                        'media_path' => $filePath,
                        'media_type' => $type,
                    ]);
                } catch (\Exception $e) {
                    // Handle error (log, return response, etc.)
                }
            }
        }

        return returnSuccess('Draft created successfully',  $look->load('media'));
    }

    public function afterLoginAssignDrafts(Request $request)
    {
        $request->validate([
            'device_id' => 'required|uuid',
            'status' => 'required',
        ]);

        $user = auth()->user();

        Look::where('device_id', $request->device_id)
            ->whereNull('user_id')
            ->update([
                'user_id' => $user->id,
                'status' => $request->status,
            ]);

        return returnSuccess('Drafts assigned successfully');
    }

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
        // if ($request->hasFile('media')) {
        //     foreach ($request->file('media') as $file) {
        //         $path = $file->store('looks/media',  [
        //         'disk' => 's3',
        //         'visibility' => 'public',
        //     ]);
        //         $mimeType = $file->getMimeType();
        //         $type = str_contains($mimeType, 'video') ? 'video' : 'image';

        //         $look->media()->create([
        //             'media_path' => $path,
        //             'media_type' => $type,
        //         ]);
        //     }
        // }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                $baseName = uniqid() . '_' . pathinfo($originalName, PATHINFO_FILENAME);
                $baseName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
                $baseName = strtolower($baseName);
                $fileName = $baseName . '.' . $extension;

                $filePath = 'looks/media/' . $fileName;

                try {
                    Storage::disk('s3')->put($filePath, file_get_contents($file), ['visibility' => 'public']);

                    $mimeType = $file->getMimeType();
                    $type = str_contains($mimeType, 'video') ? 'video' : 'image';

                    $look->media()->create([
                        'media_path' => $filePath,
                        'media_type' => $type,
                    ]);
                } catch (\Exception $e) {
                    // Handle error (log, return response, etc.)
                }
            }
        }

        return returnSuccess('Look created successfully.', $look->load('media'));
    }

    // ✅ Get All Looks
    public function index()
    {
        $looks = Look::with('media', 'user')->where('user_id', auth()->id())->latest()->paginate(5);
        return returnSuccess('Looks fetched successfully.', $looks);
    }
    // ✅ Get All Looks
    public function all_looks(Request $request, $user_id = null)
    {
        $query = Look::with('media', 'user')->latest();

        if (!is_null($user_id)) {
            $query->where('user_id', $user_id);
        }
        $query->where('status', 'published');
        
        $looks = $query->paginate(5);

        return returnSuccess('Looks fetched successfully.', $looks);
    }

    // ✅ Get All Looks
    public function search_look(Request $request)
    {
        $search = $request->q;

        $looks = Look::with('media', 'user')
        ->where('status', 'published')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('set_goal', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->paginate(50);

        return returnSuccess('Looks fetched successfully.', $looks);
    }


    // ✅ Show Single Look
    public function show($id)
    {
        $look = Look::with('media', 'user')->where('id', $id)->where('user_id', auth()->id())->first();

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

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);

                $baseName = uniqid() . '_' . pathinfo($originalName, PATHINFO_FILENAME);
                $baseName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $baseName);
                $baseName = strtolower($baseName);
                $fileName = $baseName . '.' . $extension;

                $filePath = 'looks/media/' . $fileName;

                try {
                    Storage::disk('s3')->put($filePath, file_get_contents($file), ['visibility' => 'public']);

                    $mimeType = $file->getMimeType();
                    $type = str_contains($mimeType, 'video') ? 'video' : 'image';

                    $look->media()->create([
                        'media_path' => $filePath,
                        'media_type' => $type,
                    ]);
                } catch (\Exception $e) {
                    // Handle error (log, return response, etc.)
                }
            }
        }
        return returnSuccess('Look updated successfully.', $look->load('media'));
    }

    // ✅ Delete Look

    public function destroy($id)
    {
        //DB::enableQueryLog();
        $look = Look::where('id', $id)->where('user_id', auth()->id())->first();
        // dd(DB::getQueryLog());

        if (!$look) {
            return returnError('Look not found.');
        }

        // Delete media files from S3
        foreach ($look->media as $media) {
            Storage::disk('s3')->delete($media->media_path); // ✅ delete from S3
            $media->delete(); // ✅ delete DB entry
        }

        $look->delete(); // ✅ delete the look record

        return returnSuccess('Look deleted successfully.');
    }
}
