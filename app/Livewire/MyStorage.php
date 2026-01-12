<?php

namespace App\Livewire;

use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        // Check if the authenticated user is the owner of the application
        if ($app->applicant_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if application is still pending
        if ($app->storage_application_status !== 'pending') {
            $this->dispatch(
                'show_toast',
                message: 'Cannot cancel an application that has already been processed.',
                type: 'error'
            );
            return;
        }

        // Delete associated stored items
        $app->storedItems()->delete();

        $app->delete();

        $this->dispatch('show_toast', message: 'Application cancelled successfully.', type: 'success');
    }

    public function downloadQrCode($id)
    {
        $application = StorageApplication::findOrFail($id);

        // Livewire requires 'streamDownload' for file downloads
        return response()->streamDownload(function () use ($application) {
            echo QrCode::size(500)
                ->margin(4) // <--- Adds whitespace margin (centers the QR)
                ->generate($application->qrCode->qr_token);
        }, 'qr-code.svg');
    }

    #[Computed()]
    public function studentStorageApplications()
    {
        return StorageApplication::where('applicant_id', Auth::user()->id)->with(['storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester'])->latest()->get();
    }

    public function render()
    {
        return view('livewire.my-storage');
    }
}
