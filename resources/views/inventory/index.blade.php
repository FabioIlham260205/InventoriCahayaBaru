<x-layouts.app title="Pencatatan Stok Buah">
    <section class="header">
        <div>
            <h1>Pencatatan stok buah</h1>
            <p>Catat master buah, stok masuk, stok keluar, koreksi stok, dan pantau peringatan operasional.</p>
        </div>
    </section>

    <section class="grid stats">
        <div class="card stat">
            <span>Jenis buah</span>
            <strong>{{ $stats['total_items'] }}</strong>
        </div>
        <div class="card stat">
            <span>Total stok</span>
            <strong>{{ number_format($stats['total_stock'], 2, ',', '.') }}</strong>
        </div>
        <div class="card stat">
            <span>Stok rendah</span>
            <strong>{{ $stats['low_stock'] }}</strong>
        </div>
        <div class="card stat">
            <span>Dekat kadaluarsa</span>
            <strong>{{ $stats['expiring'] }}</strong>
        </div>
    </section>

    <section class="grid two" style="margin-top:20px;">
        <div class="card">
            <h2>Tambah buah baru</h2>
            <form method="post" action="{{ route('fruits.store') }}" class="form-grid">
                @csrf
                <div><label>Nama buah</label><input name="name" required></div>
                <div><label>Kode SKU</label><input name="sku"></div>
                <div><label>Kategori</label><input name="category"></div>
                <div><label>Satuan</label><input name="unit" value="kg" required></div>
                <div><label>Stok awal</label><input type="number" step="0.01" min="0" name="current_stock" required></div>
                <div><label>Stok minimum</label><input type="number" step="0.01" min="0" name="minimum_stock" required></div>
                <div><label>Harga beli</label><input type="number" step="0.01" min="0" name="purchase_price"></div>
                <div><label>Harga jual</label><input type="number" step="0.01" min="0" name="selling_price"></div>
                <div><label>Supplier</label><input name="supplier"></div>
                <div><label>Lokasi simpan</label><input name="storage_location"></div>
                <div><label>Tanggal kadaluarsa</label><input type="date" name="expiry_date"></div>
                <div class="full"><label>Catatan</label><textarea name="notes"></textarea></div>
                <div class="full"><button class="button primary" type="submit">Simpan buah</button></div>
            </form>
        </div>

        <div class="stack">
            <div class="card">
                <h2>Catat pergerakan stok</h2>
                <form method="post" action="{{ route('stock-movements.store') }}" class="form-grid">
                    @csrf
                    <div>
                        <label>Buah</label>
                        <select name="fruit_id" required>
                            @foreach ($fruits as $fruit)
                                <option value="{{ $fruit->id }}">{{ $fruit->name }} ({{ $fruit->current_stock }} {{ $fruit->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Jenis</label>
                        <select name="type" required>
                            <option value="in">Stok masuk</option>
                            <option value="out">Stok keluar</option>
                            <option value="adjustment">Koreksi stok akhir</option>
                        </select>
                    </div>
                    <div><label>Jumlah</label><input type="number" step="0.01" min="0.01" name="quantity" required></div>
                    <div><label>Harga satuan</label><input type="number" step="0.01" min="0" name="unit_price"></div>
                    <div><label>Referensi</label><input name="reference" placeholder="PO/Nota/Retur"></div>
                    <div><label>Penanggung jawab</label><input name="handled_by"></div>
                    <div><label>Tanggal</label><input type="date" name="movement_date" value="{{ now()->toDateString() }}" required></div>
                    <div class="full"><label>Catatan</label><textarea name="notes" style="min-height:56px;"></textarea></div>
                    <div class="full"><button class="button primary" type="submit">Catat stok</button></div>
                </form>
            </div>

            <div class="card">
                <h2>Media lokal</h2>
                <p style="margin-bottom:12px;font-size:13px;">
                    Pilih foto atau video yang disimpan hanya di browser.
                </p>
                <div id="local-media-card" data-local-only="true" style="padding:0;">
                    <div class="form-grid" style="gap:10px;">
                        <div class="full"><label>Pilih file</label><input id="local-media-files" type="file" accept="image/*,video/*" multiple></div>
                        <div class="full" style="display:flex;gap:8px;align-items:center;">
                            <button class="button primary" id="save-local-media" type="button">Simpan</button>
                            <button class="button" id="clear-local-media" type="button">Hapus semua</button>
                        </div>
                    </div>
                    <div id="local-media-list" style="margin-top:14px;">
                        <p style="color:var(--muted);font-size:13px;">Belum ada media tersimpan.</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Notifikasi aktif</h2>
                <div class="stack">
                    @forelse ($alerts as $alert)
                        <div class="item">
                            <strong style="font-size:14px;">{{ $alert->title }}</strong>
                            <p style="font-size:13px;margin-top:4px;">{{ $alert->message }}</p>
                            <form method="post" action="{{ route('alerts.read', $alert) }}" style="margin-top:10px;">
                                @csrf
                                @method('patch')
                                <button class="button" type="submit">Tandai selesai</button>
                            </form>
                        </div>
                    @empty
                        <p style="font-size:13px;color:var(--muted);padding:16px 0;text-align:center;">Tidak ada notifikasi aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <style>
        .media-grid{display:grid;gap:10px;margin-top:10px;grid-template-columns:repeat(auto-fit,minmax(280px,1fr))}
        .media-card{border:1px solid rgba(232,236,233,.6);border-radius:var(--radius-sm);padding:10px;background:rgba(255,255,255,.6)}
        .media-filename{padding:8px;border:1px dashed var(--line);border-radius:6px;background:rgba(255,255,255,.8);display:flex;align-items:center;gap:10px;font-weight:600;color:var(--muted);font-size:13px}
        .media-card img,.media-card video{width:100%;max-width:100%;max-height:200px;height:auto;border-radius:8px;display:block;margin-bottom:8px;object-fit:contain}
        .media-meta{display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;font-size:12px;color:var(--muted);margin-bottom:8px}
        .media-actions{display:flex;gap:8px;flex-wrap:wrap}
        .media-actions button,.media-actions a.button{border:1px solid var(--line);border-radius:6px;padding:6px 10px;background:rgba(255,255,255,.8);cursor:pointer;font-weight:600;font-size:12px}
    </style>

    <script>
        (() => {
            const dbName = 'inventoryLocalMedia';
            const storeName = 'localMedia';
            let db;
            const listElement = document.getElementById('local-media-list');
            const fileInput = document.getElementById('local-media-files');
            const saveButton = document.getElementById('save-local-media');
            const clearButton = document.getElementById('clear-local-media');
            const MAX_BYTES = 50 * 1024 * 1024;

            function createObjectStore(event) {
                const database = event.target.result;
                if (!database.objectStoreNames.contains(storeName)) {
                    database.createObjectStore(storeName, { keyPath: 'id' });
                }
            }

            function openDatabase() {
                return new Promise((resolve, reject) => {
                    if (!window.indexedDB) { reject(new Error('IndexedDB tidak tersedia.')); return; }
                    const request = window.indexedDB.open(dbName, 1);
                    request.onupgradeneeded = createObjectStore;
                    request.onsuccess = () => resolve(request.result);
                    request.onerror = () => reject(request.error);
                });
            }

            function transaction(mode) { return db.transaction(storeName, mode).objectStore(storeName); }
            function getAllMedia() { return new Promise((resolve, reject) => { const r = transaction('readonly').getAll(); r.onsuccess = () => resolve(r.result); r.onerror = () => reject(r.error); }); }
            function saveMedia(item) { return new Promise((resolve, reject) => { const r = transaction('readwrite').put(item); r.onsuccess = () => resolve(r.result); r.onerror = () => reject(r.error); }); }
            function deleteMedia(id) { return new Promise((resolve, reject) => { const r = transaction('readwrite').delete(id); r.onsuccess = () => resolve(); r.onerror = () => reject(r.error); }); }

            function formatSize(bytes) {
                if (bytes < 1024) return `${bytes} B`;
                if (bytes < 1048576) return `${(bytes / 1024).toFixed(1)} KB`;
                return `${(bytes / 1048576).toFixed(1)} MB`;
            }

            function createPreview(item) {
                const previewUrl = URL.createObjectURL(item.data);
                const card = document.createElement('div');
                card.className = 'media-card';
                const meta = document.createElement('div');
                meta.className = 'media-meta';
                meta.innerHTML = `<strong>${item.name}</strong><span>${item.type.toUpperCase()} • ${formatSize(item.size)}</span>`;
                card.appendChild(meta);
                const filenameDiv = document.createElement('div');
                filenameDiv.className = 'media-filename';
                filenameDiv.innerHTML = `<span>${item.type === 'video' ? '🎞️' : '🖼️'}</span><span>${item.name}</span>`;
                card.appendChild(filenameDiv);
                const actions = document.createElement('div');
                actions.className = 'media-actions';
                const downloadLink = document.createElement('a');
                downloadLink.href = previewUrl;
                downloadLink.download = item.name;
                downloadLink.className = 'button';
                downloadLink.textContent = 'Unduh';
                actions.appendChild(downloadLink);
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = 'Hapus';
                removeButton.onclick = async () => { await deleteMedia(item.id); renderMedia(); };
                actions.appendChild(removeButton);
                card.appendChild(actions);
                return card;
            }

            async function renderMedia() {
                if (!listElement) return;
                const items = await getAllMedia();
                listElement.innerHTML = '';
                if (!items.length) { listElement.innerHTML = '<p style="color:var(--muted);font-size:13px;">Belum ada media tersimpan.</p>'; return; }
                const grid = document.createElement('div');
                grid.className = 'media-grid';
                items.forEach(item => grid.appendChild(createPreview(item)));
                listElement.appendChild(grid);
            }

            async function init() {
                try { db = await openDatabase(); await renderMedia(); }
                catch (error) { if (listElement) listElement.innerHTML = `<p style="color:var(--red);">Gagal inisialisasi: ${error.message}</p>`; }
            }

            if (saveButton) {
                saveButton.addEventListener('click', async () => {
                    const files = fileInput.files;
                    if (!files.length) { alert('Pilih file terlebih dahulu.'); return; }
                    for (const file of files) {
                        if (file.size > MAX_BYTES && !confirm(`${file.name} ukurannya ${formatSize(file.size)}. Tetap simpan?`)) continue;
                        const item = { id: crypto.randomUUID ? crypto.randomUUID() : `${Date.now()}-${Math.random().toString(36).slice(2)}`, name: file.name, type: file.type.startsWith('video/') ? 'video' : 'image', mimeType: file.type, size: file.size, createdAt: new Date().toISOString(), data: file };
                        try { await saveMedia(item); } catch (err) { alert(`Gagal menyimpan ${file.name}: ${err?.message || err}`); }
                    }
                    fileInput.value = '';
                    await renderMedia();
                    alert('Media berhasil disimpan di browser.');
                });
            }

            if (clearButton) {
                clearButton.addEventListener('click', async () => {
                    if (!confirm('Hapus semua media?')) return;
                    try {
                        await new Promise((resolve, reject) => { const req = transaction('readwrite').clear(); req.onsuccess = () => resolve(); req.onerror = () => reject(req.error); });
                        await renderMedia();
                    } catch (err) { alert('Gagal menghapus: ' + (err?.message || err)); }
                });
            }

            init();
        })();
    </script>
</x-layouts.app>
