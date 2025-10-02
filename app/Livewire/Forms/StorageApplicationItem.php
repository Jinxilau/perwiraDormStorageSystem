<?php

namespace App\Livewire\Forms;

use App\Models\StoredItem;
use Illuminate\Support\Facades\Log;
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
            'item_photo' => 'nullable|image|max:2048',
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
            'items.*.item_photo' => 'nullable|image|max:2048',
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
            'item_name' => $this->item_name,
            'category' => $this->category,
            'item_description' => $this->item_description,
            'estimated_size' => $this->estimated_size,
            'item_photo' => $this->item_photo,
            'temp_photo_url' => $this->item_photo ? $this->item_photo->temporaryUrl() : null,
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
}
