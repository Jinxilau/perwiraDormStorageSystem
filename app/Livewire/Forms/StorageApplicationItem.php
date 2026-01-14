<?php

namespace App\Livewire\Forms;

use App\Models\StoredItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class StorageApplicationItem extends Form
{
    use WithFileUploads;

    public $items = []; // Array to store multiple items
    public $currentItemIndex = 0; // Track current item being edited

    public $item_name = '';
    public $category = '';
    public $item_description = '';
    public $estimated_size = '';
    public $item_photo = null;

    protected $messages = [
        'items.*.item_name.required' => 'Item name is required for all items.', # * is for each index in the array
        'items.*.category.required' => 'Item type is required for all items.',
        'items.*.estimated_size.required' => 'Size is required for all items.',
    ];

    public function validateCurrentItem()
    {
        $this->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
            'item_description' => 'nullable|string|max:1000',
            'estimated_size' => 'required|in:small,medium,large',
            'item_photo' => 'nullable|image|max:5120',
        ]);
    }

    public function validateAllItem() # All item in the arr
    {
        $this->validate([
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'required|in:electronic,furniture,container,luggage,clothing,home_appliance,other',
            'items.*.item_description' => 'nullable|string|max:1000',
            'items.*.estimated_size' => 'required|in:small,medium,large',
            'items.*.item_photo' => 'nullable|image|max:5120',
        ]);
    }

    public function resetCurrentForm()
    {
        $this->reset('item_name', 'category', 'item_description', 'estimated_size', 'item_photo');
    }

    public function resetAllItem()
    {
        $this->items = [
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
        $this->currentItemIndex = 0;
        $this->resetCurrentForm();
    }

    public function saveCurrentItemToArray()
    {
        $this->items[$this->currentItemIndex] = [
            'id' => $this->items[$this->currentItemIndex]['id'] ?? null, // Preserve existing ID if present
            'item_name' => $this->item_name,
            'category' => $this->category,
            'item_description' => $this->item_description,
            'estimated_size' => $this->estimated_size,
            'item_photo' => $this->item_photo, // TemporaryUploadedFile object
            'temp_photo_url' => $this->items[$this->currentItemIndex]['temp_photo_url'] ?? null, // The existing photo path
        ];
    }

    public function loadCurrentItem() # Switch to the item
    {
        $currentItem = $this->items[$this->currentItemIndex];

        $this->item_name = $currentItem['item_name'];
        $this->category = $currentItem['category'];
        $this->item_description = $currentItem['item_description'];
        $this->estimated_size = $currentItem['estimated_size'];
        $this->item_photo = $currentItem['item_photo'];
    }

    public function createAllItem($storageApplicationId) # Create item in the arr
    {
        foreach ($this->items as $index => $item) {
            $photoPath = null;

            // Handle photo upload
            if (isset($item['item_photo']) && $item['item_photo']) {
                $photoPath = $item['item_photo']->store('storage-items', 'public');
            }

            StoredItem::create([
                'item_name' => $item['item_name'],
                'estimated_size' => $item['estimated_size'],
                'item_type' => $item['category'],
                'item_description' => $item['item_description'],
                'item_photo_path' => $photoPath,
                'storage_application_id' => $storageApplicationId,
                'missing_report_id' => null,
            ]);
        }
    }

    // Load Existing Application Items
    public function loadExistingItems($storageApplication)
    {
        $storageApplication->load(['storedItems']);
        $this->items = [];
        $this->currentItemIndex = 0;

        // Load the existing items into the form array
        foreach ($storageApplication->storedItems as $item) {
            $this->items[] = [
                'id' => $item->id,  // <- Here is the magic
                'item_name' => $item->item_name,
                'category' => $item->item_type,
                'item_description' => $item->item_description,
                'estimated_size' => $item->estimated_size,
                'item_photo' => null, // existing image
                'temp_photo_url' => $item->item_photo_path ?? null,
            ];
        }

        // Load the first item into the form
        if (!empty($this->items)) {
            $this->item_name        = $this->items[0]['item_name'];
            $this->category         = $this->items[0]['category'];
            $this->item_description = $this->items[0]['item_description'];
            $this->estimated_size   = $this->items[0]['estimated_size'];
            $this->item_photo       = $this->items[0]['item_photo'];
        }
    }

    // Update all items for an application
    public function updateAllItems($applicationId)
    {
        // Get existing item IDs associated with the application
        $existingIds = StoredItem::where('storage_application_id', $applicationId)
            ->pluck('id')
            ->toArray();

        $submittedIds = [];

        foreach ($this->items as $item) {
            // Load the submitted item IDs 
            if (!empty($item['id'])) {
                $submittedIds[] = $item['id'];
            }

            // Create new item
            if (empty($item['id'])) {
                $photoPath = null;
                if (isset($item['item_photo']) && $item['item_photo']) {
                    $photoPath = $item['item_photo']->store('storage-items', 'public');
                } else {
                    $photoPath = $item['temp_photo_url'] ?? null;
                }

                StoredItem::create([
                    'item_name' => $item['item_name'],
                    'estimated_size' => $item['estimated_size'],
                    'item_type' => $item['category'],
                    'item_description' => $item['item_description'],
                    'item_photo_path' => $photoPath,
                    'storage_application_id' => $applicationId,
                    'missing_report_id' => null,
                ]);

                continue;
            }

            // Update existing
            $storedItem = StoredItem::find($item['id']);
            if (!$storedItem) continue;

            $photoPath = $storedItem->item_photo_path;
            if (!empty($photoPath)) {
                // If photo is removed
                if ($item['temp_photo_url'] === null && $item['item_photo'] === null) {
                    $photoPath = null;
                    Storage::disk('public')->delete($storedItem->item_photo_path);
                }
            }

            if (!empty($item['item_photo'])) {
                $photoPath = $item['item_photo']->store('storage-items', 'public');
                Storage::disk('public')->delete($storedItem->item_photo_path);
            }

            $storedItem->update([
                'item_name' => $item['item_name'],
                'estimated_size' => $item['estimated_size'],
                'item_type' => $item['category'],
                'item_description' => $item['item_description'],
                'item_photo_path' => $photoPath,
            ]);
        }

        // Delete removed items
        $deleteIds = array_diff($existingIds, array_filter($submittedIds));
        foreach ($deleteIds as $id) {
            $item = StoredItem::find($id);
            Storage::disk('public')->delete($item->item_photo_path);
            $item->delete();
        }
    }
}
