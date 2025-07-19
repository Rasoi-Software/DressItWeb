<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Look;
use App\Models\LookMedia;

class LookController extends Controller
{
   public function index()
{
    $looks = Look::with('media')->orderBy('created_at', 'desc')->paginate(10);
    return view('admin.looks.index', compact('looks'));
}

public function show($id)
{
    $look = Look::with('media')->findOrFail($id);
    return view('admin.looks.show', compact('look'));
}

public function destroy($id)
{
    $look = Look::findOrFail($id);
    $look->delete();
    return redirect()->route('admin.looks.index')->with('success', 'Look deleted successfully.');
}

}

