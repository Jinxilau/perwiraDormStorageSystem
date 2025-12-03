<?php

namespace App\Livewire;

use App\Models\OpenArea;
use App\Models\StorageRoom;
use App\Models\StoredItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManageStorageRoom extends Component
{
    public $roomId;
    public $roomStatus;

    public $locker; // Selected Locker

    #[Validate('required|in:available,occupied,under_maintenance')]
    public $lockerStatus;
    #[Validate('required|regex:/^\d+x\d+x\d+cm$/')]
    public $lockerSize;

    public function mount(?int $room = null)
    {
        if ($room === null) return redirect()->route('storage_room', ['room' => 1]);

        $this->roomId = $room;
        $this->roomStatus = $this->storageRoom->room_status === "open" ? true : false;
    }

    #[On('show-modal')]
    public function showModal($id)
    {
        $this->locker = $this->storageRoom->lockers->where('id', $id)->first();
        if (!empty($this->locker)) {
            $this->lockerStatus = $this->locker->status;
            $this->lockerSize = $this->locker->size;
            $this->dispatch('display_modal');
        } else {
            $this->dispatch('show_toast', message: 'Something went wrong.', type: 'fail');
        }
    }

    public function toggleRoomStatus()
    {
        try {
            $status = $this->roomStatus ? "closed" : "open";

            StorageRoom::findOrFail($this->roomId)->update(['room_status' => $status]);

            $this->dispatch('status_updated', message: "The storage room is changed to {$status}.", type: "success");

            $this->roomStatus = !$this->roomStatus;
        } catch (Exception $e) {
            $this->dispatch('status_updated', message: "Something went wrong. Please try again.", type: 'fail');
            Log::error($e->getMessage());
        }
    }

    public function toggleAreaStatus(string $value)
    {
        try {
            $this->storageRoom->openAreas->area_status = $value;
            $this->storageRoom->openAreas->save();

            $this->dispatch('status_updated', message: "The open area status is changed to {$value}.", type: "success");
        } catch (Exception $e) {
            $this->dispatch('status_updated', message: "Something went wrong. Please try again.", type: "fail");
            Log::error($e->getMessage());
        }
    }

    public function editLocker()
    {
        $this->validate();

        // If validation passes → continue logic
        $this->locker->update([
            'status' => $this->lockerStatus,
            'size'   => $this->lockerSize,
        ]);

        $this->dispatch('status_updated', message: "You edited locker {$this->locker->id}", type: "success");
    }

    #[Computed()]
    public function storageRooms()
    {
        return StorageRoom::with(['lockers', 'openAreas.storageApplications'])->get();
    }

    #[Computed()]
    public function storageRoom()
    {
        return $this->storageRooms->where('id', $this->roomId)->first();
    }

    #[Computed()]
    public function totalLockers()
    {
        return $this->storageRoom->lockers->count();
    }
    #[Computed()]
    public function availableLockers()
    {
        return $this->storageRoom->lockers->where('status', 'available')->count();
    }
    #[Computed()]
    public function unavailableLockers()
    {
        return $this->totalLockers - $this->availableLockers;
    }
    #[Computed()]
    public function occupiedLockers()
    {
        return $this->storageRoom->lockers->where('status', 'occupied')->count();
    }
    #[Computed()]
    public function reservedLockers()
    {
        return $this->storageRoom->lockers->where('status', 'reserved')->count();
    }

    #[Computed()]
    public function openAreaStoredItems()
    {
        return StoredItem::whereHas('storageApplication', function ($q) {
            $q->where('storage_application_status', 'approved')->whereHas('openArea', function ($q2) {
                $q2->where('storage_room_id', $this->roomId);
            });
        })->count();
    }

    public function render()
    {
        return view('livewire.manage-storage-room');
    }
}
