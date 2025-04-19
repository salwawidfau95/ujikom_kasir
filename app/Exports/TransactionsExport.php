<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class TransactionsExport implements FromCollection, WithHeadings
{

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Transaction::with(['detail.product', 'member'])
            ->orderBy('created_at', 'desc');

        if ($this->request->year) {
            $query->whereYear('created_at', $this->request->year);
        }

        if ($this->request->month) {
            $query->whereMonth('created_at', $this->request->month);
        }

        if ($this->request->date) {
            $query->whereDate('created_at', $this->request->date);
        }

        $transactions = $query->get();
        $exportData = [];

        foreach ($transactions as $trx) {
            $customerName = $trx->member->name ?? 'Non Member';
            $phoneNumber = $trx->member->no_phone ?? '-';
            $point = $trx->member->point ?? '0';

            $products = $trx->detail->map(function ($item) {
                return $item->product->name . " ( " . $item->quantity . " : Rp. " . number_format($item->subtotal, 0, ',', '.') . " )";
            })->implode(', ');

            $discountPoint = 0;
            if ($trx->member && $trx->member->point > 0 && $trx->total_price > $trx->total_payment) {
                $discountPoint = $trx->total_payment - ($trx->total_price + $trx->change);
            }

            $exportData[] = [
                $customerName,
                $phoneNumber,
                $point,
                $products,
                'Rp. ' . number_format($trx->total_price, 0, ',', '.'),
                'Rp. ' . number_format($trx->total_payment, 0, ',', '.'),
                'Rp. ' . number_format($discountPoint, 0, ',', '.'),
                'Rp. ' . number_format($trx->change, 0, ',', '.'),
                $trx->created_at->format('d-m-Y'),
            ];
        }

        return new Collection($exportData);
    }


    public function headings(): array
    {
        return [
            'Name Member',
            'No HP Member',
            'Point Member',
            'Product',
            'Total Price',
            'Total Payment',
            'Total Discount Point',
            'Change',
            'Date Purchase',
        ];
    }
}
