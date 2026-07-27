<x-layouts.app title="Pesanan Customer">
    <section class="header">
        <div>
            <h1>Pesanan customer</h1>
            <p>Pantau order dari toko online, detail item, alamat pengantaran, dan status penanganan.</p>
        </div>
    </section>

    <section class="stack">
        @forelse ($orders as $order)
            <article class="card">
                <div class="header" style="margin:0 0 14px;">
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:6px;">
                            <h2 style="margin:0;font-size:18px;">{{ $order->order_number }}</h2>
                            @php
                                $statusColors = [
                                    'pending' => 'background:#fef9c3;color:#854d0e;',
                                    'confirmed' => 'background:#dcfce7;color:#166534;',
                                    'packed' => 'background:#dbeafe;color:#1e40af;',
                                    'delivered' => 'background:#dcfce7;color:#166534;',
                                    'cancelled' => 'background:#fee2e2;color:#991b1b;',
                                ];
                                $paymentColors = [
                                    'paid' => 'background:#dcfce7;color:#166534;',
                                    'failed' => 'background:#fee2e2;color:#991b1b;',
                                    'waiting_payment' => 'background:#fef9c3;color:#854d0e;',
                                    'unpaid' => 'background:#f1f5f9;color:#64748b;',
                                ];
                                $statusLabels = ['pending'=>'Pending','confirmed'=>'Dikonfirmasi','packed'=>'Dikemas','delivered'=>'Terkirim','cancelled'=>'Dibatalkan'];
                                $paymentLabels = ['paid'=>'Lunas','failed'=>'Gagal','waiting_payment'=>'Menunggu','unpaid'=>'Belum bayar'];
                            @endphp
                            <span style="display:inline-flex;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;{{ $statusColors[$order->status] ?? '' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
                            <span style="display:inline-flex;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:700;{{ $paymentColors[$order->payment_status] ?? '' }}">{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }} via {{ $order->payment_provider ?: '-' }}</span>
                        </div>
                        <p style="margin:0;color:var(--muted);font-size:13px;">{{ $order->customer_name }} &middot; {{ $order->customer_phone }}@if($order->customer_email) &middot; {{ $order->customer_email }}@endif</p>
                    </div>
                    <form method="post" action="{{ route('orders.status', $order) }}" class="actions">
                        @csrf
                        @method('patch')
                        <select name="status" aria-label="Status pesanan" style="font-size:13px;padding:7px 10px;border-radius:6px;">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button class="button primary" type="submit" style="font-size:13px;padding:7px 14px;">Simpan</button>
                    </form>
                </div>

                <div class="grid two">
                    <div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td style="font-weight:600;">{{ $item->fruit_name }}</td>
                                        <td>{{ number_format((float) $item->quantity, 2, ',', '.') }} {{ $item->unit }}</td>
                                        <td class="money">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                        <td class="money" style="font-weight:600;">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="stack">
                        <div class="item">
                            <strong style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px;">Alamat</strong>
                            <p style="font-size:14px;">{{ $order->delivery_address }}</p>
                        </div>
                        @if ($order->notes)
                            <div class="item">
                                <strong style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px;">Catatan</strong>
                                <p style="font-size:14px;">{{ $order->notes }}</p>
                            </div>
                        @endif
                        <div class="item">
                            <strong style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:6px;">Total</strong>
                            <p style="font-size:22px;font-weight:800;color:var(--ink);margin:0;">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</p>
                            <p style="font-size:12px;margin:4px 0 0;">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
                            @if ($order->paid_at)
                                <p style="font-size:12px;margin:2px 0 0;">Dibayar {{ $order->paid_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="card">
                <div style="text-align:center;padding:32px;color:var(--muted);">
                    <p style="font-size:14px;">Belum ada pesanan customer.</p>
                </div>
            </div>
        @endforelse
    </section>

    <div style="margin-top:16px;">
        {{ $orders->links() }}
    </div>
</x-layouts.app>
