<section class="grid stats">
    <div class="card stat"><span>Stok masuk</span><strong>{{ number_format($summary['stock_in'], 2, ',', '.') }}</strong></div>
    <div class="card stat"><span>Stok keluar</span><strong>{{ number_format($summary['stock_out'], 2, ',', '.') }}</strong></div>
    <div class="card stat"><span>Koreksi</span><strong>{{ $summary['adjustments'] }}</strong></div>
    <div class="card stat"><span>Nilai persediaan</span><strong>Rp {{ number_format($summary['inventory_value'], 0, ',', '.') }}</strong></div>
</section>

<section class="card" style="margin-top:16px;">
    <h2>Ringkasan stok saat ini</h2>
    <table>
        <thead><tr><th>Buah</th><th>SKU</th><th>Stok</th><th>Minimum</th><th>Harga beli</th><th>Nilai</th><th>Status</th></tr></thead>
        <tbody>
            @forelse ($fruits as $fruit)
                <tr>
                    <td>{{ $fruit->name }}</td>
                    <td>{{ $fruit->sku ?? '-' }}</td>
                    <td>{{ number_format((float) $fruit->current_stock, 2, ',', '.') }} {{ $fruit->unit }}</td>
                    <td>{{ number_format((float) $fruit->minimum_stock, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format((float) $fruit->purchase_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format((float) $fruit->current_stock * (float) $fruit->purchase_price, 0, ',', '.') }}</td>
                    <td>{{ $fruit->status }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada data buah.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="card" style="margin-top:16px;">
    <h2>Riwayat pergerakan periode ini</h2>
    <table>
        <thead><tr><th>Tanggal</th><th>Buah</th><th>Jenis</th><th>Jumlah</th><th>Harga</th><th>Referensi</th><th>PIC</th></tr></thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->movement_date->format('d M Y') }}</td>
                    <td>{{ $movement->fruit?->name ?? '-' }}</td>
                    <td>{{ ['in' => 'Masuk', 'out' => 'Keluar', 'adjustment' => 'Koreksi'][$movement->type] }}</td>
                    <td>{{ number_format((float) $movement->quantity, 2, ',', '.') }}</td>
                    <td>Rp {{ number_format((float) $movement->unit_price, 0, ',', '.') }}</td>
                    <td>{{ $movement->reference ?? '-' }}</td>
                    <td>{{ $movement->handled_by ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">Tidak ada pergerakan pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</section>
