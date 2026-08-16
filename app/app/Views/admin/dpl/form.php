<?php $isEdit = ! empty($dpl); ?>
<div class="card">
    <div class="card-head"><h2><?= $isEdit ? 'Edit' : 'Buat akun' ?> DPL</h2></div>
    <p>
        DPL (Dosen Pembimbing Lapangan) login memakai username &amp; password yang Anda buat di sini.
        Setelah disimpan, bagikan kredensial tersebut kepada dosen yang bersangkutan.
    </p>
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
                <label>Username login</label>
                <input type="text" name="username" value="<?= esc(old('username')) ?>" required autocomplete="off">
            </div>
            <div class="field">
                <label>Password awal</label>
                <input type="text" name="password" required minlength="6" autocomplete="off" placeholder="Min. 6 karakter">
                <span class="field-hint">Tampilkan sebagai teks agar mudah disalin dan dibagikan ke DPL.</span>
            </div>
            <?php else: ?>
            <div class="field">
                <label>Username</label>
                <input type="text" value="<?= esc($dpl['username'] ?? '') ?>" disabled class="font-mono">
            </div>
            <div class="field">
                <label>Password baru (opsional)</label>
                <input type="text" name="password" minlength="6" autocomplete="off" placeholder="Kosongkan jika tidak diubah">
                <span class="field-hint">Isi hanya jika ingin mereset password akun DPL.</span>
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
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Simpan' : 'Buat akun DPL' ?></button>
            <a href="<?= site_url('admin/dpl') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
