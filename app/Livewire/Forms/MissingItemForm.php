<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class MissingItemForm extends Form
{
    #[Validate('')]
    public $storageApplicationId; # Which storage that item is missing?
    
    #[Validate('')]
    public $itemId; # What item is missing?
}
