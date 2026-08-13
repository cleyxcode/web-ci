<div class="card" style="max-width:820px">
    <div class="card-head"><h2>Export laporan</h2></div>
    <p style="margin:0 0 16px;color:var(--abu-karang);font-size:0.9rem">Unduh Excel (.xls) atau CSV. File Excel terbuka langsung di Microsoft Excel / LibreOffice.</p>
    <div class="export-grid">
        <div class="export-item">
            <strong>Data mahasiswa</strong>
            <span>NPM, nama, kelompok, lokasi</span>
            <div class="export-links">
                <a href="<?= site_url('admin/export/mahasiswa') ?>">Excel</a>
                <a href="<?= site_url('admin/export/mahasiswa?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Logbook</strong>
            <span>Semua kegiatan + status validasi</span>
            <div class="export-links">
                <a href="<?= site_url('admin/export/logbook') ?>">Excel</a>
                <a href="<?= site_url('admin/export/logbook?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Laporan</strong>
            <span>Upload laporan + status review</span>
            <div class="export-links">
                <a href="<?= site_url('admin/export/laporan') ?>">Excel</a>
                <a href="<?= site_url('admin/export/laporan?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Nilai &amp; KNN</strong>
            <span>Nilai akhir, grade, prediksi KNN</span>
            <div class="export-links">
                <a href="<?= site_url('admin/export/nilai') ?>">Excel</a>
                <a href="<?= site_url('admin/export/nilai?format=csv') ?>">CSV</a>
            </div>
        </div>
        <div class="export-item">
            <strong>Kelompok + GPS</strong>
            <span>Ketua, koordinat, desa penempatan</span>
            <div class="export-links">
                <a href="<?= site_url('admin/export/kelompok') ?>">Excel</a>
                <a href="<?= site_url('admin/export/kelompok?format=csv') ?>">CSV</a>
            </div>
        </div>
    </div>
</div>
