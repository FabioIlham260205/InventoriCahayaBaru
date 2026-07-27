<?php

namespace App\Http\Controllers;

use App\Models\Fruit;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return $this->reportView($request, 'reports.index');
    }

    public function print(Request $request): View
    {
        return $this->reportView($request, 'reports.print');
    }

    private function reportView(Request $request, string $view): View
    {
        $from = $request->filled('from') ? $request->date('from') : now()->startOfMonth();
        $to = $request->filled('to') ? $request->date('to') : now();

        $movements = StockMovement::query()
            ->with('fruit')
            ->whereBetween('movement_date', [$from->toDateString(), $to->toDateString()])
            ->latest('movement_date')
            ->get();

        $fruits = Fruit::query()->orderBy('name')->get();

        $summary = [
            'stock_in' => $movements->where('type', 'in')->sum(fn (StockMovement $movement) => (float) $movement->quantity),
            'stock_out' => $movements->where('type', 'out')->sum(fn (StockMovement $movement) => (float) $movement->quantity),
            'adjustments' => $movements->where('type', 'adjustment')->count(),
            'inventory_value' => $fruits->sum(fn (Fruit $fruit) => (float) $fruit->current_stock * (float) $fruit->purchase_price),
        ];

        return view($view, compact('from', 'to', 'movements', 'fruits', 'summary'));
    }
}
