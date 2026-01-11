<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PDSS Storage Insight Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            background: linear-gradient(135deg, #4472C4 0%, #2c5aa0 100%);
            color: rgb(0, 0, 0);
            border-radius: 8px;
        }
        
        .report-metadata {
            background: #f0f4f8;
            padding: 18px;
            border-left: 5px solid #4472C4;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .report-metadata h4 {
            color: #4472C4;
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .report-metadata p {
            font-size: 11px;
            color: #555;
            margin-bottom: 6px;
            line-height: 1.5;
        }
        
        .report-metadata p strong {
            color: #333;
            font-weight: 600;
            display: inline-block;
            width: 140px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .metric-row {
            display: table-row;
        }
        
        .metric-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .metric-card h3 {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: normal;
        }
        
        .metric-card .value {
            font-size: 22px;
            font-weight: bold;
            color: #4472C4;
        }
        
        .metric-card .subtext {
            font-size: 9px;
            color: #999;
            margin-top: 5px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 16px;
            color: #4472C4;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
            font-weight: bold;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .data-table th {
            background-color: #4472C4;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 11px;
        }
        
        .data-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .chart-placeholder {
            background: #f0f0f0;
            padding: 40px;
            text-align: center;
            color: #999;
            border: 1px dashed #ccc;
            margin: 15px 0;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 12px 0;
            border-top: 2px solid #4472C4;
            background: white;
        }
        
        .footer p {
            margin: 0;
            line-height: 1.4;
        }
        
        .two-column {
            display: table;
            width: 100%;
        }
        
        .column {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #4472C4;
            margin: 15px 0;
        }
        
        .info-box h4 {
            color: #4472C4;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .info-box p {
            font-size: 11px;
            color: #666;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(68, 114, 196, 0.05);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">PDSS</div>

    <!-- Header with PDSS Branding -->
    <div class="header">
        <img src="{{ Vite::asset('resources/images/icons/perwira_logo.png') }}" alt="Perwira Dorm Storage System (PDSS)" style="height: 150px; margin-bottom: 10px;">
    </div>

    <!-- Report Metadata Block -->
    <div class="report-metadata">
        <h4>Report Information</h4>
        <p><strong>System:</strong> Perwira Dorm Storage System (PDSS)</p>
        <p><strong>Report Type:</strong> Administrative Storage Analytics</p>
        <p><strong>Generated By:</strong> PDSS Reporting Module</p>
        <p><strong>Report Date:</strong> {{ now()->format('l, F d, Y') }}</p>
        <p><strong>Academic Year:</strong> {{ now()->year }}/{{ now()->year + 1 }}</p>
    </div>

    <!-- Key Metrics -->
    <div class="section">
        <h2 class="section-title">Key Performance Indicators</h2>
        <div class="metrics-grid">
            <div class="metric-row">
                <div class="metric-card">
                    <h3>Total Applications</h3>
                    <div class="value">{{ number_format($metrics['totalApplications']) }}</div>
                    <div class="subtext">All time</div>
                </div>
                <div class="metric-card">
                    <h3>Active Items</h3>
                    <div class="value">{{ number_format($metrics['activeItems']) }}</div>
                    <div class="subtext">Currently checked in</div>
                </div>
                <div class="metric-card">
                    <h3>Locker Occupancy</h3>
                    <div class="value">{{ $metrics['lockerOccupancyRate'] }}%</div>
                    <div class="subtext">Current rate</div>
                </div>
                <div class="metric-card">
                    <h3>Unclaimed Items</h3>
                    <div class="value">{{ number_format($metrics['unclaimedItems']) }}</div>
                    <div class="subtext">Needs attention</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Demand Overview -->
    @if(!empty($storageDemand['labels']))
    <div class="section">
        <h2 class="section-title">Storage Demand by Semester</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Applications</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = array_sum($storageDemand['values']);
                @endphp
                @foreach($storageDemand['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($storageDemand['values'][$index]) }}</td>
                    <td>{{ $total > 0 ? number_format(($storageDemand['values'][$index] / $total) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Item Size Breakdown -->
    @if(!empty($itemSizes['labels']))
    <div class="section">
        <h2 class="section-title">Item Size Distribution</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Size Category</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSizes = array_sum($itemSizes['values']);
                @endphp
                @foreach($itemSizes['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($itemSizes['values'][$index]) }}</td>
                    <td>{{ $totalSizes > 0 ? number_format(($itemSizes['values'][$index] / $totalSizes) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Item Type Breakdown -->
    @if(!empty($itemTypes['labels']))
    <div class="section">
        <h2 class="section-title">Item Type Breakdown</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item Type</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalTypes = array_sum($itemTypes['values']);
                @endphp
                @foreach($itemTypes['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($itemTypes['values'][$index]) }}</td>
                    <td>{{ $totalTypes > 0 ? number_format(($itemTypes['values'][$index] / $totalTypes) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Item Condition Breakdown -->
    @if(!empty($itemConditions['labels']))
    <div class="section">
        <h2 class="section-title">Item Condition Analysis</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Condition</th>
                    <th>Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalConditions = array_sum($itemConditions['values']);
                @endphp
                @foreach($itemConditions['labels'] as $index => $label)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($itemConditions['values'][$index]) }}</td>
                    <td>{{ $totalConditions > 0 ? number_format(($itemConditions['values'][$index] / $totalConditions) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Check-in/Check-out Activity -->
    @if(!empty($checkActivity['labels']))
    <div class="section">
        <h2 class="section-title">Check-in/Check-out Activity</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Check-ins</th>
                    <th>Check-outs</th>
                    <th>Net Active</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checkActivity['labels'] as $index => $label)
                @php
                    $checkIn = $checkActivity['checkIn'][$index];
                    $checkOut = $checkActivity['checkOut'][$index];
                    $net = $checkIn - $checkOut;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($checkIn) }}</td>
                    <td>{{ number_format($checkOut) }}</td>
                    <td style="color: {{ $net >= 0 ? '#28a745' : '#dc3545' }}; font-weight: bold;">
                        {{ $net >= 0 ? '+' : '' }}{{ number_format($net) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Locker vs Open Space -->
    @if(!empty($lockerVsOpenSpace['labels']))
    <div class="section">
        <h2 class="section-title">Locker vs Open Space Usage</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Lockers</th>
                    <th>Open Spaces</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lockerVsOpenSpace['labels'] as $index => $label)
                @php
                    $lockers = $lockerVsOpenSpace['locker'][$index];
                    $openSpace = $lockerVsOpenSpace['openSpace'][$index];
                    $total = $lockers + $openSpace;
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ number_format($lockers) }} ({{ $total > 0 ? number_format(($lockers / $total) * 100, 1) : 0 }}%)</td>
                    <td>{{ number_format($openSpace) }} ({{ $total > 0 ? number_format(($openSpace / $total) * 100, 1) : 0 }}%)</td>
                    <td><strong>{{ number_format($total) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Locker Occupancy Rates -->
    @if(!empty($occupancy['labels']))
    <div class="section">
        <h2 class="section-title">Locker Occupancy Trends</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Occupancy Rate</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($occupancy['labels'] as $index => $label)
                @php
                    $rate = $occupancy['rates'][$index];
                    $status = $rate >= 90 ? 'High' : ($rate >= 70 ? 'Moderate' : 'Low');
                    $color = $rate >= 90 ? '#dc3545' : ($rate >= 70 ? '#ffc107' : '#28a745');
                @endphp
                <tr>
                    <td>{{ $label }}</td>
                    <td><strong>{{ number_format($rate, 2) }}%</strong></td>
                    <td style="color: {{ $color }}; font-weight: bold;">{{ $status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Summary Info -->
    <div class="section">
        <div class="info-box">
            <h4>About This Report</h4>
            <p>This comprehensive analytics report is generated by the Perwira Dorm Storage System (PDSS) to provide 
            detailed insights into storage facility utilization, trends, and operational metrics. The data presented 
            helps administrators make informed decisions regarding resource allocation, capacity planning, and service improvements.</p>
        </div>
        
        <div class="info-box" style="margin-top: 15px;">
            <h4>Confidentiality Notice</h4>
            <p>This document contains confidential information intended solely for authorized personnel of Perwira Residential 
            College and UTHM administration. Unauthorized distribution, copying, or disclosure is strictly prohibited.</p>
        </div>
    </div>

    <!-- Footer with Branding -->
    <div class="footer">
        <p><strong>Perwira Dorm Storage System (PDSS)</strong> • Confidential Administrative Report</p>
        <p>Perwira Residential College, Universiti Tun Hussein Onn Malaysia</p>
    </div>
</body>
</html>