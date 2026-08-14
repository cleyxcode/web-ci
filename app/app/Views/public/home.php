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
    <meta property="og:title" content="KKN Tematik UKIM — Sistem Monitoring Lapangan">
    <meta property="og:description" content="<?= esc($description) ?>">
    <meta property="og:url" content="<?= esc($siteUrl . '/') ?>">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="KKN Tematik UKIM — Sistem Monitoring Lapangan">
    <meta name="twitter:description" content="<?= esc($description) ?>">
    <?php if (trim($seo->googleSiteVerification) !== ''): ?>
    <meta name="google-site-verification" content="<?= esc($seo->googleSiteVerification) ?>">
    <?php endif; ?>
    <title>KKN Tematik UKIM — Sistem Monitoring Lapangan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
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
            'name' => 'Universitas Kristen Indonesia Maluku',
            'url' => 'https://ukim.ac.id/',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>
</head>
<body class="seo-home">
    <header class="seo-nav">
        <a class="seo-brand" href="<?= esc($siteUrl . '/') ?>" aria-label="KKN Tematik UKIM beranda">
            <span class="seo-mark">UK</span>
            <span>KKN Tematik UKIM</span>
        </a>
        <a class="btn btn-secondary" href="<?= site_url('login') ?>">Masuk ke Sistem</a>
    </header>

    <main>
        <section class="seo-hero" aria-labelledby="hero-title">
            <div class="seo-hero-copy">
                <p class="seo-eyebrow">Fakultas Ilmu Komputer · UKIM</p>
                <h1 id="hero-title">Monitoring KKN Tematik UKIM yang terarah dan terukur</h1>
                <p class="seo-lead">Monitoring KKN Tematik UKIM membantu mahasiswa, DPL, dan admin mengelola kegiatan lapangan, logbook KKN, laporan, lokasi GPS, evaluasi, dan penilaian dalam satu sistem.</p>
                <div class="seo-actions">
                    <a class="btn btn-primary" href="<?= site_url('login') ?>">Masuk ke dashboard</a>
                    <a class="btn btn-secondary" href="<?= site_url('register') ?>">Daftar sebagai mahasiswa</a>
                </div>
            </div>
            <div class="seo-hero-card" aria-label="Ringkasan fitur sistem">
                <span class="seo-card-label">Satu ruang kerja lapangan</span>
                <strong>Dokumentasi KKN lebih rapi, transparan, dan mudah dipantau.</strong>
                <div class="seo-card-grid">
                    <span>Logbook</span>
                    <span>Laporan</span>
                    <span>GPS Tim</span>
                    <span>Penilaian</span>
                </div>
            </div>
        </section>

        <section class="seo-section" aria-labelledby="features-title">
            <p class="seo-eyebrow">Fitur utama</p>
            <h2 id="features-title">Fitur sistem monitoring KKN Tematik UKIM</h2>
            <div class="seo-feature-grid">
                <article class="seo-feature-card">
                    <span class="seo-feature-number">01</span>
                    <h3>Monitoring kegiatan</h3>
                    <p>Mahasiswa mengirim logbook dan dokumentasi kegiatan untuk ditinjau DPL secara terstruktur.</p>
                </article>
                <article class="seo-feature-card">
                    <span class="seo-feature-number">02</span>
                    <h3>Lokasi dan tim</h3>
                    <p>Ketua kelompok mencatat GPS lokasi KKN agar admin dan DPL dapat memantau sebaran tim.</p>
                </article>
                <article class="seo-feature-card">
                    <span class="seo-feature-number">03</span>
                    <h3>Laporan dan nilai</h3>
                    <p>Laporan, evaluasi, validasi DPL, dan hasil penilaian tersimpan dalam alur digital yang jelas.</p>
                </article>
            </div>
        </section>

        <section class="seo-cta" aria-labelledby="cta-title">
            <div>
                <p class="seo-eyebrow">Mulai sekarang</p>
                <h2 id="cta-title">Siap mengelola KKN dengan lebih teratur?</h2>
            </div>
            <a class="btn btn-primary" href="<?= site_url('login') ?>">Buka sistem monitoring</a>
        </section>
    </main>

    <footer class="seo-footer">
        <span>KKN Tematik UKIM</span>
        <span>Universitas Kristen Indonesia Maluku</span>
    </footer>
</body>
</html>
