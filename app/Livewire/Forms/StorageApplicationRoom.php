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
}
