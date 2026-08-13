<?php $isEdit = ! empty($lokasi); ?>
<div class="card" style="max-width:640px">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> lokasi</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('admin/lokasi/' . $lokasi['id']) : site_url('admin/lokasi') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nama desa</label>
                <input type="text" name="nama_desa" value="<?= esc(old('nama_desa', $lokasi['nama_desa'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Kecamatan</label>
                <input type="text" name="kecamatan" value="<?= esc(old('kecamatan', $lokasi['kecamatan'] ?? '')) ?>">
            </div>
            <div class="field" style="grid-column:1/-1">
                <label>Kabupaten</label>
                <input type="text" name="kabupaten" value="<?= esc(old('kabupaten', $lokasi['kabupaten'] ?? '')) ?>">
                <div class="field-hint">Contoh: Maluku Tengah, Kota Ambon</div>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Tambah' ?></button>
            <a href="<?= site_url('admin/lokasi') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
