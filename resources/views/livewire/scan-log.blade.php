<div>
    {{-- Close your eyes. Count to one. That is how long forever feels. --}}
    <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">
        <!-- Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th class="align-middle py-2">
                            Log ID
                        </th>
                        <th class="align-middle py-2">
                            QR Token
                        </th>
                        <th class="align-middle py-2">
                            Application ID
                        </th>
                        <th class="align-middle py-2">
                            Timestamp
                        </th>
                        <th class="align-middle py-2">
                            Student Information
                        </th>
                        <th class="align-middle py-2">
                            Storage Rooms
                        </th>
                        <th class="align-middle py-2">
                            Activty Type
                        </th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @forelse($this->logs as $log)
                        <tr wire:key="row-{{ $log->id }}">
                            <td>
                                #{{ $log->id }} {{-- Log ID --}}
                            </td>
                            <td>
                                {{ $log->qrCode->qr_token }} {{-- QR Token --}}
                            </td>
                            <td>
                                {{ $log->qrCode->storageApplication->id }} {{-- Application ID --}}
                            </td>
                            <td>
                                {{-- Timestamp --}}
                                <div style="white-space: nowrap">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y') : 'N/A' }}
                                </div>
                                <div>
                                    {{ $log->created_at ? $log->created_at->format('h:i A') : '' }}
                                </div>
                            </td>
                            <td>
                                {{-- Student Information --}}
                                <div class="d-flex items-center gap-1">
                                    <div class="rounded-circle bg-secondary-subtle d-flex justify-content-center align-items-center"
                                        style="height: 30px; width:30px">
                                        {{ substr($log->qrCode->storageApplication->applicant->name ?? 'N/A', 0, 2) }}
                                    </div>
                                    <div>
                                        <div>
                                            {{ $log->qrCode->storageApplication->applicant->name ?? 'N/A' }}
                                        </div>
                                        <div>
                                            {{ $log->qrCode->storageApplication->applicant->email ?? 'N/A' }}
                                        </div>
                                        <div>
                                            ID: {{ $log->qrCode->storageApplication->applicant->matric_no ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{-- Storage Rooms --}}
                                {{ $log->qrCode->storageApplication->storageRoom->id }}
                            </td>
                            <td>
                                {{-- Activty Type --}}
                                {{ $log->event_type }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-3 py-5 text-center">
                                <div>
                                    <i class="bi bi-file-earmark-text mx-auto" style="font-size: 60px"></i>
                                    <h3 class="mt-4">No Scan Logs</h3>
                                    <p class="mt-2">There are no scan logs to view.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $this->logs->links('vendor.livewire.bootstrap') }}
        </div>
    </div>
</div>
