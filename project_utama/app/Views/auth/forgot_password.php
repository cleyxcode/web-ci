<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>Lupa Password</h1>
            <p>Kami kirim OTP 6 digit ke email Anda</p>
        </div>
        <?= view('partials/flash') ?>
        <form method="post" action="<?= site_url('forgot-password') ?>">
            <?= csrf_field() ?>
            <div class="field">
                <label for="email">Email terdaftar</label>
                <input type="email" id="email" name="email" value="<?= esc(old('email')) ?>" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Kirim OTP</button>
        </form>
        <p>
            <a href="<?= site_url('login') ?>">← Kembali ke login</a>
        </p>
    </div>
</div>
<?= $this->endSection() ?>
