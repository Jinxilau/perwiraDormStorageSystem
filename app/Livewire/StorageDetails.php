<?php

namespace App\Livewire;

use App\Models\Locker;
use App\Models\QRCode;
use App\Models\StorageApplication;
use App\Notifications\ApproveStorageApplicationNotification;
use App\Notifications\RejectStorageApplicationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy()]
class StorageDetails extends Component
{
    public $application;

    public $rejectionNote = '';

    public $modalData2; # application id for modal reject

    public function mount(StorageApplication $application)
    {
        $this->application = $application->load(['applicant', 'storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester',]);
    }

    private function processApproval(StorageApplication $application)
    {
        // Handle Locker Applications
        if ($application->locker_id) {
            // Lock the locker row to prevent others from reading it while we check
            $locker = Locker::lockForUpdate()->find($application->locker_id);

            if (!$locker) {
                throw new \Exception("Locker not found for Application ID: {$application->id}");
            }

            // CHECK: Is the locker actually available?
            if ($locker->status !== 'available') {
                throw new \Exception("Locker {$locker->code} is already occupied or under maintenance.");
            }

            // If safe, proceed to update locker status
            $locker->update(['status' => 'reserved']);
            // ... perform other assignment logic ...
        }

        //Handle Open Area Applications
        if ($application->open_area_id && $application->openArea->area_status !== 'available') {
            throw new \Exception("Open Area {$application->openArea->storageRoom->room_name} is already occupied or under maintenance.");
        }

        // 1. Create QR Code
        QRCode::create([
            'storage_application_id' => $application->id,
            'qr_token' => Str::uuid(),
            'qr_expires_at' => Carbon::now()->addDays(7)
        ]);

        // 3. Update Application Status
        $application->update([
            'storage_application_status' => 'approved',
            'approver_id' => Auth::id()
        ]);

        // 4. Notification
        // Wrap in try-catch if you don't want email failures to roll back the database transaction
        try {
            $application->applicant->notify(new ApproveStorageApplicationNotification($application));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        // 5. Activity Log (Standardized)
        // Assuming you have the relationship set up as seen in your first method
        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'approved', // Standardized action name
            'description' => "Approved application ID: {$application->id}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function approveApplication()
    {
        try {
            DB::transaction(function () {
                $this->processApproval($this->application);
            });

            $this->dispatch('show_toast', message: 'Application approved successfully!', type: 'success');
            $this->reset(['selectedApplications']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Approval Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: "Approval Error: {$e->getMessage()}", type: 'fail');
        }
    }

    private function processRejection(StorageApplication $application)
    {
        // 1. Update Application Status
        $application->update([
            'storage_application_status' => 'rejected',
            'note' => $this->rejectionNote,
            'approver_id' => Auth::id()
        ]);

        // 3. Notification (Optional but recommended)
        try {
            $application->applicant->notify(new RejectStorageApplicationNotification($application, $this->rejectionNote));
        } catch (\Exception $e) {
            Log::error("Notification failed for App ID {$application->id}: " . $e->getMessage());
        }

        // 4. Activity Log
        $application->activityLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'description' => "Rejected application ID: {$application->id}. Note: {$this->rejectionNote}",
            'ip_address' => request()->ip(),
        ]);
    }

    public function rejectApplication()
    {
        $this->validate(['rejectionNote' => 'nullable|string|max:500']);

        try {
            DB::transaction(function () {
                $this->processRejection($this->application);
            });

            $this->dispatch('close-modal');
            $this->dispatch('show_toast', message: 'Application rejected successfully!', type: 'success');
            $this->reset(['rejectionNote', 'selectedApplications']); // Good practice to reset inputs

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('show_toast', message: 'Application not found.', type: 'fail');
        } catch (\Exception $e) {
            Log::error('Rejection Error: ' . $e->getMessage());
            $this->dispatch('show_toast', message: 'Something went wrong. Please try again.', type: 'fail');
        }
    }

    protected function getCompetingApplications()
    {
        // Only makes sense for locker applications
        if (!$this->application->locker_id) {
            return collect();
        }

        return StorageApplication::with('applicant')
            ->where('locker_id', $this->application->locker_id)
            ->where('semester_id', $this->application->semester_id)
            ->where('storage_application_status', 'pending')
            ->orderBy('created_at')       // fairness
            ->get();
    }

    public function render()
    {
        return view('livewire.storage-details');
    }
}
