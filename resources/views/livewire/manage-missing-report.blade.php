<div>
    {{-- Because she competes with no one, no one can compete with her. --}}
    @include('livewire.includes.header2', [
        'title' => 'Missing Report Management',
        'subtitle' => 'View, verify, and take action on all submitted missing item reports.',
    ])

    <div class="container">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden container-fluid">
            <!-- Table -->
            <div class="table-responsive-xxl">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th class="align-middle py-2">
                                Report ID
                            </th>
                            <th class="align-middle py-2">
                                User Details
                            </th>
                            <th class="align-middle py-2">
                                Contact
                            </th>
                            <th class="align-middle py-2">
                                Missing Item
                            </th>
                            <th class="align-middle py-2">
                                Storage Details
                            </th>
                            <th class="align-middle py-2">
                                Report Date
                            </th>
                            <th class="align-middle py-2">
                                Status
                            </th>
                            <th class="align-middle py-2">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @forelse($this->missingReports as $report)
                            <tr wire:key="row-{{ $report->id }}">
                                <td> {{-- Report Id --}}
                                    <div style="white-space: nowrap;">#{{ $report->id }}</div>
                                </td>
                                <td> {{-- User Details --}}
                                    <div style="white-space: nowrap;">
                                        {{ $report->storedItems[0]->storageApplication->applicant->id }}
                                    </div>
                                    <div style="white-space: nowrap;">
                                        {{ $report->storedItems[0]->storageApplication->applicant->name }}
                                    </div>
                                    <div style="white-space: nowrap;">
                                        {{ $report->storedItems[0]->storageApplication->applicant->matric_no }}
                                    </div>
                                    <div style="white-space: nowrap;">
                                        {{ ucfirst($user->gender) }}
                                    </div>
                                </td>
                                <td> {{-- Contact --}}
                                    <div style="white-space: nowrap;">
                                        Email:
                                        {{ $report->storedItems[0]->storageApplication->applicant->email ?? 'N/A' }}
                                    </div>
                                    <div>
                                        Phone:
                                        {{ $report->storedItems[0]->storageApplication->applicant->phone_number ?? 'N/A' }}
                                    </div>
                                </td>
                                <td> {{-- Missing Item --}}
                                    @foreach ($report->storedItems as $item)
                                        <div>
                                            Item {{ $loops->iteration }}: {{ $item->name }}
                                        </div>
                                    @endforeach
                                </td>
                                <td> {{-- Storage Details --}}
                                    <div>
                                        Room #{{ $report->storedItems[0]->storageApplication->storageRoom->id }}
                                    </div>
                                    <div>
                                        Room Name:
                                        {{ $report->storedItems[0]->storageApplication->storageRoom->room_name }}
                                    </div>
                                    <div>
                                        Room Status:
                                        {{ $report->storedItems[0]->storageApplication->storageRoom->room_status }}
                                    </div>
                                </td>
                                <td>{{-- Report Date --}}
                                    <div>
                                        {{ $report->created_at }}
                                    </div>
                                </td>
                                <td> {{-- Status --}}
                                    <span
                                        class="badge {{ $report->status === 'pending' ? 'text-bg-warning' : ($report->status === 'resolved' ? 'text-bg-success' : ($report->status === 'investigating' ? 'text-bg-info' : 'text-bg-danger')) }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="align-middle"> {{-- Actions --}}
                                    <div class="d-flex align-items-center gap-2" style="white-space: nowrap">
                                        <button @click="$dispatch('', { })" title="View Details"
                                            class="btn btn-sm border border-secondary" style="padding: 0;"
                                            type="button">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                        <button wire:click="" class="text-success btn btn-sm border border-success"
                                            style="padding: 0;" title="Approve">
                                            <i class="bi bi-check"></i>
                                        </button>

                                        <button wire:click="" class="text-danger btn btn-sm border border-danger"
                                            style="padding: 0;" title="Reject">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-3 py-5 text-center">
                                    <div>
                                        <i class="bi bi-folder2-open mx-auto text-muted" style="font-size: 60px"></i>
                                        <h3 class="mt-4">No Missing Report Found</h3>
                                        <p class="mt-2 text-muted">There are no missing report to review.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $this->missingReports->links('vendor.livewire.bootstrap') }}
            </div>
        </div>

    </div>
</div>
