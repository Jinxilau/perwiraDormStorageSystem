<?php

namespace App\Livewire;

use App\Models\ActivityLog as ModelsActivityLog;
use App\Models\Semester;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityLog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $semester = '';
    #[Url(except: '')]
    public $role = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = 'latest';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    protected function resetsPage()
    {
        // If any of these properties change, go back to page 1
        return ['search', 'semester', 'role', 'fromRange', 'toRange'];
    }

    public function updatedPaginationInt()
    {
        $this->resetPage();
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

    public function clearFilters()
    {
        $this->search = '';
        $this->role = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = 'latest';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    public function updated($property)
    {
        if (in_array($property, [
            'search',
            'semester',
            'role',
            'fromRange',
            'toRange',
            'sortDate',
            'sortName',
            'paginationInt',
        ])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function logs()
    {
        $query = ModelsActivityLog::query()
            ->with(['user.userRole', 'semester']);

        if ($this->search) {
            $query->where(function ($q) {
                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('matric_no', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->semester) {
            $query->where('semester_id', $this->semester);
        }

        if ($this->role) {
            $query->whereHas(
                'user.userRole',
                fn($q) =>
                $q->where('role_id', $this->role)
            );
        }

        if ($this->fromRange || $this->toRange) {
            $query->whereBetween('created_at', [
                $this->fromRange ? now()->parse($this->fromRange)->startOfDay() : now()->minValue(),
                $this->toRange ? now()->parse($this->toRange)->endOfDay() : now(),
            ]);
        }

        if ($this->sortDate === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        if ($this->sortName) {
            $query->join('users', 'users.id', '=', 'activity_logs.user_id')
                ->orderBy('users.name', $this->sortName)
                ->select('activity_logs.*');
        }

        return $query->paginate($this->paginationInt);
    }

    // Metrics for dashboard cards
    #[Computed()]
    public function todayLogs()
    {
        return ModelsActivityLog::whereDate('created_at', now())->count();
    }
    // New: Counts distinct users who performed an action today
    #[Computed()]
    public function activeUsersToday()
    {
        return ModelsActivityLog::whereDate('created_at', now())
            ->distinct('user_id')
            ->count('user_id');
    }
    // New: Finds the most frequent action type (e.g., 'Update', 'Create')
    protected function baseActionQuery()
    {
        return ModelsActivityLog::whereNotNull('id');
    }

    #[Computed()]
    public function createCount()
    {
        return $this->baseActionQuery()
            ->whereIn('action', ['created', 'store'])
            ->count();
    }

    #[Computed()]
    public function updateCount()
    {
        return $this->baseActionQuery()
            ->whereIn('action', ['updated', 'edit'])
            ->count();
    }

    #[Computed()]
    public function approveCount()
    {
        return $this->baseActionQuery()
            ->whereIn('action', ['approved', 'approve'])
            ->count();
    }

    #[Computed()]
    public function deleteCount()
    {
        return $this->baseActionQuery()
            ->whereIn('action', ['deleted', 'destroy'])
            ->count();
    }

    #[Computed()]
    public function rejectCount()
    {
        return $this->baseActionQuery()
            ->whereIn('action', ['rejected', 'reject'])
            ->count();
    }

    #[Computed()]
    public function openCount()
    {
        return $this->baseActionQuery()
            ->where('action', 'opened')
            ->count();
    }

    #[Computed()]
    public function closeCount()
    {
        return $this->baseActionQuery()
            ->where('action', 'closed')
            ->count();
    }

    // Populate the filter dropdowns
    #[Computed()]
    public function semesters()
    {
        // Adjust sorting as needed (e.g., latest semester first)
        return Semester::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.activity-log');
    }
}
