<div>
    <x-table
        id="salesOrderTable"
        :headers="['#', 'Date & Time', 'Customer Name', 'Payment Status', 'Total Payment', 'Order Status', 'Action']"
    >
        @foreach ($sales as $row)
            <tr class="order-card" data-search="{{ strtolower($row['customer_name'] . '  ' . $row['sales_status'] . ' ' . $row['payment_status'] . ' ' . $row['total_amount']) . ' ' . $row['order_no']}}">
                <th>00{{ $row['order_no'] }}</th>
                <td>{{ \Carbon\Carbon::parse($row['created_at'])->format('d/m/Y - h:ia') }}</td>
                <td>{{ $row['customer_name'] }}</td>
                <td>{{ ucwords($row['payment_status']) }}</td>
                <td>₱{{ number_format($row['total_amount'],2) }}</td>
                <td>
                    <x-status :status="$row['sales_status']" />
                </td>
                <td>
                    @if ($row['sales_status'] == 'pending')
                        <div class="flex flex-wrap gap-3 w-full">
                            <x-button
                                color="error"
                                outline
                                onclick="orderPrep('{{ $row['order_no'] }}', 'cancelled')"
                            >
                                <i class="fa-solid fa-ban"></i>
                            </x-button>

                            <x-button
                                color="success"
                                outline
                                onclick="orderPrep('{{ $row['order_no'] }}', 'completed')"
                            >
                                <i class="fa-solid fa-check-double"></i>
                            </x-button>
                        </div>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-table>
</div>
<script>
    $(document).ready(function(){
        $('#salesOrderTable').DataTable();
    });
</script>
