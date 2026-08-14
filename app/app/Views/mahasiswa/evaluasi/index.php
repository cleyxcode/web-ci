<?php
$e = $evaluasi ?? null;
$m = $mahasiswa ?? [];
$stars = static function (int $value): string {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $value ? '★' : '☆';
    }

    return $html;
};
?>

<div class="hero-strip">
    <div class="periode">Umpan balik KKN Tematik</div>
    <h2 style="margin:0 0 8px;font-size:1.25rem">Evaluasi Kegiatan</h2>
    <p style="margin:0;color:var(--abu-karang);font-size:0.9rem">
        Berikan penilaian terhadap pelaksanaan kegiatan KKN yang telah Anda ikuti.
        Masukan ini dibaca DPL dan admin untuk perbaikan program.
    </p>
</div>

<?php if (empty($m['kelompok_id'])): ?>
    <div class="card">
        <p class="empty">Anda belum ditempatkan di kelompok KKN. Hubungi admin sebelum mengisi evaluasi.</p>
    </div>
<?php else: ?>

<?php if ($e): ?>
<div class="stat-row">
    <div class="stat">
        <div class="label">Rating keseluruhan</div>
        <div class="value" style="font-size:1.1rem;letter-spacing:2px;color:var(--kuning-senja)"><?= $stars((int) $e['rating']) ?></div>
        <small><?= (int) $e['rating'] ?> / 5<?= ! empty($e['kategori']) ? ' · ' . esc($e['kategori']) : '' ?></small>
    </div>
    <div class="stat">
        <div class="label">Bimbingan DPL</div>
        <div class="value"><?= (int) ($e['aspek_bimbingan'] ?? 0) ?>/5</div>
    </div>
    <div class="stat">
        <div class="label">Lokasi</div>
        <div class="value"><?= (int) ($e['aspek_lokasi'] ?? 0) ?>/5</div>
    </div>
    <div class="stat">
        <div class="label">Pelaksanaan</div>
        <div class="value"><?= (int) ($e['aspek_pelaksanaan'] ?? 0) ?>/5</div>
    </div>
</div>
<?php endif; ?>

<div class="card" style="max-width:720px">
    <div class="card-head">
        <h2><?= $e ? 'Perbarui evaluasi' : 'Isi evaluasi kegiatan' ?></h2>
        <?php if ($e): ?>
            <span class="stempel stempel-divalidasi">Sudah dikirim</span>
        <?php endif; ?>
    </div>

    <?php if (! empty(session('errors'))): ?>
        <ul class="alert alert-danger" style="margin-bottom:16px">
            <?php foreach ((array) session('errors') as $err): ?>
                <li><?= esc(is_array($err) ? implode(', ', $err) : $err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="<?= site_url('mahasiswa/evaluasi') ?>">
        <?= csrf_field() ?>

        <div class="field">
            <label>Rating keseluruhan (1–5)</label>
            <select name="rating" required>
                <option value="">Pilih rating</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (string) old('rating', $e['rating'] ?? '') === (string) $i ? 'selected' : '' ?>>
                        <?= $i ?> — <?= ['', 'Sangat kurang', 'Kurang', 'Cukup', 'Baik', 'Sangat baik'][$i] ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="field">
            <label>Kualitas bimbingan DPL</label>
            <select name="aspek_bimbingan" required>
                <option value="">Pilih nilai</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (string) old('aspek_bimbingan', $e['aspek_bimbingan'] ?? '') === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <span class="field-hint">Keaktifan pendampingan, respons, dan arahan DPL.</span>
        </div>

        <div class="field">
            <label>Lokasi & fasilitas lapangan</label>
            <select name="aspek_lokasi" required>
                <option value="">Pilih nilai</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (string) old('aspek_lokasi', $e['aspek_lokasi'] ?? '') === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="field">
            <label>Pelaksanaan kegiatan KKN Tematik</label>
            <select name="aspek_pelaksanaan" required>
                <option value="">Pilih nilai</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (string) old('aspek_pelaksanaan', $e['aspek_pelaksanaan'] ?? '') === (string) $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
            <span class="field-hint">Kesesuaian program, kebermanfaatan, dan kelancaran kegiatan.</span>
        </div>

        <div class="field">
            <label>Komentar / saran</label>
            <textarea name="komentar" rows="4" placeholder="Ceritakan pengalaman dan saran perbaikan…"><?= esc(old('komentar', $e['komentar'] ?? '')) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $e ? 'Perbarui evaluasi' : 'Kirim evaluasi' ?></button>
            <a href="<?= site_url('mahasiswa/dashboard') ?>" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
<?php endif; ?>
