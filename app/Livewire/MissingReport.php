<?php

namespace App\Livewire;

use App\Livewire\Forms\MissingItemForm;
use App\Models\MissingReport as ModelsMissingReport;
use App\Models\Setting;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use App\Models\User;
use App\Notifications\MissingReportMadeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.student')]
#[Title('Report Missing Item')]
#[Lazy()]
class MissingReport extends Component
{
    use WithFileUploads;

    public $totalSteps = 4;
    public $currentStep = 1;
    public $showSuccessMessage = false;

    public MissingItemForm $form;

    public function mount()
    {
        // Initialize form with default value
        if ($this->studentApplications->isNotEmpty()) {
            $this->form->storageApplicationId = $this->studentApplications->first()->id;
        }

        $this->form->items = [
            0 => [
                'id' => null,
                'photo' => null
            ]
        ];

        $this->form->userEmail = Auth::user()->email;
        $this->form->userPhone = Auth::user()->phone_number;
    }

    // Step 1 : Select Application
    public function selectApplication($id)
    {
        $this->form->storageApplicationId = $id;
        $this->form->itemId = null;
    }

    public function removePhoto()
    {
        $this->form->photo = null;
        $this->dispatch('show_toast', message: 'Photo remove successfully!', type: 'success');
    }

    public function addItem()
    {
        // Save current item to arr
        try {
            $this->form->validateCurrentItem();
            $this->form->storeArr();

            // Setup new item
            $newIdx = count($this->form->items);

            $this->form->items[$newIdx] = [
                'id' => null,
                'photo' => null,
            ];

            $this->form->reset('itemId', 'photo');
            $this->form->itemIdx = $newIdx;
            $this->form->loadItem();

            $this->dispatch('show_toast', message: 'New item added successfully!', type: 'success');
        } catch (ValidationException $e) {
            $this->dispatch('show_toast', message: "Item cannot be added.", type: "fail");
            $this->addError('form.itemId', "Please select an item.");
        }
    }
    public function switchToItem($itemIdx)
    {
        $this->form->storeArr();
        $this->form->itemIdx = $itemIdx;
        $this->form->loadItem();
    }
    public function removeItem($itemIdx)
    {
        $this->form->itemId = null;
        $this->form->photo = null;
        unset($this->form->items[$itemIdx]);
        // Reset index of array
        $this->form->items = array_values($this->form->items);


        // Adjust index
        if ($this->form->itemIdx >= count($this->form->items)) {
            $this->form->itemIdx = count($this->form->items) - 1;
        }

        $this->form->loadItem();
        $this->dispatch('show_toast', message: "Item removed successfully!", type: "success");
    }

    // PROCEED 
    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }
    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }
    public function goToStep($step)
    {
        if ($step <= $this->currentStep || $step == 1) {
            $this->currentStep = $step;
        }
    }
    private function validateCurrentStep()
    { // TODO
        switch ($this->currentStep) {
            case 1:
                $this->form->validateStorage();
                break;
            case 2:
                $this->form->validateCurrentItem();
                $this->form->storeArr();
                $this->form->validateAllItems();
                break;
            case 3:
                $this->form->validateTime();
                break;
            case 4:
                $this->form->validateContact();
                $this->submitForm(); // Submit the form if validation passes
                break;
        }
    }

    public function submitForm()
    {
        try {
            $report = ModelsMissingReport::create([
                'last_seen_type' => $this->form->seeTime,
                'last_seen_date' => $this->form->seeTime === 'specific_date' ? $this->form->seeDate : null,
                'last_seen_range' => $this->form->seeTime === 'range' ? $this->form->seeRange : null,
                'last_seen_location' => $this->form->last_seen_location,
                'discovered_missing_type' => $this->form->discoverTime,
                'discovered_missing_date' => $this->form->discoverTime === 'specific_date' ? $this->form->discoverDate : null,
                'discovered_missing_range' => $this->form->discoverTime === 'range' ? $this->form->discoverRange : null,
                'witnesses_and_info' => $this->form->addInfo,
                'qr_code_id' => $this->selectedApplication->qrCode->id ?? null, // Simpler access
            ]);

            // 1. Associate selected Item to the report 
            StoredItem::whereIn('id', collect($this->form->items)->pluck('id'))
                ->update(['missing_report_id' => $report->id]);

            // 2. Update photos only where needed
            foreach ($this->form->items as $item) {
                if (!empty($item['photo'])) {
                    $path = $item['photo']->store('storage-items', 'public');
                    StoredItem::where('id', $item['id'])->update(['item_photo_path' => $path]);
                }
            }

            // Update the user's contact info
            Auth::user()->update([
                'email' => $this->form->userEmail,
                'phone_number' => $this->form->userPhone,
            ]);

            $this->showSuccessMessage = true;
            $this->dispatch('show_toast', message: "Report submitted successfully!", type: "success");

            // Admin Notification
            foreach (User::where('role_id', 1)->get() as $staff) {
                $staff->notify(new MissingReportMadeNotification($report));
            }
        } catch (\Exception $e) {
            Log::error('Report submission failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('show_toast', message: "Something went wrong! Please try again later.", type: 'fail');
        }
    }

    #[Computed]
    public function isReportEnabled()
    {
        return Setting::get('missing_report_enabled', true);
    }

    #[Computed()]
    public function studentApplications()
    {
        return StorageApplication::with(['locker.storageRoom', 'openArea.storageRoom', 'storedItems', 'qrCode', 'semester'])
            ->where('applicant_id', Auth::user()->id)
            ->where('storage_application_status', 'approved')
            ->whereHas('semester', function ($query) {
                $query->where('is_current', true);
            })
            ->whereHas('qrCode', function ($query) {
                $query->where('status', 'checked_in');
            })
            ->get();
    }

    #[Computed()]
    public function selectedApplication()
    {
        if (!$this->form->storageApplicationId) return null;

        // Use 'firstWhere' on the already loaded collection to save DB queries
        return $this->studentApplications->firstWhere('id', $this->form->storageApplicationId);
    }

    #[Computed()]
    public function selectedItem()
    {
        if (!$this->form->itemId || !$this->selectedApplication) return null;

        return $this->selectedApplication->storedItems->firstWhere('id', $this->form->itemId);
    }

    public function render()
    {
        return view('livewire.missing-report');
    }
}
