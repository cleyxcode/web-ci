<?php
$seo = config('Seo');
$siteUrl = rtrim($seo->siteUrl, '/');
$description = $seo->defaultDescription;
$keywords = implode(', ', $seo->keywords);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= esc($description) ?>">
    <meta name="keywords" content="<?= esc($keywords) ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="theme-color" content="#1B6B8A">
    <link rel="canonical" href="<?= esc($siteUrl . '/') ?>">
    <link rel="alternate" hreflang="id-ID" href="<?= esc($siteUrl . '/') ?>">
    <link rel="icon" href="<?= base_url('favicon.ico') ?>" sizes="any">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= esc($seo->locale) ?>">
    <meta property="og:site_name" content="<?= esc($seo->siteName) ?>">
    <meta property="og:title" content="KKN Tematik FILKOM — Sistem Monitoring Lapangan">
    <meta property="og:description" content="<?= esc($description) ?>">
    <meta property="og:url" content="<?= esc($siteUrl . '/') ?>">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="KKN Tematik FILKOM — Sistem Monitoring Lapangan">
    <meta name="twitter:description" content="<?= esc($description) ?>">
    <?php if (trim($seo->googleSiteVerification) !== ''): ?>
    <meta name="google-site-verification" content="<?= esc($seo->googleSiteVerification) ?>">
    <?php endif; ?>
    <title>KKN Tematik FILKOM — Sistem Monitoring Lapangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,400;6..12,500;6..12,600;6..12,700;6..12,800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="application/ld+json">
    <?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $seo->siteName,
        'url' => $siteUrl . '/',
        'description' => $description,
        'keywords' => $keywords,
        'inLanguage' => 'id-ID',
        'publisher' => [
            '@type' => 'Organization',
            'name' => 'Fakultas Ilmu Komputer UKIM',
            'url' => 'https://filkom.ukim.ac.id/',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
</head>
<body class="min-h-screen bg-slate-50 font-['Nunito_Sans'] text-slate-800 antialiased">
    <header class="mx-auto flex max-w-6xl items-center justify-between px-5 py-5 sm:px-8">
        <a class="flex items-center gap-3 font-extrabold tracking-tight text-slate-900" href="<?= esc($siteUrl . '/') ?>" aria-label="KKN Tematik FILKOM beranda">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 text-sm text-white shadow-lg shadow-violet-600/20">UK</span>
            <span>KKN Tematik FILKOM</span>
        </a>
        <a class="inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-sm font-extrabold text-slate-700 shadow-sm transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700" href="<?= site_url('login') ?>">Masuk ke Sistem</a>
    </header>

    <main class="mx-auto max-w-6xl px-5 pb-12 sm:px-8">
        <section class="grid items-center gap-10 py-12 md:grid-cols-[1.2fr_.8fr] md:py-20" aria-labelledby="hero-title">
            <div>
                <p class="mb-3 text-xs font-extrabold uppercase tracking-[0.16em] text-violet-600">Fakultas Ilmu Komputer · FILKOM</p>
                <h1 id="hero-title" class="max-w-3xl text-4xl font-extrabold leading-[1.08] tracking-tight text-slate-900 sm:text-5xl">Monitoring KKN Tematik FILKOM yang terarah dan terukur</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-500">Monitoring KKN Tematik FILKOM membantu mahasiswa, DPL, dan admin mengelola kegiatan lapangan, logbook KKN, laporan, lokasi GPS, evaluasi, dan penilaian dalam satu sistem.</p>
                <div class="mt-7">
                    <a class="inline-flex min-h-11 items-center justify-center rounded-xl bg-violet-600 px-5 text-sm font-extrabold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700" href="<?= site_url('login') ?>">Masuk ke dashboard</a>
                </div>
            </div>
            <div class="rounded-3xl border border-violet-100 bg-gradient-to-br from-violet-600 to-indigo-700 p-7 text-white shadow-xl shadow-indigo-950/15" aria-label="Ringkasan fitur sistem">
                <span class="text-xs font-extrabold uppercase tracking-[0.16em] text-white/70">Satu ruang kerja lapangan</span>
                <strong class="mt-4 block text-2xl leading-tight">Dokumentasi KKN lebih rapi, transparan, dan mudah dipantau.</strong>
                <div class="mt-7 grid grid-cols-2 gap-2">
                    <span class="rounded-xl bg-white/15 px-3 py-3 text-sm font-bold ring-1 ring-white/15">Logbook</span>
                    <span class="rounded-xl bg-white/15 px-3 py-3 text-sm font-bold ring-1 ring-white/15">Laporan</span>
                    <span class="rounded-xl bg-white/15 px-3 py-3 text-sm font-bold ring-1 ring-white/15">GPS Tim</span>
                    <span class="rounded-xl bg-white/15 px-3 py-3 text-sm font-bold ring-1 ring-white/15">Penilaian</span>
                </div>
            </div>
        </section>

        <section class="py-10" aria-labelledby="features-title">
            <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-violet-600">Fitur utama</p>
            <h2 id="features-title" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900">Fitur sistem monitoring KKN Tematik FILKOM</h2>
            <div class="mt-7 grid gap-4 md:grid-cols-3">
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                    <span class="text-sm font-extrabold text-violet-600">01</span>
                    <h3 class="mt-5 text-lg font-extrabold text-slate-900">Monitoring kegiatan</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Mahasiswa mengirim logbook dan dokumentasi kegiatan untuk ditinjau DPL secara terstruktur.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                    <span class="text-sm font-extrabold text-blue-600">02</span>
                    <h3 class="mt-5 text-lg font-extrabold text-slate-900">Lokasi dan tim</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Ketua kelompok mencatat GPS lokasi KKN agar admin dan DPL dapat memantau sebaran tim.</p>
                </article>
                <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-900/5">
                    <span class="text-sm font-extrabold text-emerald-600">03</span>
                    <h3 class="mt-5 text-lg font-extrabold text-slate-900">Laporan dan nilai</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Laporan, evaluasi, validasi DPL, dan hasil penilaian tersimpan dalam alur digital yang jelas.</p>
                </article>
            </div>
        </section>

        <section class="mt-6 flex flex-col gap-6 rounded-3xl bg-slate-900 p-7 text-white sm:flex-row sm:items-center sm:justify-between" aria-labelledby="cta-title">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.16em] text-violet-300">Mulai sekarang</p>
                <h2 id="cta-title" class="mt-2 text-2xl font-extrabold">Siap mengelola KKN dengan lebih teratur?</h2>
            </div>
            <a class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-xl bg-white px-5 text-sm font-extrabold text-slate-900 transition hover:bg-violet-50" href="<?= site_url('login') ?>">Buka sistem monitoring</a>
        </section>
    </main>

    <footer class="mx-auto flex max-w-6xl flex-col gap-1 px-5 py-8 text-xs font-semibold text-slate-400 sm:flex-row sm:justify-between sm:px-8">
        <span>KKN Tematik FILKOM</span>
        <span>Fakultas Ilmu Komputer UKIM</span>
    </footer>
</body>
</html>
