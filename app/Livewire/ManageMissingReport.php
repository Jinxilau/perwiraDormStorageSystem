<?php

namespace App\Livewire;

use App\Models\MissingReport;
use App\Models\StorageRoom;
use App\Models\User;
use App\Notifications\MissingReportMadeNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Manage Missing Report')]
#[Lazy()]
class ManageMissingReport extends Component
{
    // Filter Options
    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $room = '';
    #[Url(except: '')]
    public $statusFilter = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    // Selection
    public $selectedReports = []; # arr report id
    public $selectAll = false;

    // Modal Data
    public $modalData; # report id for modal
    public $modalData2; # report id for modal status 

    // MODAL FORM
    #[Validate('required|in:investigating,found,lost,pending,dismissed')]
    public $newStatus;
    #[Validate('nullable|max:2000')]
    public $adminNote;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedReports = $this->missingReports->pluck('id')->toArray();
        } else {
            $this->selectedReports = [];
        }
    }

    public function updatedSelectedReports()
    {
        $this->selectAll = count($this->selectedReports) === $this->missingReports->count();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->statusFilter = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = '';
        $this->sortName = '';
        $this->paginationInt = 10;
    }

    public function updateStatus()
    {
        $this->validate();

        try {
            $report = MissingReport::find($this->modalData2);
            if (!$report) {
                $this->dispatch('show_toast', message: 'Missing Report not found.', type: 'fail');
                return;
            }

            $report->update([
                'status' => $this->newStatus,
                'admin_note' => $this->adminNote,
            ]);

            $this->dispatch('show_toast', message: 'Missing Report status updated successfully.', type: 'success');

            // Reset modal data
            $this->reset(['modalData2', 'newStatus', 'adminNote']);
            $this->dispatch('close_modal');
        } catch (\Exception $e) {
            $this->dispatch('show_toast', message: "Something went wrong. Please try again.", type: "fail");
            Log::error($e->getMessage());
        }
    }

    #[Computed()]
    public function missingReports()
    {
        $query = MissingReport::query()->with([
            'storedItems.storageApplication' => function ($q) {
                $q->with(['openArea.storageRoom', 'locker.storageRoom', 'applicant']);
            },
        ]);

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->whereHas("applicant", function ($userQuery) {
                    $userQuery->where('name', 'like', "%" . $this->search . "%")->orWhere('matric_no', 'like', '%' . $this->search . "%");
                });
            });
        }

        if ($this->room) {
            $query->where(function ($q) {
                $q->whereHas("openArea.storageRoom", function ($roomQuery) {
                    $roomQuery->where('id', $this->room);
                })->orWhereHas("locker.storageRoom", function ($roomQuery) {
                    $roomQuery->where('id', $this->room);
                });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->fromRange && $this->toRange) {
            $query->whereBetween('create_at', [$this->fromRange, $this->toRange]);
        } elseif ($this->fromRange) {
            $query->whereDate('create_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('create_at', '<=', $this->toRange);
        }

        if ($this->sortDate === 'latest') {
            $query->latest();
        } elseif ($this->sortDate === 'oldest') {
            $query->oldest();
        }

        if ($this->sortName) {
            $query->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'storage_applications.applicant_id'),
                $this->sortName === 'desc' ? 'desc' : 'asc'
            );
        }

        return $query->paginate($this->paginationInt);
    }

    #[Computed]
    public function reportCounts()
    {
        return MissingReport::selectRaw("
            COUNT(*) as total,
            SUM(status = 'under_investigation') as under_investigation,
            SUM(status = 'resolved') as resolved,
            SUM(status = 'pending') as pending,
            SUM(status = 'dismissed') as dismissed
        ")->first();
    }

    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }

    public function render()
    {
        return view('livewire.manage-missing-report');
    }
}
