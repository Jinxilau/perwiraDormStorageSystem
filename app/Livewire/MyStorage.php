<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy()]
#[Layout('components.layouts.student')]
#[Title('My Storage')]
class MyStorage extends Component
{
    public function goToEdit($id)
    {
        return redirect()->route('storage_edit', ['application' => $id]);
    }

    public function cancelApplication($id)
    {
        $app = StorageApplication::findOrFail($id);

        if ($app->applicant_id !== Auth::id()) {
            abort(403);
        }

        $app->update([
            'storage_application_status' => 'cancelled'
        ]);

        $this->dispatch('show_toast', message: 'Application cancelled.', type: 'success');
    }

    #[Computed()]
    public function studentStorageApplications()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester'])->get();
    }

    public function render()
    {
        return view('livewire.my-storage');
    }
}
