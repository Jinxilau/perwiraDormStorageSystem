<?php

namespace App\Livewire;

use App\Livewire\Forms\MissingItemForm;
use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MissingReport extends Component
{
    public $totalSteps = 3;
    public $currentStep = 1;
    public $showSuccessMessage = false;

    public MissingItemForm $form;

    public function mount()
    {
        // Initialize form
        $this->form->storageApplicationId = $this->studentApplications->first()->id;
    }

    #[Computed()]
    public function studentApplications()
    {
        return StorageApplication::with(['locker.storageRoom', 'openArea.StorageRoom', 'items'])
            ->where('applicant_id', Auth::user()->id)
            ->where('storage_application_status', 'approved') # only the approved storage can report missing item
            ->get();
    }

    #[Computed()]
    public function selectedApplication()
    {
        return $this->studentApplications->where('id', $this->form->storageApplicationId)->first();
    }

    #[Computed()]
    public function selectedItems() 
    {
        return $this->selectedApplication;
    }
    
    public function render()
    {
        return view('livewire.missing-report')->layout('layouts.student')->title('Student - Missing Item Report');
    }
}
