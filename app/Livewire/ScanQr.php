<?php

namespace App\Livewire;

use App\Models\QRCode;
use App\Models\ScanLog;
use Illuminate\Support\Facades\Auth;
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

            // Find QR Code
            $qr = QRCode::with('storageApplication.applicant')->where('qr_token', $this->scannedToken)->first();
            $this->qrId = $qr->id;

            // Validate QR Code
            if (!$qr) {
                throw new \Exception('Invalid QR Code');
            }
            if ($qr->qr_expires_at->isPast()) {
                throw new \Exception('QR Code expired');
            }

            $this->dispatch('show_toast', message: "Valid QR Code.", type: "success");
            $this->reset('scannedToken');
        } catch (\Exception $e) {
            $this->dispatch('show_toast', message: "Something went wrong. Please try again.", type: "fail");
            Log::error($e->getMessage());
        }
    }

    // Log the scan
    public function checkin()
    {
        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')->findOrFail($this->qrId);
            $application = $qrCode->storageApplication;

            if ($qrCode->status === 'checked_in') {
                $this->dispatch('show_toast', message: "This storage has already been checked in.", type: "fail");
                return;
            }

            ScanLog::create([
                'qr_code_id' => $this->qrId,
                'event_type' => 'check_in'
            ]);

            $qrCode->update([
                'status' => 'checked_in',
                'scanned_count' => $qrCode->scanned_count + 1
            ]);

            if ($application->locker_id) {
                $application->locker->update(['status' => 'occupied']);
            }
            if ($application->open_area_id) {
                $application->openArea->update(['status' => 'occupied']);
            }

            $this->dispatch('show_toast', message: "Check in successfully!", type: "success");
            $this->reset();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "Something went wrong. Please try again.", type: "fail");
        }
    }

    public function checkout()
    {
        try {
            $qrCode = QRCode::with('storageApplication.locker', 'storageApplication.openArea')->findOrFail($this->qrId);
            $application = $qrCode->storageApplication;

            // Check Status
            if ($qrCode->status !== 'checked_in') {
                $this->dispatch('show_toast', message: "This storage is not checked in yet.", type: "fail");
                return;
            }
            if ($qrCode->status === 'checked_out') {
                $this->dispatch('show_toast', message: "This storage has already been checked out.", type: "fail");
                return;
            }

            ScanLog::create([
                'qr_code_id' => $this->qrId,
                'event_type' => 'check_out'
            ]);

            $qrCode->update([
                'status' => 'checked_out',
                'scanned_count' => $qrCode->scanned_count + 1
            ]);

            if ($application->locker_id) {
                $application->locker->update(['status' => 'available']);
            }
            if ($application->open_area_id) {
                $application->openArea->update(['status' => 'available']);
            }

            $this->dispatch('show_toast', message: "Check out successfully!", type: "success");
            $this->reset();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $this->dispatch('show_toast', message: "Something went wrong. Please try again.", type: "fail");
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
