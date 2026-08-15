<div class="dash-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
    <div class="card">
        <div class="card-head"><h2>Data profil</h2></div>
        <form method="post" action="<?= site_url('dpl/profil') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="dpl_nama">Nama lengkap</label>
                <input id="dpl_nama" type="text" name="nama" value="<?= esc(old('nama', $profil['nama'] ?? $dpl['nama'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label for="dpl_email">Email</label>
                <input id="dpl_email" type="email" name="email" value="<?= esc(old('email', $profil['email'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label for="dpl_prodi">Program studi</label>
                <input id="dpl_prodi" type="text" name="prodi" value="<?= esc(old('prodi', $dpl['prodi'] ?? '')) ?>">
            </div>
            <div class="field">
                <label for="dpl_no_hp">Nomor HP / WA</label>
                <input id="dpl_no_hp" type="tel" name="no_hp" value="<?= esc(old('no_hp', $dpl['no_hp'] ?? '')) ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan profil</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-head"><h2>Ubah password</h2></div>
        <form method="post" action="<?= site_url('dpl/profil/password') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="dpl_current_password">Password lama</label>
                <input id="dpl_current_password" type="password" name="current_password" required autocomplete="current-password">
            </div>
            <div class="field">
                <label for="dpl_new_password">Password baru</label>
                <input id="dpl_new_password" type="password" name="new_password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="field">
                <label for="dpl_confirm_password">Konfirmasi password baru</label>
                <input id="dpl_confirm_password" type="password" name="confirm_password" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ubah password</button>
            </div>
        </form>
    </div>
</div>
