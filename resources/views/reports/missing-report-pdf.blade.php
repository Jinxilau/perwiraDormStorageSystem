<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Missing Report #{{ $report->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .info-row {
            margin-bottom: 12px;
        }

        .info-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 180px;
        }

        .info-value {
            display: inline-block;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-investigating {
            background-color: #17a2b8;
            color: #fff;
        }

        .status-found {
            background-color: #28a745;
            color: #fff;
        }

        .status-lost {
            background-color: #dc3545;
            color: #fff;
        }

        .status-dismissed {
            background-color: #6c757d;
            color: #fff;
        }

        .item-box {
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 10px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .item-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .item-details {
            font-size: 11px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            margin-right: 5px;
            background-color: #e9ecef;
            color: #495057;
        }

        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .no-data {
            font-style: italic;
            color: #999;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>Missing Report Details</h1>
        <p><strong>Report #{{ $report->id }}</strong></p>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <!-- Report Status Section -->
    <div class="section">
        <div class="section-title">Report Status</div>
        <div class="info-row">
            <span class="info-label">Current Status:</span>
            <span class="status-badge status-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Semester:</span>
            <span class="info-value">
                {{ $report->semester ? $report->semester->academic_year . ' - Semester ' . $report->semester->semester_no : 'N/A' }}
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Report Submitted:</span>
            <span class="info-value">{{ $report->created_at->format('d M Y, h:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Last Updated:</span>
            <span class="info-value">{{ $report->updated_at->format('d M Y, h:i A') }}</span>
        </div>
    </div>

    <!-- Last Seen Information -->
    <div class="section">
        <div class="section-title">Last Seen Information</div>
        <div class="info-row">
            <span class="info-label">When Last Seen:</span>
            <span class="info-value">{{ $lastSeenText }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Last Seen Location:</span>
            <span class="info-value">{{ $report->last_seen_location }}</span>
        </div>
    </div>

    <!-- Discovery Information -->
    <div class="section">
        <div class="section-title">Discovery Information</div>
        <div class="info-row">
            <span class="info-label">When Discovered Missing:</span>
            <span class="info-value">{{ $discoveredMissingText }}</span>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="section">
        <div class="section-title">Additional Information</div>
        <div class="info-row">
            <span class="info-label">Witnesses & Additional Info:</span>
        </div>
        <div style="margin-left: 0; margin-top: 8px;">
            @if ($report->witnesses_and_info)
                {{ $report->witnesses_and_info }}
            @else
                <span class="no-data">No additional information provided</span>
            @endif
        </div>
    </div>

    <!-- Admin Notes -->
    <div class="section">
        <div class="section-title">Admin Notes</div>
        @if ($report->admin_note)
            <div style="margin-top: 8px;">{{ $report->admin_note }}</div>
        @else
            <span class="no-data">No admin notes yet</span>
        @endif
    </div>

    <!-- Related Items -->
    <div class="section">
        <div class="section-title">Related Items</div>
        @if ($report->storedItems && $report->storedItems->count() > 0)
            @foreach ($report->storedItems as $item)
                <div class="item-box">
                    <div class="item-name">{{ $item->item_name }}</div>
                    <div class="item-details">
                        <span class="badge">{{ ucfirst($item->item_type) }}</span>
                        <span class="badge">{{ ucfirst($item->estimated_size) }}</span>
                    </div>
                    @if ($item->item_description)
                        <div style="margin-top: 8px; font-size: 11px;">
                            {{ $item->item_description }}
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <span class="no-data">No items linked to this report</span>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This is a system-generated document from the Lost and Found Management System</p>
        <p>Report ID: {{ $report->id }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</body>

</html>
