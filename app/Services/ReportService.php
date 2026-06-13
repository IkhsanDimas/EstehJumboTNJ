<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Ringkasan penjualan harian (status completed).
     *
     * @return array{
     *  total_orders:int,total_revenue:float,
     *  by_type:array,by_payment:array,avg_order_value:float
     * }
     */
    public function getDailySales(CarbonInterface $date): array
    {
        $orders = Order::query()
            ->where('status', 'completed')
            ->whereDate('completed_at', $date->toDateString())
            ->get();

        return [
            'total_orders'    => $orders->count(),
            'total_revenue'   => (float) $orders->sum('grand_total'),
            'by_type'         => $orders->groupBy('type')
                ->map(fn ($g) => (float) $g->sum('grand_total'))->toArray(),
            'by_payment'      => $orders->groupBy('payment_method')
                ->map(fn ($g) => (float) $g->sum('grand_total'))->toArray(),
            'avg_order_value' => $orders->count() > 0
                ? (float) $orders->avg('grand_total')
                : 0.0,
        ];
    }

    public function getTopProducts(CarbonInterface $from, CarbonInterface $to, int $limit = 5): Collection
    {
        return OrderItem::query()
            ->select('product_id', 'product_name_snapshot')
            ->selectRaw('SUM(quantity) as total_qty')
            ->selectRaw('SUM(line_total) as total_revenue')
            ->whereHas('order', function ($q) use ($from, $to) {
                $q->where('status', 'completed')
                  ->whereBetween('completed_at', [$from, $to]);
            })
            ->groupBy('product_id', 'product_name_snapshot')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    /** Pendapatan harian dalam rentang [from, to] untuk chart. */
    public function getRevenueByPeriod(CarbonInterface $from, CarbonInterface $to): array
    {
        $orders = Order::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$from, $to])
            ->orderBy('completed_at')
            ->get();

        $grouped = $orders->groupBy(function ($order) {
            return $order->completed_at->format('Y-m-d');
        });

        $results = [];
        foreach ($grouped as $date => $group) {
            $results[] = [
                'date'    => (string) $date,
                'revenue' => (float) $group->sum('grand_total'),
                'orders'  => (int) $group->count(),
            ];
        }

        return $results;
    }
}
