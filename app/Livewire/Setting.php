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
    public $rooms = [];
    public $storage_application_enabled;
    public $missing_report_enabled;
    public $announcement_text;
    public $timeline_details;

    public function mount()
    {
        foreach ($this->storageRooms as $room) {
            $this->rooms[$room->id] = [
                'opening_hour' => ModelsSetting::get("storage_room_{$room->id}_opening_hour"),
                'closing_hour' => ModelsSetting::get("storage_room_{$room->id}_closing_hour")
            ];
        }

        $this->storage_application_enabled = ModelsSetting::get('storage_application_enabled');
        $this->missing_report_enabled = ModelsSetting::get('missing_report_enabled');
        $this->announcement_text = ModelsSetting::get('announcement_text');
        $this->timeline_details = ModelsSetting::get('timeline_details');
    }

    public function save()
    {
        foreach ($this->rooms as $roomId => $hours) {
            ModelsSetting::set("storage_room_{$roomId}_opening_hour", $hours['opening_hour']);
            ModelsSetting::set("storage_room_{$roomId}_closing_hour", $hours['closing_hour']);
        }

        ModelsSetting::set('storage_application_enabled', $this->storage_application_enabled);
        ModelsSetting::set('missing_report_enabled', $this->missing_report_enabled);
        ModelsSetting::set('announcement_text', $this->announcement_text);
        ModelsSetting::set('timeline_details', $this->timeline_details);

        $this->dispatch('show_toast', message: "Settings updated successfully!", type: "success");
    }

    #[Computed()]
    public function storageRooms()
    {
        return StorageRoom::all();
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
