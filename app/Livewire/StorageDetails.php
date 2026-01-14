<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\QRCode;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use App\Notifications\ApproveStorageApplicationNotification;
use App\Notifications\RejectStorageApplicationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Lazy()]
class StorageDetails extends Component
{
    use WithFileUploads;

    public $application;
    public $rejectionNote = '';
    public $modalData2;

    // Edit Item Properties
    public $editingItemId;
    public $editItemName;
    public $editEstimatedSize;
    public $editItemType;
    public $editItemCondition;
    public $editItemDescription;
    public $editItemPhoto;
    public $currentItemPhotoPath;

    protected $rules = [
        'editItemName' => 'required|string|max:100',
        'editEstimatedSize' => 'required|in:small,medium,large',
        'editItemType' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
        'editItemCondition' => 'required|in:fragile,bulky,boxed,misc',
        'editItemDescription' => 'nullable|string|max:500',
        'editItemPhoto' => 'nullable|image|max:2048', // 2MB max
    ];

    public function mount(StorageApplication $application)
    {
        $this->application = $application->load([
            'applicant', 
            'storedItems', 
            'locker.storageRoom', 
            'openArea.storageRoom', 
            'qrCode.scanLogs', 
            'semester',
        ]);
    }

    public function editItem($itemId)
    {
        $item = StoredItem::findOrFail($itemId);
        
        // Security check: ensure item belongs to this application
        if ($item->storage_application_id !== $this->application->id) {
            $this->dispatch('show_toast', message: 'Unauthorized action.', type: 'fail');
            return;
        }

        $this->editingItemId = $item->id;
        $this->editItemName = $item->item_name;
        $this->editEstimatedSize = $item->estimated_size;
        $this->editItemType = $item->item_type;
        $this->editItemCondition = $item->item_condition;
        $this->editItemDescription = $item->item_description;
        $this->currentItemPhotoPath = $item->item_photo_path;
        $this->editItemPhoto = null;

        $this->dispatch('display_modal');
    }

    public function updateItem()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $item = StoredItem::findOrFail($this->editingItemId);

                // Handle photo upload
                $photoPath = $this->currentItemPhotoPath;
                if ($this->editItemPhoto) {
                    // Delete old photo if exists
                    if ($this->currentItemPhotoPath) {
                        Storage::disk('public')->delete($this->currentItemPhotoPath);
                    }
                    
                    // Store new photo
                    $photoPath = $this->editItemPhoto->store('stored_items', 'public');
                }

                // Update item
                $item->update([
                    'item_name' => $this->editItemName,
                    'estimated_size' => $this->editEstimatedSize,
                    'item_type' => $this->editItemType,
                    'item_condition' => $this->editItemCondition,
                    'item_description' => $this->editItemDescription,
                    'item_photo_path' => $photoPath,
                ]);

                // Activity Log
                $this->application->activityLogs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'updated',
                    'description' => "Updated stored item: {$item->item_name} (ID: {$item->id})",
                    'ip_address' => request()->ip(),
                ]);

                // Refresh application data
                $this->application->refresh();
            });

            $this->dispatch('close-modal');
            $this->dispatch('show_toast', message: 'Item updated successfully!', type: 'success');
            $this->resetEditForm();

        } catch (\Exception $e) {
            Log::error('Update Item Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Failed to update item. Please try again.', type: 'fail');
        }
    }

    public function deleteItem($itemId)
    {
        try {
            DB::transaction(function () use ($itemId) {
                $item = StoredItem::findOrFail($itemId);

                // Security check
                if ($item->storage_application_id !== $this->application->id) {
                    throw new \Exception('Unauthorized action.');
                }

                $itemName = $item->item_name;

                // Delete photo if exists
                if ($item->item_photo_path) {
                    Storage::disk('public')->delete($item->item_photo_path);
                }

                $item->delete();

                // Activity Log
                $this->application->activityLogs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'deleted',
                    'description' => "Deleted stored item: {$itemName} (ID: {$itemId})",
                    'ip_address' => request()->ip(),
                ]);

                // Refresh application data
                $this->application->refresh();
            });

            $this->dispatch('show_toast', message: 'Item deleted successfully!', type: 'success');

        } catch (\Exception $e) {
            Log::error('Delete Item Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Failed to delete item. Please try again.', type: 'fail');
        }
    }

    public function cancelEdit()
    {
        $this->resetEditForm();
        $this->dispatch('close-modal');
    }

    private function resetEditForm()
    {
        $this->reset([
            'editingItemId',
            'editItemName',
            'editEstimatedSize',
            'editItemType',
            'editItemCondition',
            'editItemDescription',
            'editItemPhoto',
            'currentItemPhotoPath'
        ]);
        $this->resetValidation();
    }

    private function processApproval(StorageApplication $application)
    {
        if ($application->locker_id) {
            $locker = Locker::lockForUpdate()->find($application->locker_id);

            if (!$locker) {
                throw new \Exception("Locker not found for Application ID: {$application->id}");
            }

            if ($locker->status !== 'available') {
                throw new \Exception("Locker {$locker->code} is already occupied or under maintenance.");
            }

            $locker->update(['status' => 'reserved']);
        }

        if ($application->open_area_id && $application->openArea->area_status !== 'available') {
            throw new \Exception("Open Area {$application->openArea->storageRoom->room_name} is already occupied or under maintenance.");
        }

        QRCode::create([
            'storage_application_id' => $application->id,
            'qr_token' => Str::uuid(),
        ]);

        $application->update([
            'storage_application_status' => 'approved',
            'approver_id' => Auth::id()
        ]);

        try {
            $application->applicant->notify(new ApproveStorageApplicationNotification($application));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'approved',
            'description' => "Approved application ID: {$application->id}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function approveApplication()
    {
        try {
            DB::transaction(function () {
                $this->processApproval($this->application);
            });

            $this->dispatch('show_toast', message: 'Application approved successfully!', type: 'success');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Approval Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: "Approval Error: {$e->getMessage()}", type: 'fail');
        }
    }

    private function processRejection(StorageApplication $application)
    {
        $application->update([
            'storage_application_status' => 'rejected',
            'note' => $this->rejectionNote,
            'approver_id' => Auth::id()
        ]);

        try {
            $application->applicant->notify(new RejectStorageApplicationNotification($application, $this->rejectionNote));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'description' => "Rejected application ID: {$application->id}. Note: {$this->rejectionNote}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function rejectApplication()
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            DB::transaction(function () {
                $this->processRejection($this->application);
            });

            $this->dispatch('close-modal');
            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
            $this->reset(['rejectionNote']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Rejection Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    protected function getCompetingApplications()
    {
        if (!$this->application->locker_id) {
            return collect();
        }

        return StorageApplication::with('applicant')
            ->where('locker_id', $this->application->locker_id)
            ->where('semester_id', $this->application->semester_id)
            ->where('storage_application_status', 'pending')
            ->orderBy('created_at')
            ->get();
    }

    public function render()
    {
        return view('livewire.storage-details');
    }
}