<?php

namespace App\Livewire;

use App\Models\Semester;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Perwira Dorm Storage System')]
#[Lazy()]
class AccountApplication extends Component
{
    public $name = '';
    // public $first_name = '';
    // public $last_name = '';
    public $matric_no = '';
    public $gender = '';
    public $year_of_study = '';
    // public $semester = '';
    public $phone_number = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    public $years = [];
    // public $semesters = [];
    public $genders = [];

    public function mount()
    {
        $this->years = [
            1 => 'Year 1',
            2 => 'Year 2',
            3 => 'Year 3',
            4 => 'Year 4',
            5 => 'Year 5',
        ];

        // $this->semesters = Semester::where('is_current', true)
        //     ->orderBy('semester_no', 'asc')
        //     ->get();

        $this->genders = [
            'male' => 'Male',
            'female' => 'Female',
            'other' => 'Other',
        ];
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // 'first_name' => 'required|string|max:255',
            // 'last_name' => 'required|string|max:255',
            'matric_no' => 'required|string|max:255|unique:users,matric_no',
            'gender' => 'required|in:male,female,other',
            'year_of_study' => 'required|integer|between:1,5',
            // 'semester' => 'required|exists:semesters,id',
            'phone_number' => 'required|string|regex:/^01[0-9]-[0-9]{7,8}$/|unique:users,phone_number',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Name is required.',
            // 'first_name.required' => 'First name is required.',
            // 'last_name.required' => 'Last name is required.',
            'matric_no.required' => 'Matric number is required.',
            'matric_no.unique' => 'This matric number is already registered.',
            'gender.required' => 'Please select your gender.',
            'year_of_study.required' => 'Please select your year of study.',
            // 'semester.required' => 'Please select your semester.',
            'phone_number.required' => 'Phone number is required.',
            'phone_number.unique' => 'This phone number is already registered.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function clearForm()
    {
        $this->reset();
        $this->resetValidation();
        session()->flash('message', 'Form cleared successfully.');
    }

    public function submit()
    {
        $this->validate();

        try {
            $studentRole = UserRole::where('role_name', 'student')->first();
            if (!$studentRole) {
                $studentRole = UserRole::find(3);
            }

            User::create([
                'name' => $this->name,
                'role_id' => $studentRole->id,
                // 'semester_id' => $this->semester,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'matric_no' => $this->matric_no,
                'phone_number' => $this->phone_number,
                'gender' => $this->gender,
                'year_of_study' => $this->year_of_study,
            ]);

            session()->flash('success', 'Account application submitted successfully! Please wait for admin approval.');
            $this->reset();
            $this->resetValidation();
            $this->redirectRoute('login');
        } catch (\Exception $e) {
            Log::error('Account Application Submission Error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your application. Please try again.');
        }
    }

    public function placeholder()
    {
        return view('livewire.placeholder.spinner');
    }

    public function render()
    {
        return view('livewire.account-application');
    }
}
