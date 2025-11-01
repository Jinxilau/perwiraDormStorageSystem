<?php

namespace App\Livewire;

use App\Models\MissingReport;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Manage Missing Report')]
#[Lazy()]
class ManageMissingReport extends Component
{
    public $selectedReports = [];

    public function investigating() {}
    public function resolved() {}
    public function closed() {}

    #[Computed()]
    public function missingReports()
    {
        return MissingReport::with([
            'storedItems.storageApplication' => function ($q) {
                $q->with(['openArea.storageRoom', 'locker.storageRoom', 'applicant']);
            },
        ])->paginate(10);
    }

    public function render()
    {
        return view('livewire.manage-missing-report');
    }
}
