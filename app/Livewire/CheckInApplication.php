<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\ScanLog;
use App\Models\StorageApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class CheckInApplication extends Component
{
    use WithFileUploads;

    public $application; # StorageApplication instance

    public $checkin_item_photo; # The uploaded check-in photo
    public $storage_zone; # The storage zone input

    public function mount(StorageApplication $application)
    {
        $this->application = $application->load(['applicant', 'storedItems', 'locker.storageRoom', 'openArea.storageRoom', 'qrCode.scanLogs', 'semester',]);

        if ($application->qrCode) {
            $this->storage_zone = $application->qrCode->storage_zone;
        }
    }

    public function confirmCheckIn()
    {
        $this->validate(
            [
                'checkin_item_photo' => 'nullable|image|max:5120', // Max 5MB
                'storage_zone'       => 'nullable|string|max:255',
            ]
        );

        // Always reload relationships to avoid stale state
        $this->application->load(['qrCode', 'locker', 'openArea']);

        if (! $this->application->qrCode) {
            $this->dispatch('show_toast', message: 'No QR Code linked to this application.', type: 'fail');
            return;
        }

        try {
            DB::transaction(function () {

                /** @var QRCode $qr */
                $qr = QRCode::where('id', $this->application->qrCode->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // ❗ Absolute guard
                if ($qr->status === 'checked_in') {
                    throw new \Exception('This application is already checked in.');
                }

                // 📸 Store photo
                $photoPath = $this->checkin_item_photo->store(
                    'checkin-photos',
                    'public'
                );

                // 🧾 Update QR Code (single update)
                $qr->update([
                    'checkin_item_photo' => $photoPath,
                    'storage_zone'       => $this->storage_zone,
                    'status'             => 'checked_in',
                ]);

                $qr->increment('scanned_count');

                // 🪵 Log scan
                ScanLog::create([
                    'qr_code_id' => $qr->id,
                    'event_type' => 'check_in',
                ]);

                // 🏷 Update storage allocation
                if ($this->application->locker) {
                    $this->application->locker->update(['status' => 'occupied']);
                }

                if ($this->application->openArea) {
                    $this->application->openArea->update(['status' => 'occupied']);
                }
            });

            $this->dispatch('show_toast', message: 'Item checked in successfully.', type: 'success');

            // Optional: reset upload field
            $this->reset('checkin_item_photo');
            session()->flash('success', 'Check-in successful! Page refreshed and filters cleared.');

            return $this->redirectRoute('check_in_application', ['application' => $this->application->id], navigate: true);
        } catch (\Throwable $e) {
            Log::error('Check-in failed', [
                'application_id' => $this->application->id,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('show_toast', message: $e->getMessage(), type: 'fail');
        }
    }

    public function confirmCheckOut()
    {
        if (!$this->application->qrCode) {
            $this->dispatch('show_toast', message: "Error: No QR Code found.", type: "fail");
            return;
        }

        DB::beginTransaction();

        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')
                ->lockForUpdate()
                ->findOrFail($this->application->qrCode->id);

            // Logic Check (Order matters!)
            if ($qrCode->status === 'checked_out') {
                DB::rollBack();
                $this->dispatch('show_toast', message: "Already checked out.", type: "fail");
                return;
            }

            if ($qrCode->status !== 'checked_in') {
                DB::rollBack();
                $this->dispatch('show_toast', message: "Item must be Checked In before it can be Checked Out.", type: "fail");
                return;
            }

            // 1. Create Log
            ScanLog::create([
                'qr_code_id' => $this->application->qrCode->id,
                'event_type' => 'check_out'
            ]);

            // 2. Update QR
            $qrCode->update(['status' => 'checked_out',]);
            $qrCode->increment('scanned_count');

            // 3. Update Storage Status
            if ($this->application->locker_id) {
                $this->application->locker()->update(['status' => 'available']);
            }
            if ($this->application->open_area_id) {
                $this->application->openArea()->update(['area_status' => 'available']);
            }

            DB::commit();

            $this->dispatch('show_toast', message: "Check-out successful!", type: "success");
            session()->flash('success', 'Check-out successful! Page refreshed and filters cleared.');

            return $this->redirectRoute('check_in_application', ['application' => $this->application->id], navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "System Error. Please try again.", type: "fail");
        }
    }

    public function render()
    {
        return view('livewire.check-in-application');
    }
}
