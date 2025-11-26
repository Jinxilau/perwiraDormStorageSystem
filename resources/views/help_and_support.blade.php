<x-layouts.student title="Help and Support">
    <style>
        .card-number {
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
            width: 45px;
            /* color: black; */
            height: 45px;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            background-color: #e9ecef !important;
        }
    </style>
    <div class="container py-2">
        @include('livewire.includes.student-title2', [
            'title' => 'Help & Support',
            'subtitle' => 'To assist you with guidance related to storage.',
        ])
        {{-- Card 1 :Guide --}}
        <div class="card shadow-lg rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary border-3">
                    <h2 class="text-primary fw-semibold mb-0">1. Storage Guides</h2>
                </div>
                <p class="mb-0 fw-bold fs-5">1. Before Applying for Storage</p>
                <ul>
                    <li>Make sure you are an eligible resident (current Perwira College student or returning student
                        with approved accommodation for the next semester).</li>
                    <li>Prepare details about your items, including:</li>
                    <ul>
                        <li>Estimated size (Small / Medium / Large) and number of boxes or bags (maximum 3 units).</li>
                        <li>Storage type:</li>
                        <ul>
                            <li><strong>Locker</strong> - limited availability, for smaller or personal items</li>
                            <li><strong>Open Area</strong> – for larger items or boxes placed in the shared storage room
                            </li>
                        </ul>
                    </ul>
                    <li>Ensure your items do not include prohibited materials such as food or hazardous
                        substances.</li>
                </ul>

                <p class="mb-0 fw-bold fs-5">2. Submitting the Application</p>
                <ul>
                    <li>Go to the “Storage Application" page.</li>
                    <li>Fill in the required details.</li>
                    <li>Click “Submit” to send your request for approval.</li>
                    <li>You will receive notification/email on status update once your application is reviewed by staff.
                    </li>
                </ul>

                <p class="mb-0 fw-bold fs-5">3. Approval and QR Code</p>
                <ul>
                    <li>Once approved, your application will include a generated QR code in “My Storage” page.</li>
                    <li>This QR code is used for check-in and check-out during storage and retrieval.</li>
                    <li>Keep it safe — you can print it or show it from your mobile device.</li>
                </ul>

                <p class="mb-0 fw-bold fs-5">4. During Check-In</p>
                <ul>
                    <li>Bring your belongings to the storage location during the official storage period.</li>
                    <li>Present your QR code to staff for scanning.</li>
                    <li>Make sure all boxes/bags are properly labeled with Matric Number.</li>
                </ul>

                <p class="mb-0 fw-bold fs-5">5. During Check-Out</p>
                <ul>
                    <li>When collecting your belongings, show the same QR code.</li>
                    <li>Unclaimed items after one (1) week of the new semester may be disposed of by the management.
                    </li>
                </ul>

                <p class="mb-0 fw-bold fs-5">6. Reporting Missing Items</p>
                <ul>
                    <li>Go to the “Report Missing Item” section in PDSS.</li>
                    <li>Provide the details required.</li>
                    <li>Your report will be reviewed by management, and updates will be sent through the
                        system.</li>
                </ul>
            </div>
        </div>

        <!-- Card 2: Technical Support -->
        <div class="card shadow-lg rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary border-3">
                    <h2 class="text-primary fw-semibold mb-0">2. Technical Support</h2>
                </div>

                <div class="border-start border-info border-4 p-3 mb-3">
                    <h4 class="text-info-emphasis mb-3">For issues such as:</h4>
                    <ul class="list-unstyled ps-3">
                        <li class="mb-2 text-info-emphasis">• Login problems</li>
                        <li class="mb-2 text-info-emphasis">• Application submission errors</li>
                        <li class="mb-2 text-info-emphasis">• QR code or check-in issues</li>
                        <li class="text-info-emphasis">• System bugs or missing data</li>
                    </ul>
                </div>

                <div class="bg-light rounded-3 p-3 mb-3 info-item">
                    <span class="fw-semibold text-primary">📧 Email:</span>
                    <span class="text-muted fst-italic">Insert PDSS support email</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Storage Arrangements -->
        <div class="card shadow-lg rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-primary border-3">
                    <h2 class="text-primary fw-semibold mb-0">3. Storage Arrangements</h2>
                </div>

                <div class="border-start border-info border-4 p-3 mb-3">
                    <h4 class="text-info-emphasis mb-3">For requests such as:</h4>
                    <ul class="list-unstyled ps-3">
                        <li class="mb-2 text-info-emphasis">• Storage outside office hours, please contact staff at KKLK</li>
                        <li class="text-info-emphasis">• For Weekend storage, please contact the Majlis Kepimpinan Pelajar (MKP)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</x-layouts.student>
