<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();
        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        City::create($request->only('name'));

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $city->update($request->only('name'));

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            if ($data[0] != 'Name' || $data[0] != 'City Name') {
                City::firstOrCreate(['name' => $data[0]]);
            }
        }

        fclose($file);

        return redirect()->route('admin.cities.index')->with('success', 'Cities imported successfully.');
    }
    public function export(): StreamedResponse
    {
        $cities = City::select('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="cities.csv"',
        ];

        $callback = function () use ($cities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['City Name']); // Header

            foreach ($cities as $city) {
                fputcsv($file, [$city->name]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
