<?php

namespace App\Livewire;

use App\Livewire\Forms\StorageApplicationItem;
use App\Livewire\Forms\StorageApplicationRoom;
use App\Models\Locker;
use App\Models\OpenArea;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\StorageApplication as ModelsStorageApplication;
use App\Models\StorageRoom;
use App\Models\User;
use App\Notifications\StorageApplicationMadeNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class StorageApplication extends Component
{
    use WithFileUploads;

    public ?ModelsStorageApplication $application = null;

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

    // public $purpose = ''; # Not using
    // public $expected_check_in = ''; # Not using

    protected $messages = [
        'purpose.required' => 'Please provide the purpose for storage.',
        'expected_check_in.after' => 'Check-in date must be in the future.',
    ];

    public function mount($applicationId = null)
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
                'name' => 'Small Item',
                'icon' => '🎒', // Or use an SVG/FontAwesome class
                'color' => 'success', // Bootstrap color
                'context' => 'Fits on a standard shelf',
                'examples' => ['Backpack', 'Shoe box', 'Laptop bag', 'Helmet']
            ],
            'medium' => [
                'name' => 'Medium Item',
                'icon' => '🧳',
                'color' => 'primary',
                'context' => 'Fits in a car trunk',
                'examples' => ['Carry-on luggage', 'Plastic storage bin', 'Microwave box']
            ],
            'large' => [
                'name' => 'Large Item',
                'icon' => '🚲',
                'color' => 'warning',
                'context' => 'Requires floor space',
                'examples' => ['Large suitcase (28"+)', 'Bicycle', 'Mattress', 'Guitar case']
            ]
        ];

        // $this->operatingHour = [
        //     'start' => 'Morning: 9:00 AM - 11:00 AM',
        //     'end' => 'Evening: 9:00 PM - 11:00 PM'
        // ];

        // Edit Mode
        if ($applicationId !== null) {
            $this->application = ModelsStorageApplication::find($applicationId);

            // Prevent editing other's application
            if ($this->application->applicant_id !== Auth::id()) {
                abort(403, 'Unauthorized');
            }

            // Optional: prevent editing non-pending entries
            if ($this->application->storage_application_status !== 'pending') {
                abort(403, 'You cannot edit this application.');
            }


            $this->itemForm->loadExistingItems($this->application);
            $this->roomForm->loadExistingRoom($this->application);
        } else {
            // Step 1: Item Details
            $this->itemForm->items = [
                0 => [
                    'id' => null,
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

    // Add new items to the list
    public function addItem()
    {
        $this->itemForm->validateCurrentItem();

        try {
            // Save current item data to the items array
            $this->itemForm->saveCurrentItemToArray();

            // Add new empty item
            $newIndex = count($this->itemForm->items);
            $this->itemForm->items[$newIndex] = [
                'id' => null,
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
        $this->roomForm->room = StorageRoom::find($roomId);
        $this->roomForm->selectedStorageType = null;
        $this->roomForm->selectedLockerId = null;
        $this->roomForm->loadRoomDetails();
    }

    public function selectLocker($lockerId)
    {
        $this->roomForm->selectedLockerId = $lockerId;
        $this->roomForm->selectedStorageType = 'locker';
        $this->roomForm->locker = Locker::find($lockerId);
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
                ]);

                $this->submitForm(); // Submit the form if validation passes
                break;
        }
    }

    public function submit()
    {
        if (!$this->termsAccepted) {
            session()->flash('error', 'Please accept the terms and conditions to proceed.');
            $this->dispatch('show_toast', message: 'Please accept the terms and conditions to proceed.', type: 'fail');
            return;
        }

        if (!Setting::get('storage_application_enabled')) {
            session()->flash('error', 'Storage application submission is currently disabled.');
            $this->dispatch('show_toast', message: 'Storage application submission is currently disabled.', type: 'fail');
            return;
        }

        // Check if user already has an application for the current semester
        $current = Semester::where('is_current', true)->first();

        if (!$current) {
            session()->flash('error', 'There is something wrong. Please contact admin.');
            $this->dispatch('show_toast', message: 'There is something wrong. Please contact admin.', type: 'fail');
            return;
        }

        $alreadyAppliedThisSemester = ModelsStorageApplication::where('applicant_id', Auth::id())
            ->where('semester_id', $current->id)
            ->exists();

        if ($alreadyAppliedThisSemester) {
            session()->flash('error', 'You already have a storage application for the current semester.');
            $this->dispatch('show_toast', message: 'You already have a storage application for the current semester.', type: 'fail');
            return;
        }

        try {
            DB::beginTransaction();
            // Check locker availability again before final submission
            $locker = null;
            if ($this->roomForm->selectedStorageType === 'locker') {
                $locker = $this->roomForm->checkLockerStatus($this->roomForm->selectedLockerId);
                if (!$locker) {
                    session()->flash('error', 'The selected locker is no longer available. Please choose a different locker.');
                    $this->dispatch('show_toast', message: 'The selected locker is no longer available. Please choose a different locker.', type: 'fail');
                    return;
                }
            }
            // Check open area availability
            $openArea = null;
            if ($this->roomForm->selectedStorageType === 'open_area') {
                $openArea = $this->roomForm->checkAreaStatus();
                if (!$openArea) {
                    session()->flash('error', 'The selected open area is no longer available. Please choose a different storage option.');
                    $this->dispatch('show_toast', message: 'The selected open area is no longer available. Please choose a different storage option.', type: 'fail');
                    return;
                }
            }

            $current = Semester::where('is_current', true)->first();
            if (!$current) throw new Exception('There is something wrong. Please contact admin.');

            // Create the main storage application
            $storageApplication = ModelsStorageApplication::create([
                'semester_id' => $current->id,
                'applicant_id' => Auth::id(),
                'approver_id' => null,
                'open_area_id' => $openArea ? $openArea->id : null,
                'locker_id' => $locker ? $locker->id : null,
                'storage_application_status' => 'pending',
            ]);

            // No reserved before approve storage application
            // if ($locker) {
            //     $locker->update([
            //         'status' => 'reserved',
            //     ]);
            // }

            // Create stored items for each item in the application
            $this->itemForm->createAllItem($storageApplication->id);

            DB::commit();

            // $message = $itemCount === 1 ? 'Storage application with 1 item submitted successfully!' : "Storage application with {$itemCount} items submitted successfully!";
            $this->dispatch('show_toast', message: 'Storage Application success.', type: 'success');
            $this->resetAllForms();
            $this->showSuccessMessage = true;

            User::whereIn('role_id', [1, 2])
                ->each(fn($user) => $user->notify(new StorageApplicationMadeNotification($storageApplication)));

            return redirect()->route('my_storage');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'An error occurred while submitting your application. Please try again.', type: 'fail');
        }
    }

    public function update()
    {
        if (!$this->application) {
            abort(400, 'No application selected');
        }

        // Prevent editing if status no longer pending
        if ($this->application->storage_application_status !== 'pending') {
            $this->dispatch('show_toast', message: 'This application cannot be edited.', type: 'fail');
            return;
        }

        try {
            DB::beginTransaction();
            $oldLockerId = $this->application->locker_id;
            // Check locker availability again before final submission
            $locker = null;
            if ($this->roomForm->selectedStorageType === 'locker') {
                $locker = $this->roomForm->checkLockerStatus($this->roomForm->selectedLockerId, $oldLockerId);
                if (!$locker) {
                    session()->flash('error', 'The selected locker is no longer available. Please choose a different locker.');
                    $this->dispatch('show_toast', message: 'The selected locker is no longer available. Please choose a different locker.', type: 'fail');
                    return;
                }
            }
            // Check open area availability
            $openArea = null;
            if ($this->roomForm->selectedStorageType === 'open_area') {
                $openArea = $this->roomForm->checkAreaStatus();
                if (!$openArea) {
                    session()->flash('error', 'The selected open area is no longer available. Please choose a different storage option.');
                    $this->dispatch('show_toast', message: 'The selected open area is no longer available. Please choose a different storage option.', type: 'fail');
                    return;
                }
            }

            // Update main storage application
            $this->application->update([
                'open_area_id' => $openArea ? $openArea->id : null,
                'locker_id' => $locker ? $locker->id : null,
            ]);

            // Update stored items (your itemForm should handle it)
            $this->itemForm->updateAllItems($this->application->id);

            // No need Locker Handling before approve storage application
            // if ($oldLockerId !== $this->roomForm->selectedLockerId) {
            //     // Release old locker
            //     if ($oldLockerId) {
            //         Locker::where('id', $oldLockerId)->update(['status' => 'available']);
            //     }

            //     // Reserve new locker
            //     if ($this->roomForm->selectedLockerId) {
            //         Locker::where('id', $this->roomForm->selectedLockerId)->update(['status' => 'reserved']);
            //     }
            // }

            DB::commit();

            $this->dispatch('show_toast', message: 'Storage Application updated successfully!', type: 'success');
            $this->resetAllForms();
            $this->showSuccessMessage = true;
            $this->redirectRoute("my_storage");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: 'Error updating application.', type: 'fail');
        }
    }

    #[Computed()]
    public function storageRooms()
    {
        return StorageRoom::with(['lockers', 'openAreas'])->get()->map(function ($room) {
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
                'is_full' => $availableLockers === 0 && !$hasOpenAreaSpace, # Check if the storage room is full or not
                'hasOpenAreaSpace' => $hasOpenAreaSpace,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.storage-application');
    }
}
