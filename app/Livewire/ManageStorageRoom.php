<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\OpenArea;
use App\Models\StorageApplication;
use App\Models\StorageRoom;
use App\Models\StoredItem;
use App\Models\User;
use App\Notifications\StorageRoomStatusNotification;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManageStorageRoom extends Component
{
    #[Url(as: 'room', except: '')]
    public $roomId = null;

    public $roomStatus;

    public $lockerStatus;

    public $lockerSize;

    public $modalData; # Locker Id

    public function mount()
    {
        $this->roomId ??= StorageRoom::min('id');

        abort_unless(
            StorageRoom::whereKey($this->roomId)->exists(),
            404
        );

        $this->roomStatus = $this->storageRoom->room_status === "open" ? true : false;
    }

    public function toggleRoomStatus()
    {
        try {
            $room = StorageRoom::findOrFail($this->roomId);

            $newStatus = $this->roomStatus ? 'closed' : 'open';

            // Prevent unnecessary updates
            if ($room->room_status === $newStatus) {
                return;
            }

            $room->update(['room_status' => $newStatus]);

            // Log activity
            $this->logRoomStatusChange($room, $newStatus);

            // Notify students
            if ($newStatus === 'open') {
                // notify students
                User::where('role_id', '3')
                    ->chunk(100, function ($students) use ($room) {
                        foreach ($students as $student) {
                            $student->notify(new StorageRoomStatusNotification($room));
                        }
                    });
            }

            $this->roomStatus = !$this->roomStatus;

            $this->dispatch(
                'show_toast',
                message: "The storage room is changed to {$newStatus}.",
                type: "success"
            );
        } catch (Exception $e) {
            $this->dispatch(
                'show_toast',
                message: "Something went wrong. Please try again.",
                type: 'fail'
            );

            Log::error($e);
        }
    }

    protected function logRoomStatusChange($room, $status)
    {
        $room->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => $status === 'open' ? 'opened' : 'closed',
            'description' => "Storage room \"{$room->room_name}\" was {$status}.",
            'ip_address' => request()->ip(),
        ]);
    }

    public function toggleAreaStatus($value)
    {
        // 1. Validate the input status
        $allowedStatuses = ['available', 'occupied', 'under_maintenance'];
        if (!in_array($value, $allowedStatuses)) {
            $this->dispatch('show_toast', message: "Invalid status selection.", type: "fail");
            return;
        }

        try {
            $openArea = $this->storageRoom->openAreas;

            if (!$openArea) {
                throw new Exception('Open area not found');
            }

            // 2. Update status
            $openArea->update(['area_status' => $value]);

            $this->dispatch('show_toast', message: "Status updated to " . ucfirst(str_replace('_', ' ', $value)), type: "success");
        } catch (Exception $e) {
            Log::error("Error updating area status: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Something went wrong. Please try again.", type: "fail");
        }
    }

    public function editLocker()
    {
        $this->validate([
            'lockerStatus' => 'required|in:available,occupied,reserved',
            'lockerSize'   => 'required|regex:/^\d+x\d+x\d+$/'
        ]);

        // Additional Validation: Prevent changing occupied locker to available if it still has items
        if ($this->locker->status === 'occupied' && ! $this->locker->canBeMarkedAvailable()) {
            $this->dispatch('show_toast', message: 'This locker still contains items and cannot be marked as available.', type: 'fail');
            return;
        }

        // If validation passes → continue logic
        $this->locker->update([
            'status' => $this->lockerStatus,
            'size'   => $this->lockerSize,
        ]);

        $this->dispatch('show_toast', message: "Locker {$this->locker->code} updated successfully.", type: "success");
        $this->reset(['lockerStatus', 'lockerSize', 'modalData']);
        $this->dispatch('close-modal');
    }

    #[Computed]
    public function rooms()
    {
        return StorageRoom::select('id', 'room_name', 'room_status')->get();
    }


    #[Computed]
    public function storageRoom()
    {
        return StorageRoom::with([
            'lockers',
            'openAreas.storageApplications'
        ])->findOrFail($this->roomId);
    }

    // Modal
    #[Computed()]
    public function locker()
    {
        if (!$this->modalData) {
            return null;
        }

        // Use find() for cleaner code.
        // Use with() to load the room so the modal doesn't have to run extra queries.
        return Locker::with(['storageRoom'])
            ->find($this->modalData);
    }
    #[Computed]
    public function lockerApplication()
    {
        if (! $this->locker) {
            return null;
        }

        return StorageApplication::with('applicant')
            ->where('locker_id', $this->locker->id)
            ->whereIn('storage_application_status', ['pending', 'approved'])
            ->latest()
            ->first();
    }

    // Metrics
    #[Computed()]
    public function totalLockers()
    {
        return $this->storageRoom->lockers->count();
    }
    #[Computed]
    public function lockerStats()
    {
        $lockers = $this->storageRoom->lockers;

        return [
            'total'     => $lockers->count(),
            'available' => $lockers->where('status', 'available')->count(),
            'occupied'  => $lockers->where('status', 'occupied')->count(),
            'reserved'  => $lockers->where('status', 'reserved')->count(),
        ];
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
