<?php

namespace App\Livewire;

use App\Models\Setting as ModelsSetting;
use App\Models\StorageRoom;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Settings')]
#[Lazy()]
class Setting extends Component
{
    public $storage_application_enabled;
    public $missing_report_enabled;

    public function mount()
    {
        $this->storage_application_enabled = ModelsSetting::get('storage_application_enabled') ? true : false;
        $this->missing_report_enabled = ModelsSetting::get('missing_report_enabled') ? true : false;
    }

    public function save()
    {
        ModelsSetting::set('storage_application_enabled', $this->storage_application_enabled);
        ModelsSetting::set('missing_report_enabled', $this->missing_report_enabled);

        $this->dispatch('show_toast', message: "Settings updated successfully!", type: "success");
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
