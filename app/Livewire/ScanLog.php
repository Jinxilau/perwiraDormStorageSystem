<?php

namespace App\Livewire;

use App\Models\ScanLog as ModelsScanLog;
use App\Models\StorageRoom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ScanLog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';
    #[Url(except: '')]
    public $event_type = '';
    #[Url(except: '')]
    public $storage_room = '';
    #[Url(except: '')]
    public $fromRange = '';
    #[Url(except: '')]
    public $toRange = '';
    #[Url(except: 'latest')]
    public $sortDate = 'latest';
    #[Url(except: '')]
    public $sortName = '';

    public $paginationInt = 10;

    public function clearFilters()
    {
        $this->search = '';
        $this->event_type = '';
        $this->storage_room = '';
        $this->fromRange = '';
        $this->toRange = '';
        $this->sortDate = 'latest';
        $this->sortName = '';

        $this->paginationInt = 10;
    }


    #[Computed()]
    public function logs()
    {
        $query = ModelsScanLog::query()->with([
            'qrCode.storageApplication' => function ($q) {
                $q->with(['applicant', 'locker.storageRoom', 'openArea.storageRoom', 'storedItems']);
            },
        ]);

        if ($this->search) {
            $search = $this->search;

            $query->where(function ($q) use ($search) {
                if (is_numeric($search)) {
                    $q->whereRelation('qrCode', 'id', $search);
                } else{
                    $q->orWhereRelation('qrCode.storageApplication.applicant', 'name', 'like', "%{$search}%")
                        ->orWhereRelation('qrCode.storageApplication.applicant', 'matric_no', 'like', "%{$search}%");
                }
            });
        }


        if (!empty($this->event_type)) {
            $query->where('event_type', $this->event_type);
        }

        if (!empty($this->storage_room)) {
            $storageRoom = $this->storage_room; // local copy for closure use

            $query->where(function ($q) use ($storageRoom) {
                $q->whereHas('qrCode.storageApplication.openArea.storageRoom', function ($roomQuery) use ($storageRoom) {
                    $roomQuery->where('id', $storageRoom);
                })
                    ->orWhereHas('qrCode.storageApplication.locker.storageRoom', function ($roomQuery) use ($storageRoom) {
                        $roomQuery->where('id', $storageRoom);
                    });
            });
        }

        if ($this->fromRange && $this->toRange) {
            $query->whereBetween('created_at', [
                Date::parse($this->fromRange)->startOfDay(),
                Date::parse($this->toRange)->endOfDay(),
            ]);
        } elseif ($this->fromRange) {
            $query->whereDate('created_at', '>=', $this->fromRange);
        } elseif ($this->toRange) {
            $query->whereDate('created_at', '<=', $this->toRange);
        }

        if ($this->sortDate === 'latest') {
            $query->oldest();
        } elseif ($this->sortDate === 'oldest') {
            $query->latest();
        }

        if ($this->sortName) {
            $query->orderBy(
                User::select('name')
                    ->whereColumn('users.id', 'storage_applications.applicant_id'),
                $this->sortName === 'desc' ? 'desc' : 'asc'
            );
        }

        return $query->paginate($this->paginationInt);
    }

    #[Computed()]
    public function todayScans()
    {
        return ModelsScanLog::whereDate('created_at', now())->count();
    }

    #[Computed()]
    public function rooms()
    {
        return StorageRoom::all();
    }

    public function render()
    {
        $layout = Auth::user()->role_id === 1 ? 'layouts.admin' : 'layouts.staff';
        $title = Auth::user()->role_id === 1 ? 'Admin' : 'Staff';

        return view('livewire.scan-log')->layout($layout)->title($title . ' - Scan Logs');
    }
}
