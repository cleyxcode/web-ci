<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($title ?? 'KKN Tematik FILKOM') ?> — Monitoring</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,300;6..12,400;6..12,500;6..12,600;6..12,700;6..12,800;6..12,900&display=swap" rel="stylesheet">
    <script>
      (function () {
        const t = localStorage.getItem('kkn-theme');
        if (t === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
      })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: ['selector', '[data-theme="dark"]'] };</script>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
      .auth-hero-blob { position: absolute; border-radius: 50%; background: rgba(255,255,255,0.06); animation: floatBlob 8s ease-in-out infinite; }
      .auth-hero-blob:nth-child(2) { animation-delay: -3s; animation-duration: 11s; }
      .auth-hero-blob:nth-child(3) { animation-delay: -6s; animation-duration: 9s; }
      @keyframes floatBlob { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-20px) rotate(5deg)} }
      .auth-card-slide { animation: slideUp 0.4s cubic-bezier(0.16,1,0.3,1); }
      @keyframes slideUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="min-h-screen bg-slate-100 font-['Nunito_Sans'] text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
<div class="flex min-h-screen flex-col lg:flex-row">

    <!-- Left Hero Panel -->
    <div class="relative hidden overflow-hidden bg-gradient-to-br from-violet-700 via-indigo-700 to-blue-800 lg:flex lg:w-[460px] lg:flex-col lg:justify-between lg:p-12 xl:w-[500px]">
        <div class="auth-hero-blob" style="width:360px;height:360px;top:-100px;right:-120px;"></div>
        <div class="auth-hero-blob" style="width:240px;height:240px;bottom:60px;left:-80px;"></div>
        <div class="auth-hero-blob" style="width:160px;height:160px;top:42%;right:40px;background:rgba(255,255,255,0.04);"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white/20 ring-2 ring-white/30 backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-7 w-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 10 12 5 2 10l10 5 10-5zm0 0v6M6 12.5V17c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-base font-extrabold tracking-tight text-white">KKN TEMATIK</div>
                    <div class="text-sm font-semibold text-white/65">FILKOM · Monitoring System</div>
                </div>
            </div>
            <div class="mt-16">
                <h1 class="text-[2.25rem] font-extrabold leading-tight tracking-tight text-white">
                    Pantau.<br>Validasi.<br>Evaluasi.
                </h1>
                <p class="mt-5 text-base leading-relaxed text-white/70">
                    Platform monitoring KKN Tematik Fakultas Ilmu Komputer. Satu sistem untuk mahasiswa, DPL, dan admin kampus.
                </p>
            </div>
            <div class="mt-10 space-y-3.5">
                <div class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/15 ring-1 ring-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                    </div>
                    <span class="text-sm font-semibold text-white/80">Validasi logbook & laporan real-time</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/15 ring-1 ring-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5-2V4l5 2 6-2 5 2v14l-5-2-6 2zM9 6v14M15 4v14"/></svg>
                    </div>
                    <span class="text-sm font-semibold text-white/80">Peta GPS lokasi kelompok KKN</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/15 ring-1 ring-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/></svg>
                    </div>
                    <span class="text-sm font-semibold text-white/80">Evaluasi & penilaian mahasiswa</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-white/15 ring-1 ring-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-4.5 w-4.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17H9a6 6 0 1 1 6-6v.5L13 13l-1-2M12 8v4l2 2"/></svg>
                    </div>
                    <span class="text-sm font-semibold text-white/80">Notifikasi real-time via Pusher</span>
                </div>
            </div>
        </div>
        <div class="relative z-10 mt-8">
            <p class="text-xs text-white/35">© <?= date('Y') ?> Fakultas Ilmu Komputer · KKN Tematik FILKOM</p>
        </div>
    </div>

    <!-- Right content area -->
    <div class="flex flex-1 flex-col">
        <!-- Mobile header bar -->
        <div class="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 lg:hidden dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 shadow-md shadow-violet-500/30">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M22 10 12 5 2 10l10 5 10-5zm0 0v6M6 12.5V17c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white">KKN Tematik FILKOM</div>
                    <div class="text-xs text-slate-500">Monitoring System</div>
                </div>
            </div>
            <button type="button" data-theme-toggle class="grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Ganti tema">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 dark:hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="hidden h-5 w-5 dark:block"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            </button>
        </div>

        <div class="hidden justify-end px-8 pt-6 lg:flex">
            <button type="button" data-theme-toggle class="grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800" aria-label="Ganti tema">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="h-5 w-5 dark:hidden"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" class="hidden h-5 w-5 dark:block"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
            </button>
        </div>

        <main class="flex flex-1 items-center justify-center p-5 sm:p-8
            [&_.auth-shell]:w-full [&_.auth-shell]:max-w-md [&_.auth-shell]:mx-auto
            [&_.auth-card]:w-full [&_.auth-card]:rounded-3xl [&_.auth-card]:border [&_.auth-card]:border-slate-200 [&_.auth-card]:bg-white [&_.auth-card]:p-7 [&_.auth-card]:shadow-xl [&_.auth-card]:shadow-slate-900/8 dark:[&_.auth-card]:border-slate-800 dark:[&_.auth-card]:bg-slate-900
            [&_.auth-brand]:mb-7
            [&_.auth-brand_h1]:mt-3 [&_.auth-brand_h1]:text-2xl [&_.auth-brand_h1]:font-extrabold [&_.auth-brand_h1]:tracking-tight [&_.auth-brand_h1]:text-slate-900 dark:[&_.auth-brand_h1]:text-white
            [&_.auth-brand_p]:mt-1 [&_.auth-brand_p]:text-sm [&_.auth-brand_p]:text-slate-500
            [&_.mark]:grid [&_.mark]:h-13 [&_.mark]:w-13 [&_.mark]:place-items-center [&_.mark]:rounded-2xl [&_.mark]:bg-gradient-to-br [&_.mark]:from-violet-600 [&_.mark]:to-indigo-700 [&_.mark]:shadow-lg [&_.mark]:shadow-violet-500/30
            [&_.field]:mb-4
            [&_.field_label]:mb-1.5 [&_.field_label]:block [&_.field_label]:text-sm [&_.field_label]:font-bold [&_.field_label]:text-slate-700 dark:[&_.field_label]:text-slate-200
            [&_input]:w-full [&_input]:rounded-xl [&_input]:border [&_input]:border-slate-200 [&_input]:bg-slate-50 [&_input]:px-4 [&_input]:py-3 [&_input]:text-sm [&_input]:font-semibold [&_input]:text-slate-800 [&_input]:outline-none [&_input]:transition focus:[&_input]:border-violet-400 focus:[&_input]:bg-white focus:[&_input]:ring-4 focus:[&_input]:ring-violet-100 dark:[&_input]:border-slate-700 dark:[&_input]:bg-slate-800 dark:[&_input]:text-white dark:focus:[&_input]:ring-violet-950
            [&_.field-hint]:mt-1.5 [&_.field-hint]:block [&_.field-hint]:text-xs [&_.field-hint]:text-slate-400
            [&_.btn]:inline-flex [&_.btn]:w-full [&_.btn]:min-h-12 [&_.btn]:items-center [&_.btn]:justify-center [&_.btn]:gap-2 [&_.btn]:rounded-xl [&_.btn]:px-5 [&_.btn]:text-sm [&_.btn]:font-extrabold [&_.btn]:tracking-wide [&_.btn]:transition
            [&_.btn-primary]:bg-gradient-to-r [&_.btn-primary]:from-violet-600 [&_.btn-primary]:to-indigo-600 [&_.btn-primary]:text-white [&_.btn-primary]:shadow-lg [&_.btn-primary]:shadow-violet-500/30 hover:[&_.btn-primary]:from-violet-700 hover:[&_.btn-primary]:to-indigo-700 hover:[&_.btn-primary]:shadow-violet-500/40 active:[&_.btn-primary]:scale-[0.98]
            [&_.btn-secondary]:border [&_.btn-secondary]:border-slate-200 [&_.btn-secondary]:bg-white [&_.btn-secondary]:text-slate-700 hover:[&_.btn-secondary]:bg-slate-50 dark:[&_.btn-secondary]:border-slate-700 dark:[&_.btn-secondary]:bg-slate-800 dark:[&_.btn-secondary]:text-slate-100
            [&_.password-wrap]:relative [&_.password-wrap_input]:pr-12
            [&_.password-toggle]:absolute [&_.password-toggle]:right-3 [&_.password-toggle]:top-1/2 [&_.password-toggle]:-translate-y-1/2 [&_.password-toggle]:rounded-lg [&_.password-toggle]:p-1.5 [&_.password-toggle]:text-slate-400 hover:[&_.password-toggle]:bg-slate-100 hover:[&_.password-toggle]:text-slate-600
            [&_.password-toggle_svg]:h-5 [&_.password-toggle_svg]:w-5
            [&_.icon-eye-off]:hidden [&_.password-toggle.is-visible_.icon-eye]:hidden [&_.password-toggle.is-visible_.icon-eye-off]:block
            [&_.otp-row]:mb-5 [&_.otp-row]:grid [&_.otp-row]:grid-cols-6 [&_.otp-row]:gap-2 [&_.otp-row_input]:px-0 [&_.otp-row_input]:text-center [&_.otp-row_input]:text-xl [&_.otp-row_input]:font-extrabold
            [&_.flash-message]:mb-4
            [&_.alert]:mb-4 [&_.alert]:rounded-xl [&_.alert]:border [&_.alert]:border-rose-200 [&_.alert]:bg-rose-50 [&_.alert]:p-3 [&_.alert]:text-sm [&_.alert]:text-rose-700
            [&_p.auth-links]:mt-5 [&_p.auth-links]:text-center [&_p.auth-links]:text-sm [&_p.auth-links]:text-slate-500
            [&_p.auth-links_a]:font-bold [&_p.auth-links_a]:text-violet-600 hover:[&_p.auth-links_a]:text-violet-700
            [&_p.auth-links_span]:block [&_p.auth-links_span]:mt-1 [&_p.auth-links_span]:text-xs [&_p.auth-links_span]:text-slate-400
            ">
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>
<script>
  // Dark mode toggle for auth pages
  document.querySelectorAll('[data-theme-toggle]').forEach((authThemeBtn) => {
    authThemeBtn.addEventListener('click', () => {
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
      localStorage.setItem('kkn-theme', isDark ? 'light' : 'dark');
    });
  });
  // Password toggle
  document.querySelectorAll('.password-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-target');
      const input = id ? document.getElementById(id) : btn.previousElementSibling;
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.classList.toggle('is-visible', show);
    });
  });
</script>
</body>
</html>
