<div class="card" style="max-width:640px">
    <div class="card-head"><h2>Upload laporan</h2></div>
    <form method="post" action="<?= site_url('mahasiswa/laporan') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="field">
            <label>Judul</label>
            <input type="text" name="judul" value="<?= esc(old('judul')) ?>" required>
        </div>
        <div class="field">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?= esc(old('deskripsi')) ?></textarea>
        </div>
        <div class="field">
            <label>File PDF (max 5MB)</label>
            <input type="file" name="file_laporan" accept=".pdf" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="<?= site_url('mahasiswa/laporan') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
