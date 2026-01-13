<?php

namespace App\Livewire;

use App\Models\MissingReport;
use App\Models\Semester;
use App\Models\StorageRoom;
use App\Notifications\MissingReportStatusNotification;
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
    public $semester = '';
    #[Url(except: '')]
    public $statusFilter = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = '';

    public $paginationInt = 10;

    // Modal Data
    public $modalData; # report id for modal
    public $modalData2; # report id for modal status 

    // MODAL FORM
    #[Validate('required|in:investigating,found,lost,pending,dismissed')]
    public $newStatus;
    #[Validate('nullable|max:2000')]
    public $adminNote;

    protected function resetsPage()
    {
        // If any of these properties change, go back to page 1
        return ['search', 'room', 'semester', 'statusFilter', 'fromRange', 'toRange'];
    }

    public function mount()
    {
        // Assuming your Semester model has a way to identify the active one, 
        // e.g., an 'is_active' column or scope.
        $activeSemester = Semester::where('is_current', true)->first();

        if ($activeSemester) {
            $this->semester = $activeSemester->id;
        }
    }

    public function updatedPaginationInt()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->room = '';
        $this->statusFilter = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = 'latest';

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

            $student = $this->report
                ->storedItems
                ->first()
                ?->storageApplication
                ?->applicant;

            if ($student) {
                $student->notify(
                    new MissingReportStatusNotification($report, $this->newStatus)
                );
            }


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
                $q->with(['openArea.storageRoom', 'locker.storageRoom', 'applicant', 'semester']);
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

        if ($this->semester) {
            $query->where('semester_id', $this->semester);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->fromRange && $this->toRange) {
            $query->whereBetween('created_at', [$this->fromRange, $this->toRange]);
        } elseif ($this->fromRange) {
            $query->whereDate('created_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('created_at', '<=', $this->toRange);
        }

        if ($this->sortDate === 'latest') {
            $query->latest();
        } elseif ($this->sortDate === 'oldest') {
            $query->oldest();
        }

        return $query->paginate($this->paginationInt);
    }

    // Modal
    #[Computed()]
    public function modalReport2()
    {
        if ($this->modalData2 === null) return null;

        return MissingReport::with([
            'storedItems.storageApplication.applicant',
            'storedItems.storageApplication.locker.storageRoom',
            'storedItems.storageApplication.openArea.storageRoom',
        ])->find($this->modalData2);
    }

    // Metrics for dashboard cards
    #[Computed]
    public function reportCounts()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        if (! $currentSemesterId) {
            return (object) [
                'total' => 0,
                'under_investigation' => 0,
                'resolved' => 0,
                'pending' => 0,
                'dismissed' => 0,
            ];
        }

        return MissingReport::where('semester_id', $currentSemesterId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'under_investigation' THEN 1 ELSE 0 END) as under_investigation,
                SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed
            ")->first();
    }
    #[Computed()]
    public function thisSemester()
    {
        $currentSemesterId = Semester::where('is_current', true)->value('id');

        return MissingReport::where('status', 'pending')
            ->where('semester_id', $currentSemesterId)
            ->count();
    }

    // Populate the filter dropdowns
    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }
    #[Computed()]
    public function semesters()
    {
        // Adjust sorting as needed (e.g., latest semester first)
        return Semester::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.manage-missing-report');
    }
}
