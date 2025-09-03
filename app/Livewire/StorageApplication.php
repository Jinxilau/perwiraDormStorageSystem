<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\OpenArea;
use App\Models\StorageApplication as ModelsStorageApplication;
use App\Models\StorageRoom;
use App\Models\StoredItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('layouts.student')]
#[Title('Student - Storage')]
class StorageApplication extends Component
{
    use WithFileUploads;

    public $items = []; // Array to store multiple items
    public $currentItemIndex = 0; // Track current item being edited
    public $currentStep = 1;
    public $totalSteps = 3;
    public $showSuccessMessage = false;

    // Step 1: Item Details
    public $item_name = '';
    public $category = '';
    public $item_description = '';
    public $estimated_size = '';
    public $item_photo = null;
    
    // Step 2: Room Selection 
    public $selectedRoom = null;
    public $selectedStorageType = []; // 'open_area' or 'locker'
    public $selectedLockerId = null;

    // Step 3: Review & Submit 
    public $termsAccepted = false;

    public $purpose = '';
    public $expected_check_in = '';

    public $categories = [];
    public $sizes = [];
    public $applicantData;

    public $storageRooms = [];
    public $availableLockers = [];
    public $openArea = null;

    public function mount()
    {
        // Step 1: Item Details
        $this->items = [
            0 => [
                'item_name' => '',
                'category' => '',
                'item_description' => '',
                'estimated_size' => '',
                'item_photo' => null,
                'temp_photo_url' => null,
            ]
        ];
        
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

        // Step 2: Room Selection 
        $this->loadStorageRooms();

        $firstAvailableRoom = StorageRoom::where('room_status', 'active')->first();
        if ($firstAvailableRoom) {
            $this->selectRoom($firstAvailableRoom->id);
        }

        // Step 3: Review & Submit 
        $this->applicantData = [
            'name' => Auth::user()->name,
            'matric_card' => Auth::user()->matric_card,
            'study_year_semester' => Auth::user()->study_year_semester,
            'gender' => Auth::user()->gender,
            'phone' => Auth::user()->phone,
            'email' => Auth::user()->email
        ];
    }

    public function loadStorageRooms()
    {
        $this->storageRooms = StorageRoom::with(['lockers', 'openAreas'])->where('room_status', 'active')->get()->map(function ($room) {
            $availableLockers = $room->lockers->where('status', 'available')->count();
            $totalLockers = $room->lockers->count();
            
            $openArea = $room->openAreas->first();
            $hasOpenAreaSpace = $openArea && $openArea->area_status === 'available' && $openArea->occupied_storage_capacity < $openArea->estimated_storage_capacity;

            return [
                'id' => $room->id,
                'room_name' => $room->room_name,
                'storage_location' => $room->storage_location,
                'available_lockers' => $availableLockers,
                'total_lockers' => $totalLockers,
                'has_available_storage' => $availableLockers > 0 || $hasOpenAreaSpace,
                'is_full' => $availableLockers === 0 && !$hasOpenAreaSpace
            ];
        })->toArray();
    }

    public function removePhoto()
    {
        $this->item_photo = null;
        $this->items[$this->currentItemIndex]['item_photo'] = null;
        $this->items[$this->currentItemIndex]['temp_photo_url'] = null;
    }

    private function buildItemDescription($item)
    {
        $description = "Item: " . $item['item_name'];
        
        if (!empty($item['item_description'])) {
            $description .= "\n\nDescription: " . $item['item_description'];
        }
        
        return $description;
    }

    public function addItem()
    {
        // Validate current item first
        $this->validateCurrentItem();

        try {
            // Save current item data to the items array
            $this->saveCurrentItemToArray();
            
            // Add new empty item
            $newIndex = count($this->items);
            $this->items[$newIndex] = [
                'item_name' => '',
                'category' => '',
                'item_description' => '',
                'estimated_size' => '',
                'item_photo' => null,
                'temp_photo_url' => null,
            ];
            
            // Switch to new item
            $this->currentItemIndex = $newIndex;
            $this->resetCurrentForm();
            
            session()->flash('success', 'New item form added successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while adding new item form. Please try again.');
        }
    }

    public function removeItem($index)
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Reindex array
            
            // Adjust current index if necessary
            if ($this->currentItemIndex >= count($this->items)) {
                $this->currentItemIndex = count($this->items) - 1;
            }
            
            $this->loadCurrentItem();
            session()->flash('success', 'Item removed successfully!');
        }
    }

    public function switchToItem($index)
    {
        // Save current item data before switching
        $this->saveCurrentItemToArray();
        
        // Switch to selected item
        $this->currentItemIndex = $index;
        $this->loadCurrentItem();
    }

    private function saveCurrentItemToArray()
    {
        $this->items[$this->currentItemIndex] = [
            'item_name' => $this->item_name,
            'category' => $this->category,
            'item_description' => $this->item_description,
            'estimated_size' => $this->estimated_size,
            'item_photo' => $this->item_photo,
            'temp_photo_url' => $this->item_photo ? $this->item_photo->temporaryUrl() : null,
        ];
    }

    private function loadCurrentItem()
    {
        $currentItem = $this->items[$this->currentItemIndex];
        $this->item_name = $currentItem['item_name'];
        $this->category = $currentItem['category'];
        $this->item_description = $currentItem['item_description'];
        $this->estimated_size = $currentItem['estimated_size'];
        $this->item_photo = $currentItem['item_photo'];
    }

    private function resetCurrentForm()
    {
        $this->item_name = '';
        $this->category = '';
        $this->item_description = '';
        $this->estimated_size = '';
        $this->item_photo = null;
    }

    private function resetAllForms()
    {
        $this->items = [
            0 => [
                'item_name' => '',
                'category' => '',
                'item_description' => '',
                'estimated_size' => '',
                'item_photo' => null,
                'temp_photo_url' => null,
            ]
        ];
        $this->currentItemIndex = 0;
        $this->resetCurrentForm();
        $this->currentStep = 1;
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = $roomId;
        $this->selectedStorageType = [];
        $this->selectedLockerId = null;
        $this->loadRoomDetails();
    }

    public function loadRoomDetails()
    {
        if (!$this->selectedRoom) return;

        // Load lockers for selected room
        $this->availableLockers = Locker::where('storage_room_id', $this->selectedRoom)->get()->map(function ($locker) {
            return [
                'id' => $locker->id,
                'locker_number' => 'L' . str_pad($locker->id, 2, '0', STR_PAD_LEFT),
                'status' => $locker->status,
                'size' => $locker->size
            ];
        })->toArray();

        $this->openArea = OpenArea::where('storage_room_id', $this->selectedRoom)->first();
    }

    public function selectLocker($lockerId)
    {
        $this->selectedLockerId = $lockerId;
        $this->selectedStorageType[] = 'locker';
    }

    // public function submitSelection()
    // {
    //     $this->validate([
    //         'selectedRoom' => 'required',
    //         'selectedStorageType' => 'required|in:open_area,locker',
    //         'selectedLockerId' => 'required_if:selectedStorageType,locker'
    //     ]);

    //     // Handle the storage selection logic here
    //     // You can emit an event or redirect to next step
    //     $this->dispatch('storage-selected', [
    //         'room_id' => $this->selectedRoom,
    //         'storage_type' => $this->selectedStorageType,
    //         'locker_id' => $this->selectedLockerId
    //     ]);

    //     session()->flash('message', 'Storage selection completed successfully!');
    // }

    public function selectStorageType($type)
    {
        $this->selectedStorageType[] = $type;
        if ($type !== 'locker') {
            $this->selectedLockerId = [];
        }
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

    private function validateCurrentItem()
    {
        $this->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
            'item_description' => 'nullable|string|max:1000',
            'estimated_size' => 'required|in:small,medium,large',
            'item_photo' => 'nullable|image|max:2048',
        ]);
    }

    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validateCurrentItem();
                try {
                    $this->saveCurrentItemToArray();
                    $this->validate([
                        'items.*.item_name' => 'required|string|max:255',
                        'items.*.category' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
                        'items.*.item_description' => 'nullable|string|max:1000',
                        'items.*.estimated_size' => 'required|in:small,medium,large',
                        'items.*.item_photo' => 'nullable|image|max:2048',
                    ]);
                } catch (ValidationException $e) {
                    logger()->error('Validation failed', ['errors' => $e->errors()]);
                    throw $e;
                }
                break;
            case 2:
                $this->validate([
                    'selectedRoom' => 'required',
                    'selectedStorageType' => 'required|array',
                    'selectedStorageType.*' => 'in:open_area,locker',
                    'selectedLockerId' => 'required_if:selectedStorageType,locker'
                ]);
                break;
            case 3:
                $this->validate([
                    'notes' => 'nullable|string|max:1000',
                    // 'paymentMethod' => 'required|in:bank_transfer,online_banking,cash',
                    'receipt' => 'required|image|max:2048', // TODO: 2MB max
                    // 'paymentReference' => 'required|string|max:100',
                    // 'customerNotes' => 'nullable|string|max:500'
                ]);

                $this->submitForm(); // Submit the form if validation passes
                break;
        }
    }

    // protected $rules = [
    //     'purpose' => 'required|string|max:1000',
    //     'expected_check_in' => 'nullable|date|after:today',
    //     'items.*.item_name' => 'required|string|max:255',
    //     'items.*.item_type' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
    //     'items.*.item_description' => 'nullable|string|max:1000',
    //     'items.*.estimated_size' => 'required|in:small,medium,large',
    //     'items.*.item_photo' => 'nullable|image|max:2048',
    // ];

    protected $messages = [
        'purpose.required' => 'Please provide the purpose for storage.',
        'expected_check_in.after' => 'Check-in date must be in the future.',
        'items.*.item_name.required' => 'Item name is required for all items.',
        'items.*.category.required' => 'Item type is required for all items.',
        'items.*.estimated_size.required' => 'Size is required for all items.',
    ];

    //////////////////// Submission
    // private function validateAllData()
    // {
    //     // First validate storage application fields
    //     $this->validate([
    //         'items.*.item_name' => 'required|string|max:255',
    //         'items.*.category' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
    //         'items.*.item_description' => 'nullable|string|max:1000',
    //         'items.*.estimated_size' => 'required|in:small,medium,large',
    //         'items.*.item_photo' => 'nullable|image|max:2048',
    //     ]);

    //     // Then validate each item
    //     foreach ($this->items as $index => $item) {
    //         if (empty($item['item_name'])) {
    //             throw new ValidationException("Item name is required for item " . ($index + 1));
    //         }
    //         if (empty($item['category'])) {
    //             throw new ValidationException("Item type is required for item " . ($index + 1));
    //         }
    //         if (empty($item['estimated_size'])) {
    //             throw new ValidationException("Size is required for item " . ($index + 1));
    //         }
            
    //         // Validate item photo if present
    //         if (isset($item['item_photo']) && $item['item_photo']) {
    //             $validator = Validator::make(
    //                 ['photo' => $item['item_photo']], 
    //                 ['photo' => 'image|max:2048']
    //             );
                
    //             if ($validator->fails()) {
    //                 throw new ValidationException("Invalid photo for item " . ($index + 1) . ": " . $validator->errors()->first());
    //             }
    //         }
    //     }
    // }

    public function submit()
    {
        if (!$this->termsAccepted) {
            session()->flash('error', 'Please accept the terms and conditions to proceed.');
            return;
        }

        $openAreaId = in_array('open_area', $this->selectedStorageType) ? OpenArea::where('storage_room_id', $this->selectedRoom)->value('id') : null;
        // Save current item data first
        $this->saveCurrentItemToArray();
        
        // Validate all data including storage application fields
        // $this->validateAllData();

        DB::beginTransaction();
        
        try {
            // Create the main storage application
            $storageApplication = ModelsStorageApplication::create([
                'applicant_id' => Auth::id(),
                'approver_id' => null, 
                'open_area_id' => $openAreaId,
                'locker_id' => $this->selectedLockerId, 
                'storage_application_status' => 'pending',
                'expected_check_in' => Carbon::parse(now())->addDay(),
                'purpose' => $this->purpose ?? 'Purpose',
            ]);

            $savedItems = [];
            
            // Create stored items for each item in the application
            foreach ($this->items as $index => $item) {
                $photoPath = null;
                
                // Handle photo upload
                if (isset($item['item_photo']) && $item['item_photo']) {
                    $photoPath = $item['item_photo']->store('storage-items', 'public');
                }

                StoredItem::create([
                    'storage_application_id' => $storageApplication->id,
                    'missing_report_id' => null,
                    'estimated_size' => $item['estimated_size'],
                    'item_type' => $item['category'],
                    'item_description' => $this->buildItemDescription($item),
                    'item_photo_path' => $photoPath,
                ]);
                
            }

            DB::commit();
            // $message = $itemCount === 1 ? 'Storage application with 1 item submitted successfully!' : "Storage application with {$itemCount} items submitted successfully!";
            session()->flash('success', 'Storage Application success.');
            $this->resetAllForms();
            $this->showSuccessMessage = true;
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Storage application submission failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'items_count' => count($this->items)
            ]);
            
            session()->flash('error', 'An error occurred while submitting your application. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.storage-application');
    }
}
