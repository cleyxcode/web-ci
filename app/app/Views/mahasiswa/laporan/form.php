<?php $isEdit = ! empty($laporan); ?>
<div class="card">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Upload' ?> laporan</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('mahasiswa/laporan/' . (int) $laporan['id']) : site_url('mahasiswa/laporan') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field">
            <label>Judul</label>
            <input type="text" name="judul" value="<?= esc(old('judul', $laporan['judul'] ?? '')) ?>" required>
        </div>
        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?= esc(old('deskripsi', $laporan['deskripsi'] ?? '')) ?></textarea>
        </div>
        <div class="field">
            <label>File PDF (max 5MB)<?= $isEdit ? ' — Kosongkan jika tidak ingin mengubah' : '' ?></label>
            <input type="file" name="file_laporan" accept=".pdf" <?= $isEdit ? '' : 'required' ?>>
            <?php if ($isEdit && ! empty($laporan['file_laporan'])): ?>
                <span class="field-hint">File saat ini: <?= esc(basename($laporan['file_laporan'])) ?></span>
            <?php endif; ?>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan Perubahan' : 'Upload' ?></button>
            <a href="<?= site_url('mahasiswa/laporan') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
