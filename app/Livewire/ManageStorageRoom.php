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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
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

    // Add Room Properties
    public $newRoomName;
    public $newRoomStatus = 'open';

    // Add Locker Properties
    public $newLockerCode;
    public $newLockerSize;
    public $newLockerStatus = 'available';

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

            Log::error("Error updating room status: " . $e->getMessage());
        }
    }

    protected function logRoomStatusChange($room, $status)
    {
        $room->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'updated',
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
            'lockerStatus' => 'required|in:available,occupied,reserved,under_maintenance',
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

    public function addRoom()
    {
        $this->validate([
            'newRoomName' => 'required|string|max:100|unique:storage_rooms,room_name',
            'newRoomStatus' => 'required|in:open,closed,under_maintenance'
        ]);

        try {
            DB::beginTransaction();

            // Create the storage room
            $room = StorageRoom::create([
                'room_name' => $this->newRoomName,
                'room_status' => $this->newRoomStatus,
            ]);

            // Create associated open area
            $room->openAreas()->create([
                'area_status' => 'available',
            ]);

            // Log activity
            $room->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => "Storage room \"{$room->room_name}\" was created with open area.",
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            $this->dispatch('show_toast', message: "Storage room '{$this->newRoomName}' created successfully.", type: "success");
            $this->dispatch('close-modal', modalId: 'addRoomModal');

            // Reset form and redirect to new room
            $this->reset(['newRoomName', 'newRoomStatus']);
            $this->roomId = $room->id;
            $this->redirect(route('storage_room', ['room' => $room->id]), navigate: true);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error creating storage room: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Failed to create storage room. Please try again.", type: "fail");
        }
    }

    public function addLocker()
    {
        $this->validate([
            'newLockerCode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lockers', 'code')->where(function ($query) {
                    return $query->where('storage_room_id', $this->roomId);
                })
            ],
            'newLockerSize' => 'required|regex:/^\d+x\d+x\d+$/',
            'newLockerStatus' => 'required|in:available,under_maintenance'
        ], [
            'newLockerCode.unique' => 'This locker code already exists in this room.',
        ]);

        try {
            $locker = Locker::create([
                'storage_room_id' => $this->roomId,
                'code' => $this->newLockerCode,
                'size' => $this->newLockerSize,
                'status' => $this->newLockerStatus,
            ]);

            // Optional: Log activity
            $locker->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'description' => "Locker '{$locker->code}' was added to room '{$this->storageRoom->room_name}'.",
                'ip_address' => request()->ip(),
            ]);

            $this->dispatch('show_toast', message: "Locker '{$this->newLockerCode}' created successfully.", type: "success");
            $this->dispatch('close-modal', modalId: 'addLockerModal');

            // Reset form
            $this->reset(['newLockerCode', 'newLockerSize', 'newLockerStatus']);
        } catch (Exception $e) {
            Log::error("Error creating locker: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Failed to create locker. Please try again.", type: "fail");
        }
    }

    public function deleteLocker()
    {
        try {
            $locker = $this->locker;

            // Check if locker can be deleted
            if (!$locker->canBeDeleted()) {
                $this->dispatch('show_toast', message: "This locker cannot be deleted because it has active storage applications.", type: "fail");
                return;
            }

            $lockerCode = $locker->code;
            $roomName = $locker->storageRoom->room_name;

            // Delete the locker
            $locker->delete();

            // Log activity
            $locker->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => "Locker '{$lockerCode}' was deleted from room '{$roomName}'.",
                'ip_address' => request()->ip(),
            ]);

            $this->dispatch('show_toast', message: "Locker '{$lockerCode}' deleted successfully.", type: "success");
            $this->dispatch('close-modal');
            $this->reset('modalData');
        } catch (Exception $e) {
            Log::error("Error deleting locker: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Failed to delete locker. Please try again.", type: "fail");
        }
    }

    public function deleteRoom()
    {
        try {
            // Check if room can be deleted
            if (!$this->storageRoom->canDelete()) {
                $this->dispatch('show_toast', message: "This room cannot be deleted because it has active storage applications.", type: "fail");
                return;
            }

            $room = $this->storageRoom;
            $roomName = $room->room_name;

            // Get the next available room to redirect to
            $nextRoom = StorageRoom::where('id', '!=', $room->id)->first();

            if (!$nextRoom) {
                $this->dispatch('show_toast', message: "Cannot delete the last remaining storage room.", type: "fail");
                return;
            }

            DB::beginTransaction();

            // Delete the room (cascade will handle lockers and open areas)
            $room->delete();

            // Log activity
            $room->activityLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'deleted',
                'description' => "Storage room '{$roomName}' was deleted.",
                'ip_address' => request()->ip(),
            ]);

            DB::commit();

            $this->dispatch('show_toast', message: "Storage room '{$roomName}' deleted successfully.", type: "success");
            $this->dispatch('close-modal', modalId: 'deleteRoomModal');

            // Redirect to the next available room
            $this->redirect(route('storage_room', ['room' => $nextRoom->id]), navigate: true);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error deleting storage room: " . $e->getMessage());
            $this->dispatch('show_toast', message: "Failed to delete storage room. Please try again.", type: "fail");
        }
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
