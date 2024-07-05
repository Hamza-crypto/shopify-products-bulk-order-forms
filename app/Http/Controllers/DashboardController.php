<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileEntry;

class DashboardController extends Controller
{
    public function showProgress()
    {
        $files = FileEntry::all();

        return view('pages.dashboard.index', compact('files'));
    }
}