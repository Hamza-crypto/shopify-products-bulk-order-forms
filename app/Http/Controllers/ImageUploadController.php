<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImageUrlsImport;
use App\Exports\ImageUrlsExport;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function showForm()
    {
        return view('upload-form');
    }

    public function upload(Request $request)
    {

        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);
        dd($request->all());
        $path = $request->file('file')->store('temp');
        $filePath = storage_path('app/' . $path);

        $imageUrls = Excel::toArray(new ImageUrlsImport(), $filePath)[0];

        // Process the images
        $processedData = [];
        foreach ($imageUrls as $row) {
            $url = $row[0];
            $imageName = basename($url);
            $imagePath = 'images/' . $imageName;

            // Download the image
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, ['sink' => storage_path('app/' . $imagePath)]);

            $processedData[] = [$url, asset('storage/' . $imagePath)];
        }
        dd('da');
        $export = new ImageUrlsExport($processedData);
        $exportPath = 'exports/processed_images.xlsx';
        Excel::store($export, $exportPath);

        return response()->download(storage_path('app/' . $exportPath))->deleteFileAfterSend(true);
    }
}
