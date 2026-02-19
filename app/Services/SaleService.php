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
    }

    // SaleService.php
    public function placeOrder(array $data): array
    {
        Log::info('Inside closure, data:', $data);

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
}
