<?php $isEdit = ! empty($logbook); ?>
<div class="card">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> logbook</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('mahasiswa/logbook/' . (int) $logbook['id']) : site_url('mahasiswa/logbook') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= esc(old('tanggal', $logbook['tanggal'] ?? date('Y-m-d'))) ?>" required>
        </div>
        <div class="field">
            <label>Kegiatan</label>
            <textarea name="kegiatan" required><?= esc(old('kegiatan', $logbook['kegiatan'] ?? '')) ?></textarea>
        </div>
        <div class="field">
            <label>Lokasi kegiatan</label>
            <input type="text" name="lokasi_kegiatan" value="<?= esc(old('lokasi_kegiatan', $logbook['lokasi_kegiatan'] ?? '')) ?>">
        </div>
        <div class="field">
            <label>Dokumentasi (jpg/png, max 5MB)<?= $isEdit ? ' — Kosongkan jika tidak ingin mengubah' : '' ?></label>
            <input type="file" name="dokumentasi" accept=".jpg,.jpeg,.png">
            <?php if ($isEdit && ! empty($logbook['dokumentasi'])): ?>
                <span class="field-hint">Dokumentasi saat ini: <?= esc(basename($logbook['dokumentasi'])) ?></span>
            <?php endif; ?>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Submit' ?></button>
            <a href="<?= site_url('mahasiswa/logbook') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
