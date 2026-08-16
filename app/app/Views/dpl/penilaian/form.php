<div class="card">
    <div class="card-head">
        <h2><?= esc($mahasiswa['nama'] ?? '') ?></h2>
        <span class="font-mono"><?= esc($mahasiswa['npm'] ?? '') ?></span>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <span class="info-label">Kelompok</span>
            <strong><?= esc($mahasiswa['nama_kelompok'] ?? 'Belum ditempatkan') ?></strong>
            <small><?= esc(format_alamat($mahasiswa)) ?></small>
        </div>
        <div class="info-card">
            <span class="info-label">Periode</span>
            <strong><?= esc($mahasiswa['periode'] ?? '-') ?></strong>
            <small><?= format_tanggal($mahasiswa['tanggal_mulai'] ?? null) ?> – <?= format_tanggal($mahasiswa['tanggal_selesai'] ?? null) ?></small>
        </div>
    </div>

    <div class="stat-row">
        <div class="stat"><div class="label">Logbook</div><div class="value"><?= (int) $jml_logbook ?></div></div>
        <div class="stat"><div class="label">Logbook valid</div><div class="value"><?= (int) $jml_logbook_valid ?></div></div>
        <div class="stat"><div class="label">Laporan</div><div class="value"><?= (int) $jml_laporan ?></div></div>
        <div class="stat"><div class="label">Laporan diterima</div><div class="value"><?= (int) $jml_laporan_terima ?></div></div>
    </div>

    <?php if (! empty($evaluasi)): ?>
        <div class="alert alert-info">
            <strong>Evaluasi kegiatan dari mahasiswa:</strong>
            rating <?= (int) $evaluasi['rating'] ?>/5
            · bimbingan <?= (int) ($evaluasi['aspek_bimbingan'] ?? 0) ?>
            · lokasi <?= (int) ($evaluasi['aspek_lokasi'] ?? 0) ?>
            · pelaksanaan <?= (int) ($evaluasi['aspek_pelaksanaan'] ?? 0) ?>
            <?php if (! empty($evaluasi['komentar'])): ?>
                <br><span><?= esc($evaluasi['komentar']) ?></span>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="field-hint">Mahasiswa belum mengirim evaluasi kegiatan.</p>
    <?php endif; ?>

    <p class="field-hint">
        Nilai akhir = keaktifan 30% + logbook 30% + laporan 40%. Grade dihitung otomatis dari nilai akhir.
    </p>

    <?php if (! empty(session('errors'))): ?>
        <ul class="alert alert-danger">
            <?php foreach ((array) session('errors') as $err): ?>
                <li><?= esc(is_array($err) ? implode(', ', $err) : $err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= site_url('dpl/penilaian/' . $mahasiswa['id']) ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nilai keaktifan (30%)</label>
                <input type="number" step="0.01" min="0" max="100" name="nilai_keaktifan" value="<?= esc(old('nilai_keaktifan', $penilaian['nilai_keaktifan'] ?? '0')) ?>" required>
            </div>
            <div class="field">
                <label>Nilai logbook (30%)</label>
                <input type="number" step="0.01" min="0" max="100" name="nilai_logbook" value="<?= esc(old('nilai_logbook', $penilaian['nilai_logbook'] ?? '0')) ?>" required>
            </div>
            <div class="field">
                <label>Nilai laporan (40%)</label>
                <input type="number" step="0.01" min="0" max="100" name="nilai_laporan" value="<?= esc(old('nilai_laporan', $penilaian['nilai_laporan'] ?? '0')) ?>" required>
            </div>
            <div class="field">
                <label>Grade otomatis</label>
                <div class="input-readonly font-mono"><?= esc($penilaian['grade'] ?? 'Dihitung setelah disimpan') ?></div>
            </div>
        </div>
        <div class="field">
            <label>Catatan</label>
            <textarea name="catatan"><?= esc(old('catatan', $penilaian['catatan'] ?? '')) ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan penilaian</button>
            <a href="<?= site_url('dpl/penilaian') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
