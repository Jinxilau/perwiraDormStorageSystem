<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\StorageApplication;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ManageStorageApplication extends Component
{
    use WithPagination;

    public $selectedApplications = []; # arr application id
    public $selectAll = false;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedApplications = $this->pendingStorageApplications->pluck('id')->toArray();
        } else {
            $this->selectedApplications = [];
        }
    }

    public function updatedSelectedApplications()
    {
        $this->selectAll = count($this->selectedApplications) === $this->pendingStorageApplications->count();
    }

    public function approveApplication($id)
    {
        try {
            $application = StorageApplication::findOrFail($id);

            QRCode::create([
                'storage_application_id' => $id,
                'qr_token' => Str::uuid(),
                'qr_expires_at' => Carbon::now()->addDays(7)
            ]);

            $application->update(['storage_application_status' => 'approved',]);

            $this->dispatch('application-updated', message: 'Application approved successfully!', type: 'success');
            $this->reset();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('application-updated', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            $this->dispatch('application-updated', message: 'Something went wrong. Please try again.', type: 'fail');
            Log::error($e->getMessage());
        }
    }
    public function bulkApprove()
    {
        try {
            StorageApplication::whereIn('id', $this->selectedApplications)->update([
                'storage_application_status' => 'approved'
            ]);

            foreach ($this->selectedApplications as $applicationId) { # Create QR Code
                QRCode::create([
                    'storage_application_id' => $applicationId,
                    'qr_token' => Str::uuid(),
                    'qr_expires_at' => Carbon::now()->addDays(7),
                ]);
            }

            $this->dispatch('application-updated', message: 'Users approved successfully!', type: 'success');
            $this->reset();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('application-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function bulkReject()
    {
        try {
            StorageApplication::whereIn('id', $this->selectedApplications)->update([
                'storage_application_status' => 'rejected'
            ]);

            $this->dispatch('application-updated', message: 'Users approved successfully!', type: 'success');
            $this->reset();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('application-updated', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    public function rejectApplication($id)
    {
        try {
            $application = StorageApplication::findOrFail($id);
            $application->update(['storage_application_status' => 'rejected']);
            $this->dispatch('application-updated', message: 'Application approved successfully!', type: 'success');
            $this->reset();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('application-updated', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            $this->dispatch('application-updated', message: 'Something went wrong. Please try again.', type: 'fail');
            Log::error($e->getMessage());
        }
    }

    #[Computed()]
    public function pendingStorageApplications()
    {
        return StorageApplication::with(['applicant', 'items', 'openArea.storageRoom', 'locker.storageRoom'])->where('storage_application_status', 'pending')->paginate(10);
    }

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.manage-storage-application')
            ->layout($layout)
            ->title($title . ' - Storage Application');
    }
}
