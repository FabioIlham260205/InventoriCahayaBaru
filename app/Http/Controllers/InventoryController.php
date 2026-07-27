<?php

namespace App\Http\Controllers;

use App\Models\Fruit;
use App\Models\InventoryAlert;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $fruits = Fruit::query()
            ->withCount('movements')
            ->orderBy('name')
            ->get();

        $movements = StockMovement::query()
            ->with('fruit')
            ->latest('movement_date')
            ->latest()
            ->limit(12)
            ->get();

        $alerts = InventoryAlert::query()
            ->with('fruit')
            ->where('is_read', false)
            ->latest()
            ->limit(8)
            ->get();

        $stats = [
            'total_items' => $fruits->count(),
            'total_stock' => $fruits->sum(fn (Fruit $fruit) => (float) $fruit->current_stock),
            'low_stock' => $fruits->filter(fn (Fruit $fruit) => (float) $fruit->current_stock <= (float) $fruit->minimum_stock)->count(),
            'expiring' => $fruits->filter(fn (Fruit $fruit) => $fruit->expiry_date && $fruit->expiry_date->between(now()->startOfDay(), now()->addDays(3)->endOfDay()))->count(),
        ];

        return view('inventory.index', compact('fruits', 'movements', 'alerts', 'stats'));
    }

    public function detail(): View
    {
        $fruits = Fruit::query()
            ->withCount('movements')
            ->orderBy('name')
            ->get();

        $movements = StockMovement::query()
            ->with('fruit')
            ->latest('movement_date')
            ->latest()
            ->limit(12)
            ->get();

        $stats = [
            'total_items' => $fruits->count(),
            'total_stock' => $fruits->sum(fn (Fruit $fruit) => (float) $fruit->current_stock),
            'low_stock' => $fruits->filter(fn (Fruit $fruit) => (float) $fruit->current_stock <= (float) $fruit->minimum_stock)->count(),
            'expiring' => $fruits->filter(fn (Fruit $fruit) => $fruit->expiry_date && $fruit->expiry_date->between(now()->startOfDay(), now()->addDays(3)->endOfDay()))->count(),
        ];

        return view('inventory.detail', compact('fruits', 'movements', 'stats'));
    }

    public function storeFruit(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:fruits,sku'],
            'category' => ['nullable', 'string', 'max:80'],
            'unit' => ['required', 'string', 'max:20'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'supplier' => ['nullable', 'string', 'max:120'],
            'storage_location' => ['nullable', 'string', 'max:120'],
            'expiry_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $fruit = Fruit::create($data);
        $this->syncAlerts($fruit);

        return back()->with('status', 'Data buah berhasil ditambahkan.');
    }

    public function storeMovement(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fruit_id' => ['required', 'exists:fruits,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'reference' => ['nullable', 'string', 'max:120'],
            'handled_by' => ['nullable', 'string', 'max:120'],
            'movement_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data): void {
            $fruit = Fruit::lockForUpdate()->findOrFail($data['fruit_id']);
            $quantity = (float) $data['quantity'];
            $currentStock = (float) $fruit->current_stock;

            $fruit->current_stock = match ($data['type']) {
                'in' => $currentStock + $quantity,
                'out' => max(0, $currentStock - $quantity),
                'adjustment' => $quantity,
            };
            $fruit->save();

            StockMovement::create($data);
            $this->syncAlerts($fruit->fresh());
        });

        return back()->with('status', 'Pergerakan stok berhasil dicatat.');
    }

    public function markAlertRead(InventoryAlert $alert): RedirectResponse
    {
        $alert->update(['is_read' => true]);

        return back()->with('status', 'Notifikasi ditandai selesai.');
    }

    private function syncAlerts(Fruit $fruit): void
    {
        if ((float) $fruit->current_stock <= (float) $fruit->minimum_stock) {
            InventoryAlert::firstOrCreate(
                ['fruit_id' => $fruit->id, 'type' => 'low_stock', 'is_read' => false],
                [
                    'title' => 'Stok rendah',
                    'message' => "{$fruit->name} tersisa {$fruit->current_stock} {$fruit->unit}.",
                ]
            );
        }

        if ($fruit->expiry_date && $fruit->expiry_date->between(now()->startOfDay(), now()->addDays(3)->endOfDay())) {
            InventoryAlert::firstOrCreate(
                ['fruit_id' => $fruit->id, 'type' => 'expiring', 'is_read' => false],
                [
                    'title' => 'Segera kadaluarsa',
                    'message' => "{$fruit->name} perlu diprioritaskan sebelum {$fruit->expiry_date->format('d M Y')}.",
                ]
            );
        }
    }
}
