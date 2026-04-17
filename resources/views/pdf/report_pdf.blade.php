<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report->ReportType }} - {{ $report->ReportID }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            line-height: 1.5;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #0d9488;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #0d9488;
            text-transform: uppercase;
        }
        .meta {
            font-size: 10px;
            color: #64748b;
            margin-top: 5px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #f8fafc;
            font-size: 10px;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #64748b;
            text-transform: uppercase;
        }
        td {
            padding: 10px;
            font-size: 11px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer {
            margin-top: 50px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .summary-box {
            background-color: #f0fdfa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $report->ReportType }}</div>
        <div class="meta">
            Reference: {{ $report->ReportID }} | Generated: {{ $report->GeneratedDate->format('M d, Y H:i:s') }}<br>
            Office: {{ ucwords($report->GeneratedForOffice) }} | Prepared By: {{ $report->user->FName }} {{ $report->user->LName }}
        </div>
    </div>

    @if($report->ReportType === 'Inventory Valuation')
        <div class="summary-box">
            <div style="font-size: 9px; font-weight: bold; color: #0d9488; text-transform: uppercase;">Total System Valuation</div>
            <div style="font-size: 20px; font-weight: bold;">₱{{ number_format($report->Data['TotalValuation'], 2) }}</div>
            <div style="font-size: 10px; color: #64748b;">Total Active Items: {{ $report->Data['TotalActiveItems'] }}</div>
        </div>

        <div class="section-title">Category Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th class="text-center">Batch Count</th>
                    <th class="text-right">Value (PHP)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->Data['CategoryBreakdown'] as $cat => $stats)
                <tr>
                    <td class="font-bold">{{ $cat }}</td>
                    <td class="text-center">{{ $stats['count'] }}</td>
                    <td class="text-right">₱{{ number_format($stats['value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($report->ReportType === 'Receipt Confirmation')
        <div class="summary-box">
            <div style="font-size: 9px; font-weight: bold; color: #0d9488; text-transform: uppercase;">Procurement Verification</div>
            <div style="font-size: 16px; font-weight: bold;">PO: {{ $report->Data['PONumber'] }}</div>
            <div style="font-size: 10px; color: #64748b;">Supplier: {{ $report->Data['Supplier'] }}</div>
        </div>

        <div class="section-title">Item Discrepancy Report</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Ordered</th>
                    <th class="text-center">Received</th>
                    <th class="text-right">Variance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->Data['Items'] as $item)
                <tr>
                    <td class="font-bold">{{ $item['ItemName'] }}</td>
                    <td class="text-center">{{ $item['Ordered'] }}</td>
                    <td class="text-center">{{ $item['Received'] }}</td>
                    <td class="text-right @if($item['Variance'] > 0) color: #ef4444; @endif">
                        {{ $item['Variance'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($report->ReportType === 'Stock Card Ledger')
        <div class="summary-box">
            <div style="font-size: 9px; font-weight: bold; color: #0d9488; text-transform: uppercase;">Item Lifecycle Ledger</div>
            <div style="font-size: 16px; font-weight: bold;">{{ $report->Data['ItemName'] }}</div>
            <div style="font-size: 10px; color: #64748b;">Closing Balance: {{ $report->Data['CurrentBalance'] }} {{ $report->Data['Unit'] }}</div>
        </div>

        <div class="section-title">Transaction History</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type / Ref</th>
                    <th class="text-center">In</th>
                    <th class="text-center">Out</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->Data['Ledger'] as $row)
                <tr>
                    <td>{{ date('M d, Y', strtotime($row['date'])) }}</td>
                    <td>
                        <span class="font-bold">{{ $row['type'] }}</span><br>
                        <span style="font-size: 8px; color: #94a3b8;">Ref: {{ $row['ref'] }}</span>
                    </td>
                    <td class="text-center" style="color: #0d9488; font-weight: bold;">{{ $row['in'] ?: '-' }}</td>
                    <td class="text-center" style="color: #ef4444; font-weight: bold;">{{ $row['out'] ?: '-' }}</td>
                    <td class="text-right font-bold">{{ $row['balance'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated by Iloilo City WMS Analytical Engine. This document is for official use only.
    </div>
</body>
</html>
