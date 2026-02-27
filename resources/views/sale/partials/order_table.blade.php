

<div id="">
    <x-table
        :headers="['#', 'Date & Time', 'Customer Name', 'Payment Status', 'Total Payment', 'Order Status', 'Orders']"
    >
        @forelse ($sales as $row)
            <tr class="order-card" data-search="{{ strtolower($row['customer_name'] . '  ' . $row['sales_status'] . ' ' . $row['payment_status'] . ' ' . $row['total_amount']) . ' ' . $row['order_no']}}">
                <th>00{{ $row['order_no'] }}</th>
                <td>{{ \Carbon\Carbon::parse($row['created_at'])->format('d/m/Y - h:ia') }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ ucwords($row['payment_status']) }}</td>
                <td>₱{{ number_format($row['total_amount'],2) }}</td>
                <td>
                    <x-status :status="$row['sales_status']" />
                </td>
                <td>Details</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-gray-500">No orders found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($sales instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $sales->links() }}
        </div>
    @endif
</div>

