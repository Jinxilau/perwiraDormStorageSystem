<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\ScanLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ScanQr extends Component
{
    public $qrId;

    #[Validate('nullable|uuid')]
    public $scannedToken;

    public function validateQR($token = null)
    {
        $this->validateOnly('scannedToken');

        try {
            if (!empty($token)) $this->scannedToken = $token;

            // 1. Find QR Code
            $qr = QRCode::with('storageApplication.applicant')
                ->where('qr_token', $this->scannedToken)
                ->first();

            // 2. Immediate Validations (Fail Fast)
            if (!$qr) {
                throw new \Exception('Invalid QR Code');
            }
            if ($qr->qr_expires_at->isPast()) {
                throw new \Exception('QR Code has expired');
            }

            // 3. Success State
            $this->qrId = $qr->id;

            // Optional: Tell the user WHO they scanned
            $applicantName = $qr->storageApplication->applicant->name ?? 'Student';
            $this->dispatch('show_toast', message: "Valid QR for $applicantName.", type: "success");

            $this->reset('scannedToken');
        } catch (\Exception $e) {
            $this->qrId = null; // Reset ID on failure so they can't click checkin
            $this->dispatch('show_toast', message: $e->getMessage(), type: "fail");
            Log::error("QR Validation Error: " . $e->getMessage());
        }
    }

    // Log the scan
    public function checkin()
    {
        if (!$this->qrId) return;

        DB::beginTransaction(); // Start Transaction

        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')
                ->lockForUpdate()
                ->findOrFail($this->qrId);
            $application = $qrCode->storageApplication;

            if ($qrCode->status === 'checked_in') {
                DB::rollBack();
                $this->dispatch('show_toast', message: "Already checked in.", type: "fail");
                return;
            }

            // 1. Create Log
            ScanLog::create([
                'qr_code_id' => $this->qrId,
                'event_type' => 'check_in'
            ]);

            // 2. Update QR
            $qrCode->update([
                'status' => 'checked_in',
                'scanned_count' => $qrCode->scanned_count + 1
            ]);

            // 3. Update Storage Status
            if ($application->locker_id) {
                $application->locker()->update(['status' => 'occupied']);
            }
            if ($application->open_area_id) {
                $application->openArea()->update(['status' => 'occupied']);
            }

            DB::commit(); // Save everything

            $this->dispatch('show_toast', message: "Check-in successful!", type: "success");
            $this->reset(['qrId', 'scannedToken']); // Clear statereset();
        } catch (\Exception $e) {
            DB::rollBack(); // Undo changes if error
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "System Error. Please try again.", type: "fail");
        }
    }

    public function checkout()
    {
        if (!$this->qrId) return;

        DB::beginTransaction();

        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')
                ->lockForUpdate()
                ->findOrFail($this->qrId);
            $application = $qrCode->storageApplication;

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
                'qr_code_id' => $this->qrId,
                'event_type' => 'check_out'
            ]);

            // 2. Update QR
            $qrCode->update([
                'status' => 'checked_out',
                'scanned_count' => $qrCode->scanned_count + 1
            ]);

            // 3. Update Storage Status
            if ($application->locker_id) {
                $application->locker()->update(['status' => 'available']);
            }
            if ($application->open_area_id) {
                $application->openArea()->update(['status' => 'available']);
            }

            DB::commit();

            $this->dispatch('show_toast', message: "Check-out successful!", type: "success");
            $this->reset(['qrId', 'scannedToken']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "System Error. Please try again.", type: "fail");
        }
    }

    #[Computed()]
    public function recentLogs()
    {
        return ScanLog::latest()->limit(15)->get();
    }

    public function render()
    {
        return view('livewire.scan-qr');
    }
}
