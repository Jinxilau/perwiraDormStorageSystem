<?php

namespace App\Livewire;

use App\Models\MissingReport;
use App\Notifications\MissingReportStatusNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Missing Details')]
#[Lazy()]
class MissingDetails extends Component
{
    public $application;
    public $report;
    public $status;
    public $adminNote;

    public $showStatusModal = false;
    public $showNoteModal = false;

    protected $rules = [
        'status' => 'required|in:pending,investigating,found,lost,dismissed',
        'adminNote' => 'nullable|string|max:5000',
    ];

    public function mount(MissingReport $report)
    {
        $this->report = $report->load([
            'semester',
            'storedItems.storageApplication.applicant',
            'storedItems.storageApplication.openArea.storageRoom',
            'storedItems.storageApplication.locker.storageRoom'
        ]);

        $this->status = $this->report->status;
        $this->adminNote = $this->report->admin_note;
        $this->application = $this->report
            ->storedItems
            ->first()
            ?->storageApplication;
    }

    public function openStatusModal()
    {
        $this->status = $this->report->status;
        $this->showStatusModal = true;
    }

    public function closeStatusModal()
    {
        $this->showStatusModal = false;
        $this->resetValidation('status');
    }

    public function openNoteModal()
    {
        $this->adminNote = $this->report->admin_note;
        $this->showNoteModal = true;
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->resetValidation('adminNote');
    }

    public function updateStatus()
    {
        $this->validate(['status' => 'required|in:pending,investigating,found,lost,dismissed']);

        $oldStatus = $this->report->status;

        // Prevent duplicate notifications
        if ($oldStatus === $this->status) {
            return;
        }

        $this->report->update([
            'status' => $this->status
        ]);

        // Notify student
        $student = $this->report
            ->storedItems
            ->first()
            ?->storageApplication
            ?->applicant;

        if ($student) {
            $student->notify(
                new MissingReportStatusNotification($this->report, $this->status)
            );
        }

        $this->closeStatusModal();

        $this->dispatch('show_toast', message: 'Report status updated successfully!', type: 'success');

        $this->report->refresh();
    }

    public function quickUpdateStatus($newStatus)
    {
        if (!in_array($newStatus, ['pending', 'investigating', 'found', 'lost', 'dismissed'])) {
            return;
        }

        $oldStatus = $this->report->status;

        if ($oldStatus === $newStatus) {
            return;
        }

        $this->report->update([
            'status' => $newStatus
        ]);

        $student = $this->report
            ->storedItems
            ->first()
            ?->storageApplication
            ?->applicant;

        if ($student) {
            $student->notify(
                new MissingReportStatusNotification($this->report, $newStatus)
            );
        }

        $this->dispatch('show_toast', message: 'Report status updated to ' . ucfirst($newStatus) . '!', type: 'success');

        $this->report->refresh();
    }


    public function updateNote()
    {
        $this->validate(['adminNote' => 'nullable|string|max:5000']);

        $this->report->update([
            'admin_note' => $this->adminNote
        ]);

        $this->closeNoteModal();

        $this->dispatch('show_toast', message: 'Admin note updated successfully!', type: 'success');

        $this->report->refresh();
    }

    public function getStatusColorProperty()
    {
        return match ($this->report->status) {
            'pending' => 'warning',
            'investigating' => 'info',
            'found' => 'success',
            'lost' => 'danger',
            'dismissed' => 'secondary',
            default => 'secondary'
        };
    }

    public function getLastSeenTextProperty()
    {
        if ($this->report->last_seen_type === 'specific_date') {
            return \Carbon\Carbon::parse($this->report->last_seen_date)->format('d M Y');
        } elseif ($this->report->last_seen_type === 'dont_remember') {
            return "Don't Remember";
        } else {
            return ucwords(str_replace('_', ' ', $this->report->last_seen_range));
        }
    }

    public function getDiscoveredMissingTextProperty()
    {
        if ($this->report->discovered_missing_type === 'specific_date') {
            return \Carbon\Carbon::parse($this->report->discovered_missing_date)->format('d M Y');
        } elseif ($this->report->discovered_missing_type === 'dont_remember') {
            return "Don't Remember";
        } else {
            return ucwords(str_replace('_', ' ', $this->report->discovered_missing_range));
        }
    }

    public function exportPdf()
    {
        if (class_exists('\Debugbar')) {
            debugbar()->disable();
        }

        $data = [
            'report' => $this->report->load(['semester', 'storedItems']),
            'statusColor' => $this->statusColor,
            'lastSeenText' => $this->lastSeenText,
            'discoveredMissingText' => $this->discoveredMissingText,
        ];

        $pdf = Pdf::loadView('reports.missing-report-pdf', $data)
            ->setPaper('a4', 'portrait');

        return response()->streamDownload(
            function () use ($pdf) {
                echo $pdf->output();
            },
            'missing-report-' . $this->report->id . '-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.missing-details');
    }
}
