<?php $isEdit = ! empty($mahasiswa); ?>
<div class="card" style="max-width:720px">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> mahasiswa</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('admin/mahasiswa/' . $mahasiswa['id']) : site_url('admin/mahasiswa') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= esc(old('nama', $mahasiswa['nama'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>NPM</label>
                <input type="text" name="npm" class="font-mono" value="<?= esc(old('npm', $mahasiswa['npm'] ?? '')) ?>" required>
            </div>
            <?php if (! $isEdit): ?>
            <div class="field">
                <label>Username</label>
                <input type="text" name="username" value="<?= esc(old('username')) ?>" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <?php endif; ?>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= esc(old('email', $mahasiswa['email'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Prodi</label>
                <input type="text" name="prodi" value="<?= esc(old('prodi', $mahasiswa['prodi'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>No. HP</label>
                <input type="text" name="no_hp" value="<?= esc(old('no_hp', $mahasiswa['no_hp'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>Kelompok KKN</label>
                <select name="kelompok_id">
                    <option value="">— Pilih —</option>
                    <?php foreach ($kelompok ?? [] as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= (string) old('kelompok_id', $mahasiswa['kelompok_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>>
                            <?= esc($k['nama_kelompok']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Tambah' ?></button>
            <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
