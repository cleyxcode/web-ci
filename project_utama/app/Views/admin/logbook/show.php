<div class="mb-4">
    <a href="<?= site_url('admin/logbook') ?>" class="btn btn-secondary btn-sm">&larr; Kembali ke daftar logbook</a>
</div>

<div class="dashboard-grid">
    <div>
        <div class="card">
            <div class="card-head">
                <h2>Detail logbook</h2>
                <span class="<?= stempel_class($logbook['status']) ?>"><?= stempel_label($logbook['status']) ?></span>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <span class="info-label">Tanggal kegiatan</span>
                    <strong><?= format_tanggal($logbook['tanggal'] ?? null) ?></strong>
                </div>
                <div class="info-card">
                    <span class="info-label">Lokasi kegiatan</span>
                    <strong><?= esc($logbook['lokasi_kegiatan'] ?? '-') ?></strong>
                </div>
            </div>

            <div class="info-card mt-3">
                <span class="info-label">Uraian kegiatan</span>
                <p class="text-sm text-slate-700 dark:text-slate-300 mt-1 leading-relaxed whitespace-pre-wrap"><?= esc($logbook['kegiatan'] ?? '-') ?></p>
            </div>

            <?php if (! empty($logbook['catatan_dpl'])): ?>
                <div class="info-card mt-3" style="border-color:rgb(196 181 253);background:rgb(245 243 255)" class="dark:border-violet-800 dark:bg-violet-950/20">
                    <span class="info-label" style="color:rgb(109 40 217)">Catatan DPL</span>
                    <p class="text-sm mt-1 leading-relaxed" style="color:rgb(91 33 182)"><?= esc($logbook['catatan_dpl']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (! empty($logbook['dokumentasi'])): ?>
                <div class="mt-4">
                    <p class="info-label mb-2">Dokumentasi</p>
                    <?php
                        $ext = strtolower(pathinfo($logbook['dokumentasi'], PATHINFO_EXTENSION));
                        $imgExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    ?>
                    <?php if (in_array($ext, $imgExt, true)): ?>
                        <a href="<?= base_url('uploads/' . $logbook['dokumentasi']) ?>" target="_blank">
                            <img src="<?= base_url('uploads/' . $logbook['dokumentasi']) ?>" alt="Dokumentasi" class="rounded-xl border border-slate-200 max-h-72 object-cover dark:border-slate-700">
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('uploads/' . $logbook['dokumentasi']) ?>" target="_blank" class="btn btn-secondary btn-sm">Unduh file dokumentasi</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($logbook['validated_at'])): ?>
                <p class="text-xs text-slate-400 mt-4">Divalidasi pada: <?= format_tanggal($logbook['validated_at']) ?></p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-head"><h2>Tindakan</h2></div>
            <form method="post" action="<?= site_url('admin/logbook/' . (int) $logbook['id'] . '/delete') ?>" data-confirm="Hapus logbook ini permanen? File dokumentasi juga akan dihapus." data-confirm-danger="1">
                <?= csrf_field() ?>
                <p class="text-sm text-slate-500 mb-4">Penghapusan bersifat permanen dan tidak dapat dibatalkan. File dokumentasi juga akan ikut dihapus.</p>
                <button type="submit" class="btn btn-danger btn-sm">Hapus logbook ini</button>
            </form>
        </div>
    </div>

    <div>
        <div class="card">
            <div class="card-head"><h2>Informasi mahasiswa</h2></div>
            <div class="info-grid">
                <div class="info-card">
                    <span class="info-label">Nama</span>
                    <strong><?= esc($logbook['nama_mahasiswa'] ?? '-') ?></strong>
                </div>
                <div class="info-card">
                    <span class="info-label">NPM</span>
                    <strong class="font-mono"><?= esc($logbook['npm'] ?? '-') ?></strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-head"><h2>Informasi kelompok</h2></div>
            <div class="info-grid">
                <div class="info-card">
                    <span class="info-label">Kelompok</span>
                    <strong><?= esc($logbook['nama_kelompok'] ?? '-') ?></strong>
                </div>
                <div class="info-card">
                    <span class="info-label">Periode KKN</span>
                    <strong><?= esc($logbook['periode'] ?? '-') ?></strong>
                </div>
                <div class="info-card">
                    <span class="info-label">Lokasi KKN</span>
                    <strong><?= esc(format_alamat($logbook)) ?></strong>
                </div>
                <div class="info-card">
                    <span class="info-label">DPL</span>
                    <strong><?= esc($logbook['nama_dpl'] ?? '-') ?></strong>
                    <?php if (! empty($logbook['nidn'])): ?>
                        <small class="block text-xs text-slate-400 font-mono"><?= esc($logbook['nidn']) ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
