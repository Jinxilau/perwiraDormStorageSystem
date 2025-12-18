<?php

namespace App\Http\Controllers;

use App\Models\StorageApplication;
use Illuminate\Http\Request;

class StorageApplicationDetailController extends Controller
{
    public function show(StorageApplication $application)
    {
        $application->load(['applicant', 'storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester',]);

        return view('admin.storage-details', compact('application'));
    }
}
