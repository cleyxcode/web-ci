<div class="card" style="max-width:640px">
    <div class="card-head"><h2>Tambah logbook</h2></div>
    <form method="post" action="<?= site_url('mahasiswa/logbook') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?= esc(old('tanggal', date('Y-m-d'))) ?>" required>
        </div>
        <div class="field">
            <label>Kegiatan</label>
            <textarea name="kegiatan" required><?= esc(old('kegiatan')) ?></textarea>
        </div>
        <div class="field">
            <label>Lokasi kegiatan</label>
            <input type="text" name="lokasi_kegiatan" value="<?= esc(old('lokasi_kegiatan')) ?>">
        </div>
        <div class="field">
            <label>Dokumentasi (jpg/png, max 5MB)</label>
            <input type="file" name="dokumentasi" accept=".jpg,.jpeg,.png">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?= site_url('mahasiswa/logbook') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
