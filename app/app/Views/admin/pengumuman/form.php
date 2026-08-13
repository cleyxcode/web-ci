<div class="card" style="max-width:720px">
    <div class="card-head"><h2>Buat pengumuman</h2></div>
    <form method="post" action="<?= site_url('admin/pengumuman') ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label>Judul</label>
            <input type="text" name="judul" value="<?= esc(old('judul')) ?>" required>
        </div>
        <div class="field">
            <label>Isi</label>
            <textarea name="isi" required><?= esc(old('isi')) ?></textarea>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Publikasikan</button>
            <a href="<?= site_url('admin/pengumuman') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
