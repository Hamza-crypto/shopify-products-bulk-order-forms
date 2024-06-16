<?php


// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductUpload;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function showUploadForm()
    {
        return view('pages.admin.upload');
    }

    public function uploadProducts(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv',
        ]);

        $file = $request->file('file');
        $domain = $request->input('domain');
        $filePath = $file->store('uploads');

        $uniqueId = Str::uuid();

        ProductUpload::create([
            'unique_id' => $uniqueId,
            'file_path' => $filePath,
            'domain' => $domain,
        ]);

        $url = sprintf("<strong>%s</strong>", route('customer.products', ['unique_id' => $uniqueId]));


        return back()->with('success', 'File uploaded successfully! Share this URL with your customer: ' . $url);

    }
}
