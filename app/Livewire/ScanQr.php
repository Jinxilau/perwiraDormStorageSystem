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
    public $storageApp; # QR's storage application
    public $qrId;

    #[Validate('required|uuid')]
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

            // Find Storage Application
            $this->storageApp = $qr->storageApplication;

            $this->reset('scannedToken');
        } catch (\Exception $e) {
            $this->dispatch('scanned', message: "Something went wrong. Please try again.", type: "fail");
            Log::error($e->getMessage());
        }
    }

    // Log the scan
    public function checkin()
    {
        ScanLog::create([
            'qr_code_id' => $this->qrId,
            'event_type' => 'check_in'
        ]);

        $this->dispatch('scanned', message: "Check in successfully!", type: "success");
        $this->reset();
    }

    public function checkout()
    {
        ScanLog::create([
            'qr_code_id' => $this->qrId,
            'event_type' => 'check_out'
        ]);

        $this->dispatch('scanned', message: "Check out successfully!", type: "success");
        $this->reset();
    }

    #[Computed()]
    public function recentLogs() {
        return ScanLog::latest()->limit(15)->get();
    }

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.scan-qr')
            ->layout($layout)->title($title . ' - Scan QR');
    }
}
