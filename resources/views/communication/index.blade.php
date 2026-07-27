<x-layouts.app title="Notif dan Komunikasi">
    <section class="header">
        <div>
            <h1>Notif & komunikasi</h1>
            <p>Pantau alert stok dan simpan komunikasi singkat antar gudang, pembelian, penjualan, dan manajemen.</p>
        </div>
    </section>

    <section class="grid two">
        <div class="card">
            <h2>Kirim pesan tim</h2>
            <form method="post" action="{{ route('communication.store') }}" class="form-grid">
                @csrf
                <div><label>Nama pengirim</label><input name="sender_name" required></div>
                <div>
                    <label>Kanal</label>
                    <select name="channel" required>
                        <option value="gudang">Gudang</option>
                        <option value="pembelian">Pembelian</option>
                        <option value="penjualan">Penjualan</option>
                        <option value="manajemen">Manajemen</option>
                    </select>
                </div>
                <div class="full"><label>Subjek</label><input name="subject" required></div>
                <div class="full"><label>Isi pesan</label><textarea name="body" required></textarea></div>
                <div class="full"><button class="button primary" type="submit">Kirim pesan</button></div>
            </form>
        </div>

        <div class="card">
            <h2>Alert inventory</h2>
            <div class="stack">
                @forelse ($alerts as $alert)
                    <div class="item">
                        <span class="badge {{ $alert->is_read ? 'ok' : 'warn' }}">{{ $alert->is_read ? 'Selesai' : 'Aktif' }}</span>
                        <strong style="display:block; margin-top:8px;">{{ $alert->title }}</strong>
                        <p>{{ $alert->message }}</p>
                    </div>
                @empty
                    <p>Belum ada alert inventory.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card" style="margin-top:16px;">
        <h2>Log komunikasi</h2>
        <div class="stack">
            @forelse ($messages as $message)
                <article class="item">
                    <span class="badge">{{ ucfirst($message->channel) }}</span>
                    <strong style="display:block; margin-top:8px;">{{ $message->subject }}</strong>
                    <p>{{ $message->body }}</p>
                    <small>{{ $message->sender_name }} / {{ $message->created_at->format('d M Y H:i') }}</small>
                </article>
            @empty
                <p>Belum ada pesan tim.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
