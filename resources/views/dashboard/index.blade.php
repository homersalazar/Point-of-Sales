@extends('layouts.app')

@section('content')
    <div class="dash-wrap p-4 sm:p-5">

        {{-- ── Header ── --}}
        <div class="dash-header">
            <div>
                <h1 class="dash-title">Dashboard</h1>
                <p class="dash-sub">Let's check your store today</p>
            </div>
            <div class="dash-date">
                <i class="fa-regular fa-calendar"></i>
                <span id="dash-date-str"></span>
            </div>
        </div>

        {{-- ── Stat Cards ── --}}
        <div class="stat-grid">

            <div class="stat-card" style="--accent:#6366f1">
                <div class="stat-icon" style="background:rgba(99,102,241,.12)">
                    <i class="fa-solid fa-hand-holding-dollar" style="color:#6366f1"></i>
                </div>
                <div class="stat-body">
                    <p class="stat-label">Total Revenue</p>
                    <p class="stat-val">₱{{ number_format($total_revenue, 2) }}</p>
                    <p class="stat-trend {{ $revenuePercentageChange >= 0 ? 'up' : 'down' }}">
                        <i class="fa-solid {{ $revenuePercentageChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($revenuePercentageChange) }}% vs last month
                    </p>
                </div>
            </div>

            <div class="stat-card" style="--accent:#10b981">
                <div class="stat-icon" style="background:rgba(16,185,129,.12)">
                    <i class="fa-brands fa-shopify" style="color:#10b981"></i>
                </div>
                <div class="stat-body">
                    <p class="stat-label">Total Sales</p>
                    <p class="stat-val">₱{{ number_format($total_sales, 2) }}</p>
                    <p class="stat-trend {{ $salesPercentageChange >= 0 ? 'up' : 'down' }}">
                        <i class="fa-solid {{ $salesPercentageChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($salesPercentageChange) }}% vs last month
                    </p>
                </div>
            </div>

            <div class="stat-card" style="--accent:#f59e0b">
                <div class="stat-icon" style="background:rgba(245,158,11,.12)">
                    <i class="fa-solid fa-file-invoice" style="color:#f59e0b"></i>
                </div>
                <div class="stat-body">
                    <p class="stat-label">Total Expense</p>
                    <p class="stat-val">₱{{ number_format($total_expenses, 2) }}</p>
                    <p class="stat-trend {{ $expensesPercentageChange >= 0 ? 'up' : 'down' }}">
                        <i class="fa-solid {{ $expensesPercentageChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($expensesPercentageChange) }}% vs last month
                    </p>
                </div>
            </div>

            <div class="stat-card" style="--accent:#3b82f6">
                <div class="stat-icon" style="background:rgba(59,130,246,.12)">
                    <i class="fa-solid fa-cart-arrow-down" style="color:#3b82f6"></i>
                </div>
                <div class="stat-body">
                    <p class="stat-label">Total Purchase</p>
                    <p class="stat-val">₱{{ number_format($total_purchases, 2) }}</p>
                    <p class="stat-trend {{ $purchasesPercentageChange >= 0 ? 'up' : 'down' }}">
                        <i class="fa-solid {{ $purchasesPercentageChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                        {{ abs($purchasesPercentageChange) }}% vs last month
                    </p>
                </div>
            </div>

        </div>

        {{-- ── Bottom Row ── --}}
        <div class="bottom-row">

            {{-- Chart --}}
            <div class="chart-card">
                <div class="card-head">
                    <div>
                        <h2 class="card-title">Sales & Purchases</h2>
                        <p class="card-sub">Monthly overview for {{ now()->year }}</p>
                    </div>
                    <div class="legend">
                        <span class="leg-dot" style="background:#6366f1"></span><span>Sales</span>
                        <span class="leg-dot" style="background:#f43f5e;margin-left:12px"></span><span>Purchases</span>
                    </div>
                </div>
                <div class="chart-wrap">
                    <canvas id="myChart"></canvas>
                </div>
            </div>

            {{-- Stock Alert --}}
            <div class="stock-card">
                <div class="card-head">
                    <div>
                        <h2 class="card-title">Stock Alert</h2>
                        <p class="card-sub">Low inventory items</p>
                    </div>
                    <span class="badge-count">{{ $lowStockProducts->count() }}</span>
                </div>
                <div class="stock-list">
                    @forelse ($lowStockProducts as $alert)
                        @php
                            if ($alert->stock == 0)          { $sc = 'stock-red';    $label = 'Out'; }
                            elseif ($alert->stock <= 5)      { $sc = 'stock-yellow'; $label = 'Low'; }
                            else                             { $sc = 'stock-green';  $label = 'Ok';  }
                        @endphp
                        <div class="stock-row">
                            <div class="stock-name">
                                <i class="fa-solid fa-box-open stock-ico"></i>
                                {{ $alert->name }}
                            </div>
                            <span class="stock-badge {{ $sc }}">{{ $alert->stock }}</span>
                        </div>
                    @empty
                        <div class="stock-empty">
                            <i class="fa-solid fa-circle-check" style="color:#10b981;font-size:22px"></i>
                            <p>All stock levels are healthy</p>
                        </div>
                    @endforelse
                </div>

                @if ($lowStockProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="stock-pager">{{ $lowStockProducts->links() }}</div>
                @endif
            </div>

        </div>
    </div>

    {{-- ── Styles ── --}}
    <style>
        /* ── Root ── */
        .dash-wrap {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
        }

        /* ── Header ── */
        .dash-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .dash-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: oklch(var(--bc));
            letter-spacing: -0.02em;
            margin: 0;
        }
        .dash-sub {
            font-size: 0.83rem;
            color: oklch(var(--bc) / 0.45);
            margin: 4px 0 0;
        }
        .dash-date {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            color: oklch(var(--bc) / 0.5);
            background: oklch(var(--b1));
            border: 1px solid oklch(var(--b3));
            padding: 7px 13px;
            border-radius: 10px;
            white-space: nowrap;
        }

        /* ── Stat Grid ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }
        @media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 540px)  { .stat-grid { grid-template-columns: 1fr; } }

        .stat-card {
            background: oklch(var(--b1));
            border: 1px solid oklch(var(--b3));
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 16px;
            border-left: 3px solid var(--accent);
            pointer-events: none;
        }
        .stat-card:hover {
            box-shadow: 0 8px 28px oklch(var(--bc) / 0.07);
            transform: translateY(-2px);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }
        .stat-body { flex: 1; min-width: 0; }
        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: oklch(var(--bc) / 0.45);
            margin: 0 0 4px;
        }
        .stat-val {
            font-size: 1.35rem;
            font-weight: 700;
            color: oklch(var(--bc));
            margin: 0 0 6px;
            letter-spacing: -0.02em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-trend {
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
        }
        .stat-trend.up   { color: #10b981; }
        .stat-trend.down { color: #f43f5e; }

        /* ── Bottom Row ── */
        .bottom-row {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 16px;
            align-items: start;
        }
        @media (max-width: 1024px) {
            .bottom-row { grid-template-columns: 1fr; }
        }

        /* ── Shared card shell ── */
        .chart-card,
        .stock-card {
            background: oklch(var(--b1));
            border: 1px solid oklch(var(--b3));
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── Card head ── */
        .card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px 20px 0;
        }
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            color: oklch(var(--bc));
            margin: 0;
            letter-spacing: -0.01em;
        }
        .card-sub {
            font-size: 0.75rem;
            color: oklch(var(--bc) / 0.4);
            margin: 3px 0 0;
        }

        /* ── Legend ── */
        .legend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.75rem;
            color: oklch(var(--bc) / 0.6);
        }
        .leg-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ── Chart ── */
        .chart-wrap {
            padding: 16px 20px 20px;
            position: relative;
            height: 300px;
        }
        .chart-wrap canvas { width: 100% !important; height: 100% !important; }

        /* ── Stock card ── */
        .badge-count {
            background: #f43f5e;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            line-height: 1.6;
        }

        .stock-list {
            padding: 12px 16px 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            max-height: 360px;
            overflow-y: auto;
        }

        .stock-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            border-radius: 10px;
            transition: background 0.15s;
            gap: 10px;
        }
        .stock-row:hover { background: oklch(var(--b2)); }

        .stock-name {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 0.83rem;
            color: oklch(var(--bc) / 0.85);
            font-weight: 500;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .stock-ico { color: oklch(var(--bc) / 0.25); font-size: 13px; flex-shrink:0; }

        .stock-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }
        .stock-red    { background: #fee2e2; color: #b91c1c; }
        .stock-yellow { background: #fef9c3; color: #92400e; }
        .stock-green  { background: #d1fae5; color: #065f46; }

        .stock-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 32px 16px;
            color: oklch(var(--bc) / 0.4);
            font-size: 0.83rem;
            text-align: center;
        }
        .stock-pager { padding: 8px 16px 14px; }
    </style>

    {{-- ── Script ── --}}
    <script>
        document.getElementById('dash-date-str').textContent =
            new Date().toLocaleDateString('en-US', { weekday:'short', year:'numeric', month:'long', day:'numeric' });

        const ctx = document.getElementById('myChart');
        const sales     = @json($monthlyTotalSales);
        const purchases = @json($monthlyTotalPurchases);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
                datasets: [
                    {
                        label: 'Sales',
                        data: sales,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#6366f1',
                        pointHoverRadius: 6,
                        fill: true
                    },
                    {
                        label: 'Purchases',
                        data: purchases,
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244,63,94,0.07)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#f43f5e',
                        pointHoverRadius: 6,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1e2e',
                        titleColor: '#a0a0b0',
                        bodyColor: '#ffffff',
                        padding: 12,
                        cornerRadius: 10,
                        callbacks: {
                            label: ctx => ' ₱' + ctx.parsed.y.toLocaleString()
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'DM Sans', sans-serif" }, color: '#9ca3af' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { size: 11, family: "'DM Sans', sans-serif" },
                            color: '#9ca3af',
                            callback: v => '₱' + v.toLocaleString()
                        }
                    }
                }
            }
        });
    </script>
@endsection
