<?php

namespace App\Livewire\Forms;

use App\Models\MissingReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\Form;

class MissingItemForm extends Form
{
    use WithFileUploads;

    protected function messages()
    {
        return [
            'items.*.id.required' => 'Item #:position is required.',
            'seeTime.required' => 'This field is required',
            'discoverTime.required' => 'This field is required',
            'confirmed.accepted' => 'This field must be accepted',
            'addInfo.max' => 'This field must not be greater than 2000 characters.'
        ];
    }

    public $items; # store all the items that are missing.
    public $itemIdx = 0; # index of array

    public $storageApplicationId; # Which storage that item is missing?

    public $itemId; # What item is missing?
    public $photo; # Item's photo

    public $seeTime; # Last see the item not missing
    public $seeDate;
    public $seeRange;

    public $discoverTime; # When discover missing
    public $discoverDate;
    public $discoverRange;

    public $last_seen_location;

    public $userEmail;
    public $userPhone;
    public $confirmed; # Confirm that information provided is correct.

    public $addInfo;

    // Step 1 : Storage
    public function updatedStorageApplicationId()
    {
        $this->itemId = null;
    }

    // Step 2 : Items
    public function storeArr()
    {
        $this->items[$this->itemIdx] = [
            'id' => $this->itemId,
            'photo' => $this->photo,
        ];
    }
    public function loadItem() # Load to prop
    {
        $this->itemId = $this->items[$this->itemIdx]['id'];
        $this->photo = $this->items[$this->itemIdx]['photo'];
    }

    // Validate Steps
    public function validateStorage()
    {
        $this->validate([
            'storageApplicationId' => 'required|exists:storage_applications,id',
        ]);
    }

    public function validateCurrentItem()
    {
        $this->validate([
            'itemId' => 'required|exists:stored_items,id',
            'photo' => 'nullable|image|max:5120'
        ]);
    }

    public function validateAllItems()
    {
        $this->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stored_items,id',
            'items.*.photo' => 'nullable|image|max:5120',
        ]);
    }

    public function validateTime()
    {
        $this->validate([
            'seeTime' => 'required|in:specific_date,range,dont_remember',
            'seeDate' => [
                'nullable',
                'date',
                'before_or_equal:today',
                Rule::requiredIf(fn() => $this->seeTime === 'specific_date')
            ],
            'seeRange' => [
                'nullable',
                'in:today,yesterday,this_week,last_week,last_month,longer',
                Rule::requiredIf(fn() => $this->seeTime === 'range')
            ],
            'last_seen_location' => 'required|string|max:255',
            'discoverTime' => 'required|in:specific_date,range,dont_remember',
            'discoverDate' => ['nullable', 'date', 'before_or_equal:today', Rule::requiredIf(fn() => $this->discoverTime === 'date')],
            'discoverRange' => ['nullable', 'in:today,yesterday,this_week,last_week,last_month,longer', Rule::requiredIf(fn() => $this->discoverTime === 'range')],
            'addInfo' => 'nullable|string|max:2000',
        ]);
    }

    public function validateContact()
    {
        $this->validate([
            'userEmail' => 'required|email',
            'userPhone' => 'required|string|regex:/^01[0-9]-[0-9]{7,8}$/',
            'confirmed' => 'accepted'
        ]);
    }
}
