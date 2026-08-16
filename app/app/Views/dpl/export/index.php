<div class="card">
    <div class="card-head"><h2>Export laporan bimbingan</h2></div>
    <p>Data mahasiswa bimbingan Anda. Excel (.xls) atau CSV.</p>
    <div class="export-grid">
        <div class="export-item">
            <strong>Logbook bimbingan</strong>
            <span>Kegiatan mahasiswa + status</span>
            <div class="export-links">
                <a href="<?= site_url('dpl/export/logbook') ?>">Excel</a>
                <a href="<?= site_url('dpl/export/logbook?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Laporan bimbingan</strong>
            <span>Status review laporan</span>
            <div class="export-links">
                <a href="<?= site_url('dpl/export/laporan') ?>">Excel</a>
                <a href="<?= site_url('dpl/export/laporan?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Nilai bimbingan</strong>
            <span>Nilai akhir &amp; grade mahasiswa</span>
            <div class="export-links">
                <a href="<?= site_url('dpl/export/nilai') ?>">Excel</a>
                <a href="<?= site_url('dpl/export/nilai?format=csv') ?>">CSV</a>
            </div>
        </div>
    </div>
</div>
