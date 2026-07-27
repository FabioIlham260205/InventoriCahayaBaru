<x-layouts.app title="Cetak Laporan Inventory">
    <section class="header">
        <div>
            <h1>Laporan Inventory Buah Cahaya Baru</h1>
            <p>Periode {{ $from->format('d M Y') }} sampai {{ $to->format('d M Y') }}</p>
        </div>
        <button class="button primary no-print" onclick="window.print()">Cetak</button>
    </section>

    @include('reports.partials.content')

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</x-layouts.app>
