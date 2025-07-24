<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LookController extends Controller
{
    public function index()
    {
        $looks = Look::with('media')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.looks.index', compact('looks'));
    }

    public function show($id)
    {
        $look = Look::with('media')->find($id);
        if (empty($look)) {
            return redirect()->route('admin.looks.index')->with('error', 'Look not found.');
        }
        return view('admin.looks.show', compact('look'));
    }

    public function destroy($id)
    {


        // Find the Look with related media
        $look = Look::with('media')
            ->where('id', $id)
            ->first();

        if (!$look) {
            return redirect()->route('admin.looks.index')->with('error', 'Look not found.');
        }

        DB::transaction(function () use ($look) {
            foreach ($look->media as $media) {
                // Delete file from S3
                Storage::disk('s3')->delete($media->media_path);

                // Delete media DB record
                $media->delete();
            }

            // Delete the Look record
            $look->delete();
        });

        $look = Look::find($id);
        if (!empty($look)) {
            $look->delete();
        }
        return redirect()->route('admin.looks.index')->with('success', 'Look deleted successfully.');
    }
}
