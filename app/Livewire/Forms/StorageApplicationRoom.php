<?php

namespace App\Livewire\Forms;

use App\Models\Locker;
use App\Models\OpenArea;
use Livewire\Attributes\Validate;
use Livewire\Form;

class StorageApplicationRoom extends Form
{
    public $selectedRoom = null;
    public $selectedStorageType = []; // 'open_area' / 'locker'
    public $selectedLockerId = null;

    public $availableLockers = [];
    public $openArea = null;

    public function validateStorage()
    {
        $this->validate([
            'selectedRoom' => 'required',
            'selectedStorageType' => 'required|array',
            'selectedStorageType.*' => 'in:open_area,locker',
            'selectedLockerId' => 'required_if:selectedStorageType,locker'
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
        $this->selectedStorageType = [];
        $this->selectedRoom = null;
        $this->selectedLockerId = null;

        if ($storageApplication->locker_id && $storageApplication->open_area_id) {
            $this->selectedStorageType = ['locker', 'open_area'];
            $this->selectedRoom = $storageApplication->locker->storage_room_id;
            $this->selectedLockerId = $storageApplication->locker_id;
        } elseif ($storageApplication->open_area_id) {
            $this->selectedStorageType = ['open_area'];
            $this->selectedRoom = $storageApplication->openArea->storage_room_id;
            $this->openArea = $storageApplication->openArea;
        } elseif ($storageApplication->locker_id) {
            $this->selectedStorageType = ['locker'];
            $this->selectedRoom = $storageApplication->locker->storage_room_id;
            $this->selectedLockerId = $storageApplication->locker_id;
        }

        $this->loadRoomDetails();
    }
}
