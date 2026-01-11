<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.student')]
#[Title('My Missing Report')]
#[Lazy()]
class MyReport extends Component
{
    public function cancelReport($reportId)
    {
        $report = MissingReport::find($reportId);

        // Verify the report exists
        if (!$report) {
            $this->dispatch('show_toast', message: 'Report not found.', type: 'error');
            return;
        }

        // Verify the report belongs to the current user
        $reportBelongsToUser = $this->storageApplication
            ->storedItems
            ->pluck('missingReport')
            ->filter()
            ->contains('id', $reportId);

        if (!$reportBelongsToUser) {
            $this->dispatch('show_toast', message: 'Unauthorized action.', type: 'error');
            return;
        }

        // Only allow cancellation if status is pending
        if ($report->status !== 'pending') {
            $this->dispatch('show_toast', message: 'Only pending reports can be cancelled.', type: 'error');
            return;
        }

        // Delete the report
        $report->delete();

        // Clear computed properties to refresh data
        unset($this->storageApplication);
        unset($this->reports);

        $this->dispatch('show_toast', message: 'Report cancelled successfully.', type: 'success');
    }

    #[Computed]
    public function storageApplication()
    {
        return StorageApplication::query()
            ->where('applicant_id', Auth::id())
            ->with([
                'semester',
                'approver',
                'openArea',
                'locker',
                'storedItems.missingReport',
            ])
            ->first();
    }

    #[Computed]
    public function reports()
    {
        if (! $this->storageApplication) {
            return collect();
        }

        return $this->storageApplication
            ->storedItems
            ->pluck('missingReport')
            ->filter()
            ->unique('id')
            ->values();
    }

    public function render()
    {
        return view('livewire.my-report');
    }
}
