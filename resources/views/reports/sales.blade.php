@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-base-200 p-6">

    {{-- ── PAGE HEADER ── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-base-content">
                Sales Report
            </h1>
            <p class="text-base-content/50 text-sm mt-1">
                {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}
                –
                {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
            </p>
        </div>

        {{-- Date Filter --}}
        <form method="GET" action="{{ route('report.index') }}"
                class="flex flex-wrap gap-2 items-end">
            <div class="form-control">
                <label class="label py-0"><span class="label-text text-xs">From</span></label>
                <x-text-input type="date" name="start_date" value="{{ $startDate }}"
                        class="bg-white" />
            </div>
            <div class="form-control">
                <label class="label py-0"><span class="label-text text-xs">To</span></label>
                <x-text-input type="date" name="end_date" value="{{ $endDate }}"
                        class="bg-white" />
            </div>
            <x-button icon="fa-filter" type="submit" color="primary">Filter</x-button>

            <a href="{{ route('report.sales.export', request()->query()) }}"
                class="btn btn-success btn-sm gap-1">
                <i class="fa-solid fa-download"></i>
                Export CSV
            </a>
        </form>
    </div>

    {{-- ── KPI STAT CARDS ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="stat bg-base-100 rounded-2xl shadow">
            <div class="stat-figure text-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-title text-xs">Total Revenue</div>
            <div class="stat-value text-success text-2xl">
                ₱{{ number_format($kpi->total_revenue ?? 0, 2) }}
            </div>
            <div class="stat-desc">Completed sales only</div>
        </div>

        <div class="stat bg-base-100 rounded-2xl shadow">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-title text-xs">Total Orders</div>
            <div class="stat-value text-primary text-2xl">{{ $kpi->total_orders ?? 0 }}</div>
            <div class="stat-desc">All statuses</div>
        </div>

        <div class="stat bg-base-100 rounded-2xl shadow">
            <div class="stat-figure text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-title text-xs">Pending</div>
            <div class="stat-value text-warning text-2xl">{{ $kpi->pending_count ?? 0 }}</div>
            <div class="stat-desc">Awaiting completion</div>
        </div>

        <div class="stat bg-base-100 rounded-2xl shadow">
            <div class="stat-figure text-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-title text-xs">Cancelled</div>
            <div class="stat-value text-error text-2xl">{{ $kpi->cancelled_count ?? 0 }}</div>
            <div class="stat-desc">Voided transactions</div>
        </div>
    </div>

    {{-- ── TOP PRODUCTS + PAYMENT + CATEGORY ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">

        {{-- Top Products --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title text-base font-bold">🏆 Top Selling Products</h2>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-base-200">
                                <th>#</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $i => $item)
                            <tr class="hover">
                                <td>
                                    @if($i === 0) <span class="badge badge-warning badge-sm">1st</span>
                                    @elseif($i === 1) <span class="badge badge-ghost badge-sm">2nd</span>
                                    @elseif($i === 2) <span class="badge badge-ghost badge-sm">3rd</span>
                                    @else <span class="text-base-content/40">{{ $i + 1 }}</span>
                                    @endif
                                </td>
                                <td class="font-medium">{{ $item->product_name }}</td>
                                <td>
                                    <span class="badge badge-outline badge-sm">
                                        {{ $item->category_name }}
                                    </span>
                                </td>
                                <td class="text-right">{{ $item->total_qty }}</td>
                                <td class="text-right font-semibold text-success">
                                    ₱{{ number_format($item->total_revenue, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-base-content/40 py-6">
                                    No product data
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="flex flex-col gap-4">

            {{-- Payment Methods --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold">💳 Payment Methods</h2>
                    @php $totalTxns = array_sum(array_column($byPayment, 'txn_count')); @endphp
                    <div class="space-y-3">
                        @forelse($byPayment as $row)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="capitalize font-medium">{{ $row->payment_method }}</span>
                                <span class="text-base-content/60">
                                    {{ $row->txn_count }} txn · ₱{{ number_format($row->total, 2) }}
                                </span>
                            </div>
                            @php $pct = $totalTxns > 0 ? ($row->txn_count / $totalTxns) * 100 : 0; @endphp
                            <progress class="progress progress-primary w-full"
                                      value="{{ $pct }}" max="100"></progress>
                        </div>
                        @empty
                        <p class="text-base-content/40 text-sm text-center py-4">No data</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Revenue by Category --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base font-bold">📂 Revenue by Category</h2>
                    <div class="space-y-2">
                        @forelse($byCategory as $row)
                        <div class="flex justify-between items-center">
                            <span class="badge badge-outline">{{ $row->category_name }}</span>
                            <span class="font-semibold text-success">
                                ₱{{ number_format($row->revenue, 2) }}
                            </span>
                        </div>
                        @empty
                        <p class="text-base-content/40 text-sm text-center py-4">No data</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TRANSACTION TABLE ── --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex items-center justify-between mb-2">
                <h2 class="card-title text-base font-bold">📋 Transaction History</h2>
                <span class="badge badge-neutral">{{ $paginator->total() }} records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-sm table-zebra">
                    <thead>
                        <tr class="bg-base-200">
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Items</th>
                            <th>Payment</th>
                            <th class="text-right">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr class="hover">
                            <td class="font-mono text-xs text-primary">
                                {{ $sale->invoice_no }}
                            </td>
                            <td class="text-xs text-base-content/60 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($sale->created_at)->format('M d, Y h:i A') }}
                            </td>
                            <td>{{ $sale->customer_name }}</td>
                            <td>{{ $sale->cashier_name }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($sale->items as $item)
                                    <span class="badge badge-ghost badge-xs">
                                        {{ $item->quantity }}x {{ $item->product_name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-outline badge-sm capitalize">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="text-right font-semibold">
                                ₱{{ number_format($sale->total_amount, 2) }}
                            </td>
                            <td>
                                @if($sale->sales_status === 'completed')
                                    <span class="badge badge-success badge-sm">Completed</span>
                                @elseif($sale->sales_status === 'cancelled')
                                    <span class="badge badge-error badge-sm">Cancelled</span>
                                @else
                                    <span class="badge badge-warning badge-sm">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-base-content/40 py-10">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 opacity-20"
                                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>No transactions found for this period</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($sales) > 0)
                    <tfoot>
                        <tr class="font-bold bg-base-200">
                            <td colspan="6" class="text-right">Grand Total (Completed)</td>
                            <td class="text-right text-success text-base">
                                ₱{{ number_format($kpi->total_revenue ?? 0, 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $paginator->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

