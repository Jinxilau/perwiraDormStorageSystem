<?php

namespace App\Livewire;

use App\Models\ScanLog as ModelsScanLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ScanLog extends Component
{
    use WithPagination;

    #[Computed()]
    public function logs()
    {
        return ModelsScanLog::with([
            'qrCode.storageApplication' => function ($q) {
                $q->with(['applicant', 'locker.storageRoom', 'openArea.storageRoom']);
            },
        ])->latest()->paginate(10);
    }

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.scan-log')->layout($layout)->title($title . ' - Scan Logs');
    }
}
