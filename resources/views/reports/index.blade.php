<x-layouts.app title="Laporan Inventory Buah">
    <section class="header">
        <div>
            <h1>Laporan stok buah</h1>
            <p>Filter periode, periksa nilai persediaan, lalu cetak laporan operasional.</p>
        </div>
        <div class="actions no-print">
            <a class="button primary" href="{{ route('reports.print', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" target="_blank">Cetak laporan</a>
        </div>
    </section>

    <section class="card no-print" style="margin-bottom:16px;">
        <form method="get" action="{{ route('reports.index') }}" class="form-grid">
            <div><label>Dari tanggal</label><input type="date" name="from" value="{{ $from->toDateString() }}"></div>
            <div><label>Sampai tanggal</label><input type="date" name="to" value="{{ $to->toDateString() }}"></div>
            <div class="full"><button class="button primary" type="submit">Terapkan filter</button></div>
        </form>
    </section>

    @include('reports.partials.content')
</x-layouts.app>
