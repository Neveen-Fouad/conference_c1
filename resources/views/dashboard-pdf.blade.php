<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
        }

        h1 {
            color: #0F1B33;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .subtitle {
            color: #6B7280;
            font-size: 11px;
            margin-bottom: 18px;
        }

        h2 {
            color: #0F1B33;
            font-size: 14px;
            font-weight: bold;
            margin-top: 22px;
            margin-bottom: 8px;
            border-bottom: 1px solid #E5E7EB;
            padding-bottom: 4px;
        }

        table.cards {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px 6px;
            margin-bottom: 4px;
        }

        table.cards td {
            background-color: #F7F9FC;
            border: 0.75px solid #E5E7EB;
            border-radius: 4px;
            padding: 10px 12px;
            width: 25%;
            vertical-align: top;
        }

        table.cards td.empty {
            background-color: transparent;
            border: none;
        }

        .card-label {
            color: #6B7280;
            font-size: 8px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .card-value {
            color: #0F1B33;
            font-size: 16px;
            font-weight: bold;
            margin-top: 4px;
        }

        .card-value.green {
            color: #16A34A;
        }

        .card-value.blue {
            color: #2F6FED;
        }

        .chart-wrapper {
            margin-top: 10px;
            padding: 8px;
            background-color: #FFFFFF;
            border: 0.75px solid #E5E7EB;
            border-radius: 4px;
        }

        .chart {
            width: 100%;
            display: block;
        }

        .chart.half {
            width: 60%;
            margin: 0 auto;
        }

        .footer {
            margin-top: 24px;
            padding-top: 8px;
            border-top: 0.75px solid #E5E7EB;
            font-size: 8px;
            color: #9CA3AF;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard Report</h1>
    <div class="subtitle">{{ $stats['period_label'] }} &middot; Generated {{ $stats['generated_at'] }}</div>

    <h2>Trips &amp; Revenue</h2>
    <table class="cards">
        <tr>
            <td>
                <div class="card-label">Total Trips</div>
                <div class="card-value">{{ number_format($stats['trip_stats']['total_trips']) }}</div>
            </td>
            <td>
                <div class="card-label">Trips This Month</div>
                <div class="card-value">{{ number_format($stats['trip_stats']['monthly_trips']) }}</div>
            </td>
            <td>
                <div class="card-label">Favorited Trips</div>
                <div class="card-value">{{ number_format($stats['trip_stats']['favorite_trips']) }}</div>
            </td>
            <td>
                <div class="card-label">Avg. Budget</div>
                <div class="card-value">${{ number_format($stats['trip_stats']['average_budget'], 2) }}</div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="card-label">Total Revenue</div>
                <div class="card-value green">${{ number_format($stats['trip_stats']['total_revenue'], 2) }}</div>
            </td>
            <td>
                <div class="card-label">Revenue This Month</div>
                <div class="card-value green">${{ number_format($stats['trip_stats']['monthly_revenue'], 2) }}</div>
            </td>
            <td class="empty"></td>
            <td class="empty"></td>
        </tr>
    </table>

    <div class="chart-wrapper">
        <img class="chart" src="{{ $charts['revenue_trend'] }}">
    </div>

    <div class="chart-wrapper">
        <img class="chart" src="{{ $charts['destinations'] }}">
    </div>

    <h2>Users</h2>
    <table class="cards">
        <tr>
            <td>
                <div class="card-label">Total Users</div>
                <div class="card-value">{{ number_format($stats['user_stats']['total_users']) }}</div>
            </td>
            <td>
                <div class="card-label">New This Month</div>
                <div class="card-value">{{ number_format($stats['user_stats']['monthly_users']) }}</div>
            </td>
            <td>
                <div class="card-label">Verified</div>
                <div class="card-value blue">{{ number_format($stats['user_stats']['verified_users']) }}</div>
            </td>
            <td>
                <div class="card-label">Unverified</div>
                <div class="card-value">{{ number_format($stats['user_stats']['unverified_users']) }}</div>
            </td>
        </tr>
    </table>

    <div class="chart-wrapper">
        <img class="chart half" src="{{ $charts['verification'] }}">
    </div>

    <div class="footer">
        Generated automatically &middot; Admin Dashboard
    </div>
</body>
</html>