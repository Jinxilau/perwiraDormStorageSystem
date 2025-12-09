<div>
    <style>
        /* Card Styling */
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.06);
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .card-header {
            color: white;
            border: none;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.25rem 1.5rem;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .card-body {
            padding: 2rem;
        }

        /* Section Titles */
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #212529;
            margin: 2rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .section-title i {
            color: #667eea;
            font-size: 1.2rem;
        }

        /* Form Controls */

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Checkbox Cards */
        .form-check-card .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            cursor: pointer;
        }

        .form-check-card .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }

        .form-check-card .form-check-label {
            cursor: pointer;
            font-weight: 500;
            margin-left: 0.5rem;
        }

        /* Button Styling */
        .btn-primary {
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:active {
            transform: translateY(0);
        }
    </style>

    @include('livewire.includes.header2', [
        'title' => 'Setting',
        'subtitle' => 'Configure general settings, operating hours, and system features.',
    ])
    <div class="container">

        <div class="card">
            <div class="card-header bg-dark">
                <h5><i class="bi bi-sliders me-2"></i>General Configuration</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <!-- Feature Management Section -->
                    <h5 class="section-title">
                        <i class="bi bi-toggles"></i>
                        Feature Management
                    </h5>
                    <div class="mb-3">
                        <div class="form-check form-check-card">
                            <input class="form-check-input" type="checkbox" wire:model="storage_application_enabled"
                                id="storageCheck">
                            <label class="form-check-label" for="storageCheck">
                                Enable Storage Application
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-check-card">
                            <input class="form-check-input" type="checkbox" wire:model="missing_report_enabled"
                                id="missingCheck">
                            <label class="form-check-label" for="missingCheck">
                                Enable Missing Report
                            </label>
                        </div>
                    </div>

                    <!-- Operating Hours Section -->
                    {{-- <h5 class="section-title">
                        <i class="bi bi-clock"></i>
                        Operating Hours
                    </h5>
                    @foreach ($this->storageRooms as $room)
                        <fieldset>
                            <legend class="form-label">Room {{ $room->room_name }}</legend>
                            <div class="row" wire:key="row-{{ $room->id }}">
                                <div class="col-md-6 mb-3">
                                    <label for="openingHour" class="form-label small text-muted">Opening Hour</label>
                                    <input type="time" wire:model.live="rooms.{{ $room->id }}.opening_hour"
                                        class="form-control" id="openingHour">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="closingHour" class="form-label small text-muted">Closing Hour</label>
                                    <input type="time" wire:model="rooms.{{ $room->id }}.closing_hour"
                                        class="form-control" id="closingHour">
                                </div>
                            </div>
                        </fieldset>
                    @endforeach --}}

                    <!-- Announcement & Timeline Section, this section is replaced using whatsapp workflow -->
                    {{-- <h5 class="section-title">
                        <i class="bi bi-megaphone"></i>
                        Announcement & Timeline
                    </h5>
                    <div class="mb-3">
                        <label for="announcementText" class="form-label">Announcement Text</label>
                        <textarea wire:model="announcement_text" class="form-control" id="announcementText" rows="3"
                            placeholder="Enter announcement message for users...">System maintenance scheduled for this weekend.</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="timelineDetails" class="form-label">Storage & Receiving Timeline Details</label>
                        <textarea wire:model="timeline_details" class="form-control" id="timelineDetails" rows="3"
                            placeholder="Enter timeline details for storage and receiving process...">Items received before 2 PM will be processed on the same day.</textarea>
                    </div> --}}

                    {{-- Save Button --}}
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="last-updated">
                            <i class="bi bi-clock-history me-1"></i>Last updated:
                            {{ optional(\App\Models\Setting::latest('updated_at')->first())->updated_at?->diffForHumans() ?? 'Never' }}
                        </span>
                        <button type="submit" class="btn btn-primary" wire:click="save">
                            <i class="bi bi-check-lg me-2"></i>Save Changes
                        </button>
                    </div> 
                </form>
            </div>
        </div>
    </div>

</div>
