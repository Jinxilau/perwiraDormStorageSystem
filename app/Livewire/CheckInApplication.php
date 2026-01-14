<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\ScanLog;
use App\Models\StorageApplication;
use App\Models\StoredItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CheckInApplication extends Component
{
    use WithFileUploads;

    public $application; # StorageApplication instance

    public $checkin_item_photo; # The uploaded check-in photo
    public $storage_zone; # The storage zone input

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
        $this->application = $application->load(['applicant', 'storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester',]);

        if ($application->qrCode) {
            $this->storage_zone = $application->qrCode->storage_zone;
        }
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

    public function confirmCheckIn()
    {
        $this->validate(
            [
                'checkin_item_photo' => 'nullable|image|max:5120', // Max 5MB
                'storage_zone'       => 'nullable|string|max:255',
            ]
        );

        // Always reload relationships to avoid stale state
        $this->application->load(['qrCode', 'locker', 'openArea']);

        if (! $this->application->qrCode) {
            $this->dispatch('show_toast', message: 'No QR Code linked to this application.', type: 'fail');
            return;
        }

        try {
            DB::transaction(function () {

                /** @var QRCode $qr */
                $qr = QRCode::where('id', $this->application->qrCode->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // ❗ Absolute guard
                if ($qr->status === 'checked_in') {
                    throw new \Exception('This application is already checked in.');
                }

                // 📸 Store photo
                $photoPath = $this->checkin_item_photo->store(
                    'checkin-photos',
                    'public'
                );

                // 🧾 Update QR Code (single update)
                $qr->update([
                    'checkin_item_photo' => $photoPath,
                    'storage_zone'       => $this->storage_zone,
                    'status'             => 'checked_in',
                ]);

                $qr->increment('scanned_count');

                // 🪵 Log scan
                ScanLog::create([
                    'qr_code_id' => $qr->id,
                    'event_type' => 'check_in',
                ]);

                // 🏷 Update storage allocation
                if ($this->application->locker) {
                    $this->application->locker->update(['status' => 'occupied']);
                }

                if ($this->application->openArea) {
                    $this->application->openArea->update(['status' => 'occupied']);
                }
            });

            $this->dispatch('show_toast', message: 'Item checked in successfully.', type: 'success');

            // Optional: reset upload field
            $this->reset('checkin_item_photo');
            session()->flash('success', 'Check-in successful! Page refreshed and filters cleared.');

            return $this->redirectRoute('check_in_application', ['application' => $this->application->id], navigate: true);
        } catch (\Throwable $e) {
            Log::error('Check-in failed', [
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('show_toast', message: $e->getMessage(), type: 'fail');
        }
    }

    public function confirmCheckOut()
    {
        if (!$this->application->qrCode) {
            $this->dispatch('show_toast', message: "Error: No QR Code found.", type: "fail");
            return;
        }

        DB::beginTransaction();

        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')
                ->lockForUpdate()
                ->findOrFail($this->application->qrCode->id);

            // Logic Check (Order matters!)
            if ($qrCode->status === 'checked_out') {
                DB::rollBack();
                $this->dispatch('show_toast', message: "Already checked out.", type: "fail");
                return;
            }

            if ($qrCode->status !== 'checked_in') {
                DB::rollBack();
                $this->dispatch('show_toast', message: "Item must be Checked In before it can be Checked Out.", type: "fail");
                return;
            }

            // 1. Create Log
            ScanLog::create([
                'qr_code_id' => $this->application->qrCode->id,
                'event_type' => 'check_out'
            ]);

            // 2. Update QR
            $qrCode->update(['status' => 'checked_out',]);
            $qrCode->increment('scanned_count');

            // 3. Update Storage Status
            if ($this->application->locker_id) {
                $this->application->locker()->update(['status' => 'available']);
            }
            if ($this->application->open_area_id) {
                $this->application->openArea()->update(['area_status' => 'available']);
            }

            DB::commit();

            $this->dispatch('show_toast', message: "Check-out successful!", type: "success");
            session()->flash('success', 'Check-out successful! Page refreshed and filters cleared.');

            return $this->redirectRoute('check_in_application', ['application' => $this->application->id], navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "System Error. Please try again.", type: "fail");
        }
    }

    public function render()
    {
        return view('livewire.check-in-application');
    }
}
