<?php

namespace App\Services;

use App\Models\Sale_item;
use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SaleService extends BaseService
{
    protected $saleRepo;

    public function __construct(SaleRepository $saleRepo)
    {
        parent::__construct($saleRepo);
        $this->saleRepo = $saleRepo;
    }

    public function placeOrder(array $data): array
    {
        $result = $this->executeTransaction(function () use ($data) {
            $sale = $this->repo->create([
                'invoice_no'     => 'INV-' . now()->format('YmdHis'),
                'customer_id'    => $data['customer_id'],
                // 'user_id'        => Auth::id(),
                'user_id'        => '1',
                'total_amount'   => collect($data['items'])->sum(fn($i) => $i['qty'] * $i['price']),
                'payment_method' => $data['payment_method'],
                'payment_status' => 'paid',
            ]);

            foreach ($data['items'] as $item) {
                Sale_item::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $item['qty'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['qty'] * $item['price'],
                ]);
            }

            return $sale;
        }, 'Failed to place order');

        return $result
            ? ['success' => true,  'message' => 'Order placed successfully!']
            : ['success' => false, 'message' => 'Something went wrong.'];
    }

    public function getAllSales(): array
    {
        $headers = $this->repo->findAllSales();

        return collect($headers)->map(function ($header) {
            $items = $this->repo->findSaleItems($header->order_no);

            return [
                'customer_name'  => $header->name,
                'order_no'       => $header->order_no,
                'payment_status' => $header->payment_status,
                'created_at'     => $header->created_at,
                'items'          => $items,
                'total_amount'   => $header->total_amount,
                'sales_status'   => $header->sales_status,
                'payment_method'   => $header->payment_method
            ];
        })->all(); // returns collection items but keeps them as arrays
    }

    public function countByOrderStatus()
    {
        return $this->saleRepo->countByOrderStatus();
    }
}
