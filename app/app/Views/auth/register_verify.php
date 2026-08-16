<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<div class="auth-shell">
    <div class="auth-card">
        <div class="auth-brand">
            <div class="mark">UK</div>
            <h1>Verifikasi Email</h1>
            <p>Kode dikirim ke <strong><?= esc($email ?? '') ?></strong></p>
        </div>

        <?= view('partials/flash') ?>

        <form method="post" action="<?= site_url('register/verify') ?>" id="otp-form">
            <?= csrf_field() ?>
            <div class="otp-row">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <input type="text" name="otp[]" maxlength="1"
                           inputmode="numeric" pattern="[0-9]"
                           required autocomplete="one-time-code"
                           aria-label="Digit <?= $i + 1 ?>"
                           <?= $i === 0 ? 'autofocus' : '' ?>>
                <?php endfor; ?>
            </div>

            <button type="submit" class="btn btn-primary">
                Verifikasi &amp; Buat Akun
            </button>
        </form>

        <p>
            Tidak menerima kode?
            <form method="post" action="<?= site_url('register/resend') ?>">
                <?= csrf_field() ?>
                <button type="submit"
                       >
                    Kirim ulang
                </button>
            </form>
        </p>

        <p>
            Email salah?
            <a href="<?= site_url('register') ?>">Kembali ke formulir daftar</a>
        </p>
    </div>
</div>
<script>
(() => {
    const inputs = [...document.querySelectorAll('.otp-row input')];
    inputs.forEach((input, i) => {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(0, 1);
            if (input.value && i < inputs.length - 1) inputs[i + 1].focus();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && i > 0) inputs[i - 1].focus();
        });
        input.addEventListener('paste', (e) => {
            const text = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
            if (!text) return;
            e.preventDefault();
            text.split('').forEach((ch, idx) => { if (inputs[idx]) inputs[idx].value = ch; });
            inputs[Math.min(text.length, 5)].focus();
        });
    });
})();
</script>
<?= $this->endSection() ?>
