<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px" class="dash-grid">
    <div class="card">
        <div class="card-head"><h2>Profil</h2></div>
        <form method="post" action="<?= site_url('mahasiswa/profil') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label>Nama</label>
                <input type="text" name="nama" value="<?= esc(old('nama', $profil['nama'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= esc(old('email', $profil['email'] ?? '')) ?>" required>
            </div>
            <div class="field">
                <label>Username</label>
                <input type="text" value="<?= esc($profil['username'] ?? '') ?>" disabled>
                <div class="field-hint">Username tidak dapat diubah</div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Simpan profil</button>
            </div>
        </form>
    </div>
    <div class="card">
        <div class="card-head"><h2>Ubah password</h2></div>
        <form method="post" action="<?= site_url('mahasiswa/profil/password') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label>Password lama</label>
                <div class="password-wrap">
                    <input type="password" name="current_password" id="mhs_current_password" required>
                    <button type="button" class="password-toggle" data-target="mhs_current_password" aria-label="Tampilkan password">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                    </button>
                </div>
            </div>
            <div class="field">
                <label>Password baru</label>
                <div class="password-wrap">
                    <input type="password" name="new_password" id="mhs_new_password" required minlength="6">
                    <button type="button" class="password-toggle" data-target="mhs_new_password" aria-label="Tampilkan password">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><path stroke-linecap="round" stroke-linejoin="round" d="M1 1l22 22"/></svg>
                    </button>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ubah password</button>
            </div>
        </form>
    </div>
</div>
<style>@media (max-width:900px){.dash-grid{grid-template-columns:1fr!important}}</style>
