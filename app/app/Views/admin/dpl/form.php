<?php $isEdit = ! empty($dpl); ?>
<div class="card" style="max-width:720px">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Tambah' ?> DPL</h2></div>
    <form method="post" action="<?= $isEdit ? site_url('admin/dpl/' . $dpl['id']) : site_url('admin/dpl') ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= esc(old('nama', $dpl['nama'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>NIDN</label>
                <input type="text" name="nidn" class="font-mono" value="<?= esc(old('nidn', $dpl['nidn'] ?? '')) ?>" required>
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
                <input type="email" name="email" value="<?= esc(old('email', $dpl['email'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Prodi</label>
                <input type="text" name="prodi" value="<?= esc(old('prodi', $dpl['prodi'] ?? '')) ?>">
            </div>
            <div class="field">
                <label>No. HP</label>
                <input type="text" name="no_hp" value="<?= esc(old('no_hp', $dpl['no_hp'] ?? '')) ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Tambah' ?></button>
            <a href="<?= site_url('admin/dpl') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
