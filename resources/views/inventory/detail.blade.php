<x-layouts.app title="Dashboard Detail">
    <section class="header">
        <div>
            <h1>Dashboard Detail</h1>
            <p>Detail daftar stok seluruh buah yang tercatat di inventory.</p>
        </div>
        <div class="actions">
            <a class="button" href="{{ route('inventory.index') }}">Kembali ke Pencatatan</a>
        </div>
    </section>

    <section class="grid stats">
        <div class="card stat"><span>Jenis buah</span><strong>{{ $stats['total_items'] }}</strong></div>
        <div class="card stat"><span>Total stok</span><strong>{{ number_format($stats['total_stock'], 2, ',', '.') }}</strong></div>
        <div class="card stat"><span>Stok rendah</span><strong>{{ $stats['low_stock'] }}</strong></div>
        <div class="card stat"><span>Dekat kadaluarsa</span><strong>{{ $stats['expiring'] }}</strong></div>
    </section>

    <section class="card" style="margin-top:20px;">
        <h2>Daftar stok buah</h2>
        <table>
            <thead>
                <tr>
                    <th>Buah</th>
                    <th>SKU</th>
                    <th>Stok</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Lokasi</th>
                    <th>Kadaluarsa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fruits as $fruit)
                    <tr>
                        <td><strong>{{ $fruit->name }}</strong><br><small>{{ $fruit->category ?? '-' }}</small></td>
                        <td>{{ $fruit->sku ?? '-' }}</td>
                        <td>{{ number_format((float) $fruit->current_stock, 2, ',', '.') }} {{ $fruit->unit }}<br><small>Min {{ number_format((float) $fruit->minimum_stock, 2, ',', '.') }}</small></td>
                        <td class="money">Rp {{ number_format((float) $fruit->purchase_price, 0, ',', '.') }}</td>
                        <td class="money">Rp {{ number_format((float) $fruit->selling_price, 0, ',', '.') }}</td>
                        <td>{{ $fruit->storage_location ?? '-' }}</td>
                        <td>{{ $fruit->expiry_date?->format('d M Y') ?? '-' }}</td>
                        <td><span class="badge {{ $fruit->status === 'Aman' ? 'ok' : ($fruit->status === 'Stok rendah' ? 'danger' : 'warn') }}">{{ $fruit->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8">Belum ada data buah.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
</x-layouts.app>
