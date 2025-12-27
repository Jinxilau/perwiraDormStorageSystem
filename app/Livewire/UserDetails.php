<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('User Details')]
#[Lazy()]
class UserDetails extends Component
{
    public User $user;

    // User properties
    public string $name;
    public string $email;
    public ?string $matricNo;
    public ?string $gender;
    public ?string $phoneNumber;
    public ?int $yearOfStudy;
    public string $applicationStatus;
    public int $roleId;

    public $roles;

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
            'matricNo' => ['nullable', 'string', Rule::unique('users', 'matric_no')->ignore($this->user->id)],
            'gender' => ['nullable', 'in:male,female,other'],
            'phoneNumber' => ['nullable', 'string', Rule::unique('users', 'phone_number')->ignore($this->user->id)],
            'yearOfStudy' => ['nullable', 'integer', 'min:1', 'max:7'],
            'applicationStatus' => ['required', 'in:pending,approved,rejected'],
            'roleId' => ['required', 'exists:user_roles,id'],
        ];
    }

    public function mount(User $user)
    {
        $this->user = $user->load(['userRole', 'applyStorage']);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->matricNo = $user->matric_no;
        $this->gender = $user->gender ?? '';
        $this->phoneNumber = $user->phone_number;
        $this->yearOfStudy = $user->year_of_study;
        $this->applicationStatus = $user->application_status;
        $this->roleId = $user->role_id;

        // Load all available roles
        $this->roles = UserRole::all();
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'matric_no' => $this->matricNo,
            'gender' => $this->gender ?: null,
            'phone_number' => $this->phoneNumber,
            'year_of_study' => $this->yearOfStudy,
            'application_status' => $this->applicationStatus,
            'role_id' => $this->roleId,
        ]);

        $this->dispatch('show_toast', message: 'User updated successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.user-details');
    }
}
