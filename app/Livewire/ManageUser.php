<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Manage User Account')]
#[Lazy()]
class ManageUser extends Component
{
    use WithPagination;

    public $selectedUsers = [];
    public $selectAll = false;
    public $userId; # Modal

    #[Validate('required|in:approved,pending,rejected')]
    public $userStatus;
    #[Validate('required|in:administrator,staff,student')]
    public $userRole;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $role = '';
    #[Url(except: '')]
    public $accStatus = '';
    #[Url(except: '')]
    public $userGender = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: '')]
    public $sortDate = 'latest';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        $this->selectAll = count($this->selectedUsers) === $this->users->count();
    }

    public function updatedPaginationInt()
    {
        $this->reset(['selectAll', 'selectedReports']);
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->role = '';
        $this->accStatus = '';
        $this->userGender = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = 'latest';
        $this->sortName = '';

        $this->paginationInt = 10; # Pagination Amount
    }

    public function approveUser($userId)
    {
        try {
            User::findOrFail($userId)->update(['application_status' => 'approved']);

            $this->dispatch('show_toast', message: 'User approved successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }
    public function rejectUser($userId)
    {
        try {
            User::findOrFail($userId)->update(['application_status' => 'rejected']);

            $this->dispatch('show_toast', message: 'User rejected successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function bulkApprove()
    {
        try {
            User::whereIn('id', $this->selectedUsers)->update(['application_status' => 'approved']);

            $this->dispatch('show_toast', message: 'Users approved successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }
    public function bulkReject()
    {
        try {
            User::whereIn('id', $this->selectedUsers)->update(['application_status' => 'rejected']);

            $this->dispatch('show_toast', message: 'Users rejected successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function editUser()
    {
        $this->validate();

        $role = match ($this->userRole) {
            'administrator' => 1,
            'staff' => 2,
            'student' => 3,
        };

        $this->user->update([
            'application_status' => $this->userStatus,
            'role_id' => $role,
        ]);

        $this->dispatch('show_toast', message: "Users {$this->user->id} updated.", type: 'fail');
    }

    #[Computed()]
    public function users()
    {
        $query = User::query()->with(['userRole', 'applyStorage']);

        if ($this->search) {
            $query->where(function ($q) {

                if (is_numeric($this->search)) {
                    $q->where('id', $this->search);
                }

                $q->where('matric_no', 'like', "%" . $this->search . "%")->orWhere('name', 'like', "%" . $this->search . "%");
            });
        }

        if (!empty($this->role)) {
            $query->where('role_id', is_numeric($this->role));
        }

        if (!empty($this->accStatus)) {
            $query->where('application_status', $this->accStatus);
        }

        if (!empty($this->userGender)) {
            $query->where('gender', $this->userGender);
        }

        if ($this->fromRange && $this->toRange) {
            $query->whereBetween('created_at', [$this->fromRange, $this->toRange]);
        } elseif ($this->fromRange) {
            $query->whereDate('created_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('created_at', '<=', $this->toRange);
        }

        if ($this->sortDate === 'latest') {
            $query->oldest();
        } elseif ($this->sortDate === 'oldest') {
            $query->latest();
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

    #[Computed()]
    public function roles()
    {
        return UserRole::all();
    }

    public function render()
    {
        return view('livewire.manage-user');
    }
}
