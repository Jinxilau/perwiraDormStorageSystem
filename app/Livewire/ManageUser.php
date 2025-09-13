<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Admin - User')]
class ManageUser extends Component
{
    use WithPagination;

    public $selectedUsers = [];
    public $selectAll = false;
    public $user; # Specific User's Modal

    #[Validate('required|in:approved,pending,rejected')]
    public $userStatus;
    #[Validate('required|in:administrator,staff,student')]
    public $userRole;

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

    public function approveUser($userId)
    {
        try {
            User::findOrFail($userId)->update(['application_status' => 'approved']);

            $this->dispatch('user-updated', message: 'User approved successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('user-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }
    public function rejectUser($userId)
    {
        try {
            User::findOrFail($userId)->update(['application_status' => 'rejected']);

            $this->dispatch('user-updated', message: 'User rejected successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('user-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function bulkApprove()
    {
        try {
            User::whereIn('id', $this->selectedUsers)->update(['application_status' => 'approved']);

            $this->dispatch('user-updated', message: 'Users approved successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('user-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }
    public function bulkReject()
    {
        try {
            User::whereIn('id', $this->selectedUsers)->update(['application_status' => 'rejected']);

            $this->dispatch('user-updated', message: 'Users rejected successfully!', type: 'success');
            $this->reset();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('user-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    #[On('view-user')]
    public function viewModal($id)
    {
        $this->user = $this->users->where('id', $id)->first();

        if (!empty($this->user)) {
            $this->userStatus = $this->user->application_status;
            $this->userRole = $this->user->userRole->role_name;
            $this->dispatch('display_modal');
        } else {
            $this->dispatch('user-updated', message: 'Something went wrong.', type: 'fail');
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

        $this->dispatch('user-updated', message: "Users {$this->user->id} updated.", type: 'fail');
    }

    #[Computed()]
    public function users()
    {
        return User::with(['userRole', 'applyStorage'])->paginate(15);
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
