<?php

namespace App\Livewire;

use App\Livewire\Forms\StorageApplicationItem;
use App\Livewire\Forms\StorageApplicationRoom;
use App\Models\OpenArea;
use App\Models\StorageApplication as ModelsStorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use App\Notifications\StorageApplicationMadeNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('components.layouts.student')]
#[Title('Storage Application')]
#[Lazy()]
class StorageApplication extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 3;
    public $showSuccessMessage = false;

    // Step 1: Item Details
    public StorageApplicationItem $itemForm;

    // Step 2: Room Selection 
    public StorageApplicationRoom $roomForm;

    // Step 3: Review & Submit 
    public $termsAccepted = false;

    public $categories = [];
    public $sizes = [];
    public $operatingHour = [];
    public $lockerSize = '45×90×55cm (120-150 liters)';
    public $applicantData;

    public $purpose = ''; # Not using
    public $expected_check_in = ''; # Not using

    protected $messages = [
        'purpose.required' => 'Please provide the purpose for storage.',
        'expected_check_in.after' => 'Check-in date must be in the future.',
    ];

    public function mount()
    {
        $this->categories = [
            'electronic' => 'Electronic',
            'furniture' => 'Furniture',
            'container' => 'Container',
            'luggage' => 'Luggage',
            'clothing' => 'Clothing',
            'home_appliance' => 'Home Appliance',
            'other' => 'Other',
        ];

        $this->sizes = [
            'small' => [
                'name' => 'Small',
                'description' => 'Books, clothes, small electronics, small-sized box',
                'icon' => '📦'
            ],
            'medium' => [
                'name' => 'Medium',
                'description' => 'Appliances, sports gear, medium-sized box',
                'icon' => '📋'
            ],
            'large' => [
                'name' => 'Large',
                'description' => 'Chairs, lamps, full-size standing fan, large appliances, bicycle, shoe rack, large luggage',
                'icon' => '🗄️'
            ]
        ];

        $this->operatingHour = [
            'start' => 'Morning: 9:00 AM - 11:00 AM',
            'end' => 'Evening: 9:00 PM - 11:00 PM'
        ];

        // Step 1: Item Details
        $this->itemForm->items = [
            0 => [
                'item_name' => '',
                'category' => '',
                'item_description' => '',
                'estimated_size' => '',
                'item_photo' => null,
                'temp_photo_url' => null,
            ]
        ];

        // Step 2: Storage Room
        $firstAvailableRoom = StorageRoom::where('room_status', 'open')->first();
        if ($firstAvailableRoom) {
            $this->selectRoom($firstAvailableRoom->id);
        }

        // Step 3: Review & Submit 
        $this->applicantData = [
            'name' => Auth::user()->name,
            'matric_card' => Auth::user()->matric_no,
            'study_year_semester' => Auth::user()->year_of_study,
            'gender' => Auth::user()->gender,
            'phone' => Auth::user()->phone_number,
            'email' => Auth::user()->email
        ];
    }

    // ITEMS
    public function switchToItem($index) # Tab navigation function
    {
        // Save current item data before switching
        $this->itemForm->saveCurrentItemToArray();

        // Switch to selected item
        $this->itemForm->currentItemIndex = $index;
        $this->itemForm->loadCurrentItem();
    }

    public function removeItem($index) # Remove one added item
    {
        if (count($this->itemForm->items) > 1) {
            unset($this->itemForm->items[$index]);
            $this->itemForm->items = array_values($this->itemForm->items); // Reindex array

            // Adjust current index if necessary
            if ($this->itemForm->currentItemIndex >= count($this->itemForm->items)) {
                $this->itemForm->currentItemIndex = count($this->itemForm->items) - 1;
            }

            $this->itemForm->loadCurrentItem();
            $this->dispatch('show_toast', message: 'Item removed successfully!', type: 'success');
        }
    }

    public function removePhoto()
    {
        $this->itemForm->item_photo = null;
        $this->itemForm->items[$this->itemForm->currentItemIndex]['item_photo'] = null;
        $this->itemForm->items[$this->itemForm->currentItemIndex]['temp_photo_url'] = null;
        $this->dispatch('show_toast', message: 'Photo remove successfully!', type: 'success');
    }

    public function addItem()
    {
        $this->itemForm->validateCurrentItem();

        try {
            // Save current item data to the items array
            $this->itemForm->saveCurrentItemToArray();

            // Add new empty item
            $newIndex = count($this->itemForm->items);
            $this->itemForm->items[$newIndex] = [
                'item_name' => '',
                'category' => '',
                'item_description' => '',
                'estimated_size' => '',
                'item_photo' => null,
                'temp_photo_url' => null,
            ];

            // Switch to new item
            $this->itemForm->currentItemIndex = $newIndex;
            $this->itemForm->resetCurrentForm();

            $this->dispatch('show_toast', message: 'New item form added successfully!', type: 'success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'An error occurred while adding new item form. Please try again.', type: 'fail');
        }
    }

    // STORAGE ROOM
    public function selectRoom($roomId)
    {
        $this->roomForm->selectedRoom = $roomId;
        $this->roomForm->selectedStorageType = [];
        $this->roomForm->selectedLockerId = null;
        $this->roomForm->loadRoomDetails();
    }

    public function selectLocker($lockerId)
    {
        $this->roomForm->selectedLockerId = $lockerId;
        $this->roomForm->selectedStorageType[] = 'locker';
    }

    private function resetAllForms() # Reset Item Form
    {
        // Step 1
        $this->itemForm->resetAllItem();

        // Step 2

        $this->currentStep = 1;
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
    {
        switch ($this->currentStep) {
            case 1:
                $this->itemForm->validateCurrentItem();
                $this->itemForm->saveCurrentItemToArray();
                $this->itemForm->validateAllItem();
                break;
            case 2:
                $this->roomForm->validateStorage();
                break;
            case 3:
                $this->validate([
                    'notes' => 'nullable|string|max:1000',
                    'receipt' => 'required|image|max:2048', // TODO: 2MB max
                    // 'paymentMethod' => 'required|in:bank_transfer,online_banking,cash',
                    // 'paymentReference' => 'required|string|max:100',
                    // 'customerNotes' => 'nullable|string|max:500'
                ]);

                $this->submitForm(); // Submit the form if validation passes
                break;
        }
    }

    public function submit()
    {
        if (!$this->termsAccepted) {
            session()->flash('error', 'Please accept the terms and conditions to proceed.');
            return;
        }

        $openAreaId = in_array('open_area', $this->roomForm->selectedStorageType) ? $this->roomForm->openArea->id : null;

        try {
            DB::beginTransaction();
            // Create the main storage application
            $storageApplication = ModelsStorageApplication::create([
                'applicant_id' => Auth::id(),
                'approver_id' => null,
                'open_area_id' => $openAreaId,
                'locker_id' => $this->roomForm->selectedLockerId,
                'storage_application_status' => 'pending',
                'start_date' => Date::now(),
                'end_date' => Date::now()->addDays(30) // TODO
            ]);

            // Create stored items for each item in the application
            $this->itemForm->createAllItem($storageApplication->id);

            DB::commit();

            // $message = $itemCount === 1 ? 'Storage application with 1 item submitted successfully!' : "Storage application with {$itemCount} items submitted successfully!";
            $this->dispatch('show_toast', message: 'Storage Application success.', type: 'success');
            $this->resetAllForms();
            $this->showSuccessMessage = true;

            foreach (User::whereIn('role_id', [1, 2])->get() as $staff) {
                $staff->notify(new StorageApplicationMadeNotification($storageApplication));
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'An error occurred while submitting your application. Please try again.', type: 'fail');
        }
    }

    #[Computed()]
    public function storageRooms()
    {
        return StorageRoom::with(['lockers', 'openAreas'])->where('room_status', 'open')->get()->map(function ($room) {
            $availableLockers = $room->lockers->where('status', 'available')->count();
            $totalLockers = $room->lockers->count();

            $openArea = $room->openAreas->first();
            $hasOpenAreaSpace = $openArea && $openArea->area_status === 'available';

            return [
                'id' => $room->id,
                'room_name' => $room->room_name,
                'storage_location' => $room->storage_location,
                'available_lockers' => $availableLockers, # Amount of Available Lockers
                'total_lockers' => $totalLockers, # Amount of locker
                'is_full' => $availableLockers === 0 && !$hasOpenAreaSpace # Check if the storage room is full or not
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.storage-application');
    }
}
