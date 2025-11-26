<?php

namespace App\Livewire;

use App\Models\ActivityLog as ModelsActivityLog;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;

    public $paginationInt;

    #[Computed()]
    public function logs()
    {
        return ModelsActivityLog::with(['user.userRole'])->paginate($this->paginationInt);
    }

    #[Computed()]
    public function todayLogs()
    {
        return ModelsActivityLog::whereDate('created_at', now())->count();
    }

    public function render()
    {
        return view('livewire.activity-log');
    }
}
