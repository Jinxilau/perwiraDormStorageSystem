<?php

namespace App\Livewire\Forms;

use App\Models\Locker;
use App\Models\OpenArea;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StorageApplicationRoom extends Form
{
    public $selectedRoom = null;
    public $selectedStorageType = null; // 'open_area' / 'locker'
    public $selectedLockerId = null;

    // Models
    public $locker = null;
    public $room = null;
    
    public $availableLockers = [];
    public $openArea = null;

    public function validateStorage()
    {
        $this->validate([
            'selectedRoom' => 'required',
            'selectedStorageType' => 'required|in:open_area,locker', // Changed: removed array validation
            'selectedLockerId' => 'required_if:selectedStorageType,locker' // Works the same way
        ]);
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

    // Check if selected locker is still available
    public function checkLockerStatus($lockerId, $oldLockerId = null)
    {
        // 1. Find the locker, and lock the row for update
        $locker = Locker::where('id', $lockerId)
            ->lockForUpdate()
            ->first();

        // If user is editing the application and selected the SAME locker, allow it
        if ($lockerId === $oldLockerId) {
            return $locker;
        }

        // 2. Validate existence
        if (!$locker) {
            session()->flash('error', 'Locker not found.');
            return null;
        }

        // 3. Validate locker availability
        if (in_array($locker->status, ['reserved', 'occupied', 'under_maintenance'])) {
            session()->flash('error', 'The selected locker is no longer available. Please choose a different locker.');
            return null;
        }

        return $locker;
    }

    // Check if selected open area is still available
    public function checkAreaStatus()
    {
        // Ensure model exists
        if (!$this->openArea) {
            session()->flash('error', 'Selected area does not exist.');
            return null;
        }

        // Check availability
        if (in_array($this->openArea->area_status, ['occupied', 'under_maintenance'])) {
            session()->flash('error', 'The selected area is no longer available. Please choose a different area.');
            return null;
        }

        // Valid area
        return $this->openArea;
    }

    // Load existing room selection for edit mode
    public function loadExistingRoom($storageApplication)
    {
        // Reset to defaults
        $this->selectedStorageType = null;
        $this->selectedRoom = null;
        $this->selectedLockerId = null;

        // Determine which storage type was selected based on what's populated
        if ($storageApplication->locker_id && $storageApplication->open_area_id) {
            // CHANGED: User previously had both, but now can only have one
            // Default to locker since it's more specific
            $this->selectedStorageType = 'locker';
            $this->selectedRoom = $storageApplication->locker->storage_room_id;
            $this->selectedLockerId = $storageApplication->locker_id;
        } elseif ($storageApplication->open_area_id) {
            // User selected open area only
            $this->selectedStorageType = 'open_area';
            $this->selectedRoom = $storageApplication->openArea->storage_room_id;
            $this->openArea = $storageApplication->openArea;
        } elseif ($storageApplication->locker_id) {
            // User selected locker only
            $this->selectedStorageType = 'locker';
            $this->selectedRoom = $storageApplication->locker->storage_room_id;
            $this->selectedLockerId = $storageApplication->locker_id;
        }

        // Load room details
        $this->loadRoomDetails();
    }
}
