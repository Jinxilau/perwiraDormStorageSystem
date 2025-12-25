<?php

namespace App\Http\Controllers;

use App\Models\MissingReport;
use Illuminate\Http\Request;

class MissingReportDetailController extends Controller
{
    public function show(MissingReport $report)
    {
        $report->load([
            'semester',
            'storedItems.storageApplication.applicant',
            'storedItems.storageApplication.openArea.storageRoom',
            'storedItems.storageApplication.locker.storageRoom'
        ]);

        return view('admin.missing-details', compact('report'));
    }
}
