<?php

namespace App\Livewire;

use App\Models\ActivityLog as ModelsActivityLog;
use App\Models\User;
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
    public $role = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = '';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

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

    #[Computed()]
    public function logs()
    {
        $query = ModelsActivityLog::with(['user.userRole']);

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->whereHas("user", function ($userQuery) {
                    $userQuery->where(function($q) {
                        $q->where('name', 'like', "%" . $this->search . "%")
                          ->orWhere('matric_no', 'like', '%' . $this->search . "%");
                    });
                });
            });
        }
        
        if (!empty($this->role)) {
            $query->whereHas('user.userRole', function ($roleQuery) {
                $roleQuery->where('role_id', $this->role);
            });
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

        if ($this->sortName) {
            $query->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'activity_logs.user_id'),
                $this->sortName === 'desc' ? 'desc' : 'asc'
            );
        }

        return $query->paginate($this->paginationInt);
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
