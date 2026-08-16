<?php
$uri = uri_string();
$user = $user ?? current_user();
$menus = $menus ?? panel_menus($user['role'] ?? '');
$iconSvg = static function (string $name): string {
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-10.5z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'academic' => '<path stroke-linecap="round" stroke-linejoin="round" d="M22 10 12 5 2 10l10 5 10-5zm0 0v6M6 12.5V17c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/>',
        'group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a2 2 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'map' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5-2V4l5 2 6-2 5 2v14l-5-2-6 2zM9 6v14M15 4v14"/>',
        'doc' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 0v6h6M8 13h8M8 17h5"/>',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>',
        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'star' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4v15.5A2.5 2.5 0 0 1 6.5 17H20V4H6.5A2.5 2.5 0 0 0 4 6.5"/>',
        'upload' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1 2-2v-4M17 8l-5-5-5 5M12 3v12"/>',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
        'moon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16M8 17V10M12 17V7M16 17v-4"/>',
        'download' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>',
        'history' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 3-6.7M3 4v5h5M12 7v5l3 2"/>',
        'settings' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7Zm0-12v2m0 13v2m8.5-8.5h-2m-13 0h-2m14.52-6.52-1.42 1.42M6.4 17.6l-1.42 1.42m12.04 0-1.42-1.42M6.4 6.4 4.98 4.98"/>',
        'logout' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5M15 12H3M21 19V5a2 2 0 0 0-2-2h-6"/>',
    ];
    return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">' . ($icons[$name] ?? $icons['home']) . '</svg>';
};
$mobileMenus = array_values(array_filter($menus, static fn ($m) => ! empty($m['mobile']))) ?: array_slice($menus, 0, 5);
$initial = strtoupper(mb_substr($user['nama'] ?? 'U', 0, 1));
$unread = (int) ($unreadCount ?? count($notifikasi ?? []));
$role = (string) ($user['role'] ?? 'guest');
$profilUrl = match ($role) {
    'admin' => site_url('admin/profil'),
    'dpl' => site_url('dpl/profil'),
    'mahasiswa' => site_url('mahasiswa/profil'),
    default => '#',
};
$roleTheme = match ($role) {
    'admin' => 'from-violet-950 via-violet-900 to-indigo-900',
    'dpl' => 'from-emerald-950 via-emerald-900 to-teal-900',
    default => 'from-sky-950 via-blue-900 to-indigo-900',
};
$roleAccent = match ($role) {
    'admin' => 'from-violet-500 to-indigo-600',
    'dpl' => 'from-emerald-400 to-teal-500',
    default => 'from-sky-400 to-blue-500',
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($title ?? 'Panel') ?> — KKN Tematik FILKOM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,400;6..12,500;6..12,600;6..12,700;6..12,800&display=swap" rel="stylesheet">
    <script>
      (function () {
        const t = localStorage.getItem('kkn-theme');
        if (t === 'dark' || !t) document.documentElement.setAttribute('data-theme', 'dark');
      })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: ['selector', '[data-theme="dark"]'] };</script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
</head>
<body class="min-h-screen bg-slate-50 font-['Nunito_Sans'] text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
<div class="min-h-screen lg:flex">
    <aside class="panel-sidebar hidden w-[260px] shrink-0 flex-col overflow-y-auto overscroll-contain bg-gradient-to-b <?= $roleTheme ?> px-4 py-5 text-white shadow-2xl shadow-indigo-950/10 lg:fixed lg:inset-y-0 lg:left-0 lg:flex" aria-label="Navigasi utama">
        <a class="flex items-center gap-3 px-3" href="<?= esc($menus[0]['url'] ?? '/') ?>">
            <span class="grid h-10 w-10 place-items-center rounded-xl bg-white/15 ring-1 ring-white/25"><?= $iconSvg('check') ?></span>
            <span class="leading-tight"><strong class="block text-[15px] font-extrabold tracking-tight">KKN TEMATIK</strong><small class="text-[11px] font-semibold text-white/70">Monitoring System</small></span>
        </a>
        <a href="<?= esc($profilUrl) ?>" class="mt-7 flex items-center gap-3 rounded-2xl bg-white/10 p-3 ring-1 ring-white/10 transition hover:bg-white/15">
            <span class="grid h-11 w-11 place-items-center rounded-full bg-white text-sm font-extrabold text-indigo-700 shadow-sm"><?= esc($initial) ?></span>
            <span class="min-w-0"><strong class="block truncate text-sm"><?= esc($user['nama'] ?? 'Pengguna') ?></strong><small class="block text-xs text-white/70"><?= esc(ucfirst($role)) ?></small></span>
        </a>
        <p class="mt-8 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-white/45">Menu utama</p>
        <nav class="mt-3 space-y-1" aria-label="Menu panel">
            <?php foreach ($menus as $menu): ?>
                <?php $active = str_starts_with('/' . $uri, rtrim($menu['url'], '/')) || $uri === ltrim($menu['url'], '/'); ?>
                <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-[13px] font-bold transition <?= $active ? 'bg-white/20 text-white shadow-sm ring-1 ring-white/20' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>" href="<?= esc($menu['url']) ?>">
                    <span class="h-[18px] w-[18px] shrink-0"><?= $iconSvg($menu['icon'] ?? 'home') ?></span><?= esc($menu['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <div class="min-w-0 flex-1 lg:ml-[260px]">
        <header class="sticky top-0 z-30 flex h-[68px] items-center justify-between border-b border-slate-200/80 bg-white/95 px-4 backdrop-blur lg:px-8 dark:border-slate-800 dark:bg-slate-900/95">
            <div class="min-w-0"><h1 class="truncate text-lg font-extrabold tracking-tight text-slate-900 lg:text-xl dark:text-white"><?= esc($title ?? '') ?></h1><p class="hidden text-xs font-semibold text-slate-400 sm:block">Fakultas Ilmu Komputer · FILKOM</p></div>
            <div class="flex items-center gap-1.5 sm:gap-3">
                <button type="button" class="grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800" id="theme-toggle" title="Mode gelap/terang" aria-label="Toggle dark mode"><span class="h-5 w-5 dark:hidden"><?= $iconSvg('moon') ?></span><span class="hidden h-5 w-5 dark:block"><?= $iconSvg('sun') ?></span></button>
                <div class="relative" id="notif-wrap">
                    <button type="button" class="relative grid h-10 w-10 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 dark:text-slate-300 dark:hover:bg-slate-800" id="notif-btn" title="Notifikasi" aria-label="Notifikasi" aria-expanded="false"><span class="h-5 w-5"><?= $iconSvg('bell') ?></span><?php if ($unread > 0): ?><span class="dot absolute right-2 top-2 h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white dark:ring-slate-900" id="notif-dot"></span><?php endif; ?></button>
                    <div class="absolute right-0 z-50 mt-2 w-[min(22rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900" id="notif-panel" hidden>
                        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-800"><strong class="text-sm text-slate-900 dark:text-white">Notifikasi</strong><?php if ($unread > 0): ?><button type="button" class="text-xs font-bold text-violet-600 hover:text-violet-700" id="notif-read-all">Tandai dibaca</button><?php endif; ?></div>
                        <div class="max-h-80 divide-y divide-slate-100 overflow-y-auto dark:divide-slate-800" id="notif-list">
                            <?php if (empty($notifikasiAll)): ?><p class="notif-empty px-4 py-7 text-center text-sm text-slate-400">Belum ada notifikasi</p><?php else: ?>
                                <?php foreach ($notifikasiAll as $n): ?><div class="notif-item cursor-pointer px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-800 <?= $n['is_read'] ? '' : 'unread bg-violet-50/60 dark:bg-violet-950/20' ?>" data-id="<?= (int) $n['id'] ?>"><strong class="block text-sm text-slate-800 dark:text-slate-100"><?= esc($n['judul']) ?></strong><p class="mt-1 text-xs text-slate-500"><?= esc(mb_strimwidth($n['pesan'], 0, 80, '…')) ?></p><small class="mt-1 block text-[11px] text-slate-400"><?= format_tanggal($n['created_at'] ?? null) ?></small></div><?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="relative" id="profile-wrap">
                    <button type="button" id="profile-btn" class="flex max-w-[13rem] items-center gap-2 rounded-xl p-1.5 text-left transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-violet-300 dark:hover:bg-slate-800 dark:focus:ring-violet-800" aria-label="Buka menu profil" aria-expanded="false" aria-controls="profile-panel">
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-gradient-to-br <?= esc($roleAccent) ?> text-xs font-extrabold text-white shadow-sm"><?= esc($initial) ?></span>
                        <span class="hidden min-w-0 sm:block"><strong class="block truncate text-xs text-slate-800 dark:text-white"><?= esc($user['nama'] ?? 'Pengguna') ?></strong><small class="block text-[11px] text-slate-400"><?= esc(ucfirst($role)) ?></small></span>
                        <svg aria-hidden="true" class="hidden h-4 w-4 shrink-0 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div id="profile-panel" class="absolute right-0 top-full z-50 mt-2 hidden w-[min(18rem,calc(100vw-1rem))] max-w-[calc(100vw-1rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/15 dark:border-slate-700 dark:bg-slate-900" role="menu">
                        <a href="<?= esc($profilUrl) ?>" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-violet-50 dark:hover:bg-violet-950/30" role="menuitem">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-950/60 dark:text-violet-300"><?= $iconSvg('user') ?></span>
                            <span class="min-w-0"><strong class="block text-sm font-extrabold text-slate-800 dark:text-white">Profil saya</strong><small class="block truncate text-xs text-slate-400">Kelola informasi akun</small></span>
                        </a>
                        <div class="my-1 border-t border-slate-100 dark:border-slate-800"></div>
                        <form method="get" action="<?= site_url('logout') ?>" data-confirm="Anda akan keluar dari akun. Lanjutkan?">
                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-rose-600 transition hover:bg-rose-50 dark:text-rose-400 dark:hover:bg-rose-950/30" role="menuitem">
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-rose-100 dark:bg-rose-950/50"><?= $iconSvg('logout') ?></span>
                                <span><strong class="block text-sm font-extrabold">Keluar dari akun</strong><small class="block text-xs text-rose-400">Akhiri sesi saat ini</small></span>
                            </button>
                        </form>
                    </div>
                </div>
                <form method="get" action="<?= site_url('logout') ?>" data-confirm="Anda akan keluar dari akun. Lanjutkan?" class="shrink-0">
                    <button type="submit" class="group grid h-10 w-10 place-items-center rounded-xl border border-rose-200 bg-rose-50 text-rose-600 transition hover:border-rose-300 hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-300 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-300 dark:hover:border-rose-800 dark:hover:bg-rose-950/60 sm:flex sm:w-auto sm:gap-2 sm:px-3" title="Keluar dari akun" aria-label="Keluar dari akun">
                        <span class="h-4 w-4"><?= $iconSvg('logout') ?></span>
                        <span class="hidden text-xs font-extrabold sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="min-h-[calc(100vh-68px)] p-4 pb-24 sm:p-6 lg:p-8 lg:pb-8
            [&_a]:transition-colors
            [&_.card]:mb-5 [&_.card]:rounded-2xl [&_.card]:border [&_.card]:border-slate-200 [&_.card]:bg-white [&_.card]:p-5 [&_.card]:shadow-sm [&_.card]:shadow-slate-900/5 dark:[&_.card]:border-slate-800 dark:[&_.card]:bg-slate-900
            [&_.card-head]:mb-4 [&_.card-head]:flex [&_.card-head]:flex-wrap [&_.card-head]:items-center [&_.card-head]:justify-between [&_.card-head]:gap-3 [&_.card-head_h2]:text-base [&_.card-head_h2]:font-extrabold [&_.card-head_h2]:text-slate-900 dark:[&_.card-head_h2]:text-white
            [&_.btn]:inline-flex [&_.btn]:min-h-10 [&_.btn]:items-center [&_.btn]:justify-center [&_.btn]:rounded-xl [&_.btn]:px-4 [&_.btn]:text-sm [&_.btn]:font-extrabold [&_.btn]:shadow-sm [&_.btn]:transition [&_.btn]:focus:outline-none [&_.btn]:focus:ring-4 [&_.btn]:focus:ring-violet-200 dark:[&_.btn]:focus:ring-violet-900
            [&_.btn-primary]:bg-violet-600 [&_.btn-primary]:text-white hover:[&_.btn-primary]:bg-violet-700 [&_.btn-primary]:shadow-violet-600/20
            [&_.btn-secondary]:border [&_.btn-secondary]:border-slate-200 [&_.btn-secondary]:bg-white [&_.btn-secondary]:text-slate-700 hover:[&_.btn-secondary]:border-violet-200 hover:[&_.btn-secondary]:bg-violet-50 hover:[&_.btn-secondary]:text-violet-700 dark:[&_.btn-secondary]:border-slate-700 dark:[&_.btn-secondary]:bg-slate-800 dark:[&_.btn-secondary]:text-slate-100
            [&_.btn-success]:bg-emerald-500 [&_.btn-success]:text-white hover:[&_.btn-success]:bg-emerald-600 [&_.btn-danger]:bg-rose-500 [&_.btn-danger]:text-white hover:[&_.btn-danger]:bg-rose-600 [&_.btn-danger-solid]:bg-rose-600 [&_.btn-danger-solid]:text-white [&_.btn-sm]:min-h-8 [&_.btn-sm]:rounded-lg [&_.btn-sm]:px-3 [&_.btn-sm]:text-xs
            [&_.dashboard-page]:space-y-5 [&_.admin-welcome]:mb-5 [&_.admin-welcome]:flex [&_.admin-welcome]:flex-col [&_.admin-welcome]:justify-between [&_.admin-welcome]:gap-4 [&_.admin-welcome]:rounded-2xl [&_.admin-welcome]:border [&_.admin-welcome]:border-violet-100 [&_.admin-welcome]:bg-gradient-to-r [&_.admin-welcome]:from-violet-600 [&_.admin-welcome]:to-indigo-700 [&_.admin-welcome]:p-5 [&_.admin-welcome]:text-white [&_.admin-welcome_p]:text-xs [&_.admin-welcome_p]:font-extrabold [&_.admin-welcome_p]:uppercase [&_.admin-welcome_p]:tracking-wider [&_.admin-welcome_p]:text-violet-100 [&_.admin-welcome_h2]:mt-1 [&_.admin-welcome_h2]:text-2xl [&_.admin-welcome_h2]:font-extrabold [&_.admin-welcome>div>span]:mt-1 [&_.admin-welcome>div>span]:block [&_.admin-welcome>div>span]:text-sm [&_.admin-welcome>div>span]:text-violet-100 sm:[&_.admin-welcome]:flex-row sm:[&_.admin-welcome]:items-center [&_.admin-welcome_.btn-primary]:bg-white [&_.admin-welcome_.btn-primary]:text-violet-700 hover:[&_.admin-welcome_.btn-primary]:bg-violet-50 [&_.hero-strip]:mb-5 [&_.hero-strip]:rounded-2xl [&_.hero-strip]:border [&_.hero-strip]:border-violet-100 [&_.hero-strip]:bg-gradient-to-br [&_.hero-strip]:from-violet-50 [&_.hero-strip]:to-indigo-50 [&_.hero-strip]:p-5 dark:[&_.hero-strip]:border-violet-900/50 dark:[&_.hero-strip]:from-violet-950/50 dark:[&_.hero-strip]:to-slate-900 [&_.hero-strip_h2]:font-extrabold [&_.hero-strip_h2]:text-slate-900 dark:[&_.hero-strip_h2]:text-white [&_.periode]:mb-1 [&_.periode]:text-xs [&_.periode]:font-extrabold [&_.periode]:uppercase [&_.periode]:tracking-wider [&_.periode]:text-violet-600 [&_.hero-metric]:mt-4 [&_.hero-metric]:rounded-xl [&_.hero-metric]:bg-white/80 [&_.hero-metric]:p-3 [&_.hero-metric_strong]:block [&_.hero-metric_strong]:text-2xl [&_.hero-metric_strong]:font-extrabold [&_.hero-metric_strong]:text-violet-700
            [&_.stat-row]:mb-5 [&_.stat-row]:grid [&_.stat-row]:grid-cols-2 [&_.stat-row]:gap-3 xl:[&_.stat-row]:grid-cols-4 [&_.stat]:rounded-2xl [&_.stat]:border [&_.stat]:border-slate-200 [&_.stat]:bg-white [&_.stat]:p-4 [&_.stat]:shadow-sm dark:[&_.stat]:border-slate-800 dark:[&_.stat]:bg-slate-900 [&_.stat_.label]:text-xs [&_.stat_.label]:font-bold [&_.stat_.label]:text-slate-500 [&_.stat_.value]:mt-1 [&_.stat_.value]:text-2xl [&_.stat_.value]:font-extrabold [&_.stat_.value]:tracking-tight [&_.stat_.value]:text-slate-900 dark:[&_.stat_.value]:text-white [&_.stat_small]:mt-1 [&_.stat_small]:block [&_.stat_small]:text-xs [&_.stat_small]:text-slate-400
            [&_.admin-stat-grid]:mb-5 [&_.admin-stat-grid]:grid [&_.admin-stat-grid]:grid-cols-1 [&_.admin-stat-grid]:gap-3 sm:[&_.admin-stat-grid]:grid-cols-2 xl:[&_.admin-stat-grid]:grid-cols-4 [&_.admin-stat-card]:flex [&_.admin-stat-card]:items-center [&_.admin-stat-card]:gap-3 [&_.admin-stat-card]:rounded-2xl [&_.admin-stat-card]:border [&_.admin-stat-card]:border-slate-200 [&_.admin-stat-card]:bg-white [&_.admin-stat-card]:p-4 [&_.admin-stat-card]:shadow-sm dark:[&_.admin-stat-card]:border-slate-800 dark:[&_.admin-stat-card]:bg-slate-900 [&_.admin-stat-card_strong]:block [&_.admin-stat-card_strong]:text-2xl [&_.admin-stat-card_strong]:font-extrabold [&_.admin-stat-card_small]:block [&_.admin-stat-card_small]:text-xs [&_.admin-stat-card_small]:text-slate-400 [&_.admin-stat-card>div>span]:text-xs [&_.admin-stat-card>div>span]:font-bold [&_.admin-stat-card>div>span]:text-slate-500 [&_.admin-stat-icon]:grid [&_.admin-stat-icon]:h-11 [&_.admin-stat-icon]:w-11 [&_.admin-stat-icon]:shrink-0 [&_.admin-stat-icon]:place-items-center [&_.admin-stat-icon]:rounded-xl [&_.admin-stat-icon_svg]:h-6 [&_.admin-stat-icon_svg]:w-6 [&_.tone-violet_.admin-stat-icon]:bg-violet-100 [&_.tone-violet_.admin-stat-icon]:text-violet-600 [&_.tone-blue_.admin-stat-icon]:bg-blue-100 [&_.tone-blue_.admin-stat-icon]:text-blue-600 [&_.tone-green_.admin-stat-icon]:bg-emerald-100 [&_.tone-green_.admin-stat-icon]:text-emerald-600 [&_.tone-amber_.admin-stat-icon]:bg-amber-100 [&_.tone-amber_.admin-stat-icon]:text-amber-600
            [&_.dashboard-filter-card]:mb-5 [&_.dashboard-filter-card]:flex [&_.dashboard-filter-card]:flex-col [&_.dashboard-filter-card]:gap-4 [&_.dashboard-filter-card]:rounded-2xl [&_.dashboard-filter-card]:border [&_.dashboard-filter-card]:border-slate-200 [&_.dashboard-filter-card]:bg-white [&_.dashboard-filter-card]:p-4 [&_.dashboard-filter-card]:shadow-sm dark:[&_.dashboard-filter-card]:border-slate-800 dark:[&_.dashboard-filter-card]:bg-slate-900 [&_.dashboard-filter-kicker]:block [&_.dashboard-filter-kicker]:text-[11px] [&_.dashboard-filter-kicker]:font-extrabold [&_.dashboard-filter-kicker]:uppercase [&_.dashboard-filter-kicker]:tracking-wider [&_.dashboard-filter-kicker]:text-violet-600 [&_.dashboard-filter-copy_strong]:mt-1 [&_.dashboard-filter-copy_strong]:block [&_.dashboard-filter-copy_strong]:text-sm [&_.dashboard-filter-copy_strong]:text-slate-800 dark:[&_.dashboard-filter-copy_strong]:text-white [&_.dashboard-filter-copy_small]:mt-1 [&_.dashboard-filter-copy_small]:block [&_.dashboard-filter-copy_small]:text-xs [&_.dashboard-filter-copy_small]:text-slate-400 [&_.dashboard-filter]:flex [&_.dashboard-filter]:flex-wrap [&_.dashboard-filter]:items-end [&_.dashboard-filter]:gap-3 [&_.dashboard-filter_.field]:mb-0 sm:[&_.dashboard-filter-card]:flex-row sm:[&_.dashboard-filter-card]:items-end sm:[&_.dashboard-filter-card]:justify-between
            [&_.dashboard-grid]:grid [&_.dashboard-grid]:gap-5 lg:[&_.dashboard-grid]:grid-cols-2 lg:[&_.dashboard-grid-wide]:grid-cols-[1.5fr_1fr] [&_.dpl-workspace-grid]:grid [&_.dpl-workspace-grid]:gap-5 lg:[&_.dpl-workspace-grid]:grid-cols-2
            [&_.quick-actions]:mb-5 [&_.quick-actions]:grid [&_.quick-actions]:grid-cols-2 [&_.quick-actions]:gap-3 sm:[&_.quick-actions]:grid-cols-3 xl:[&_.quick-actions]:grid-cols-5 [&_.quick-action]:flex [&_.quick-action]:min-h-28 [&_.quick-action]:flex-col [&_.quick-action]:items-start [&_.quick-action]:justify-between [&_.quick-action]:rounded-2xl [&_.quick-action]:border [&_.quick-action]:border-slate-200 [&_.quick-action]:bg-white [&_.quick-action]:p-4 [&_.quick-action]:text-sm [&_.quick-action]:font-extrabold [&_.quick-action]:text-slate-700 [&_.quick-action]:shadow-sm hover:[&_.quick-action]:-translate-y-0.5 hover:[&_.quick-action]:border-blue-200 hover:[&_.quick-action]:text-blue-700 dark:[&_.quick-action]:border-slate-800 dark:[&_.quick-action]:bg-slate-900 dark:[&_.quick-action]:text-slate-100 [&_.quick-action_svg]:h-7 [&_.quick-action_svg]:w-7 [&_.quick-action_svg]:rounded-xl [&_.quick-action_svg]:bg-blue-50 [&_.quick-action_svg]:p-1.5 [&_.quick-action_svg]:text-blue-600
            [&_.table-wrap]:overflow-x-auto [&_.data]:w-full [&_.data]:min-w-[600px] [&_.data]:border-separate [&_.data]:border-spacing-0 [&_.data_th]:border-b [&_.data_th]:border-slate-100 [&_.data_th]:px-3 [&_.data_th]:py-3 [&_.data_th]:text-left [&_.data_th]:text-[11px] [&_.data_th]:font-extrabold [&_.data_th]:uppercase [&_.data_th]:tracking-wide [&_.data_th]:text-slate-400 dark:[&_.data_th]:border-slate-800 [&_.data_td]:border-b [&_.data_td]:border-slate-100 [&_.data_td]:px-3 [&_.data_td]:py-3 [&_.data_td]:text-sm [&_.data_td]:text-slate-600 dark:[&_.data_td]:border-slate-800 dark:[&_.data_td]:text-slate-300 [&_.data_tbody_tr:last-child_td]:border-b-0 [&_.row-highlight_td]:bg-violet-50/60 dark:[&_.row-highlight_td]:bg-violet-950/20
            [&_.field]:mb-4 [&_.field_label]:mb-1.5 [&_.field_label]:block [&_.field_label]:text-sm [&_.field_label]:font-bold [&_.field_label]:text-slate-700 dark:[&_.field_label]:text-slate-200 [&_input]:w-full [&_input]:rounded-xl [&_input]:border [&_input]:border-slate-200 [&_input]:bg-white [&_input]:px-3 [&_input]:py-2.5 [&_input]:text-sm [&_input]:text-slate-800 [&_input]:outline-none [&_input]:transition focus:[&_input]:border-violet-400 focus:[&_input]:ring-4 focus:[&_input]:ring-violet-100 dark:[&_input]:border-slate-700 dark:[&_input]:bg-slate-800 dark:[&_input]:text-white dark:focus:[&_input]:ring-violet-950 [&_select]:w-full [&_select]:rounded-xl [&_select]:border [&_select]:border-slate-200 [&_select]:bg-white [&_select]:px-3 [&_select]:py-2.5 [&_select]:text-sm [&_select]:text-slate-800 [&_select]:outline-none focus:[&_select]:border-violet-400 focus:[&_select]:ring-4 focus:[&_select]:ring-violet-100 dark:[&_select]:border-slate-700 dark:[&_select]:bg-slate-800 dark:[&_select]:text-white [&_textarea]:w-full [&_textarea]:rounded-xl [&_textarea]:border [&_textarea]:border-slate-200 [&_textarea]:bg-white [&_textarea]:px-3 [&_textarea]:py-2.5 [&_textarea]:text-sm [&_textarea]:text-slate-800 [&_textarea]:outline-none focus:[&_textarea]:border-violet-400 focus:[&_textarea]:ring-4 focus:[&_textarea]:ring-violet-100 dark:[&_textarea]:border-slate-700 dark:[&_textarea]:bg-slate-800 dark:[&_textarea]:text-white [&_.field-hint]:mt-1 [&_.field-hint]:block [&_.field-hint]:text-xs [&_.field-hint]:text-slate-400 [&_.form-grid]:grid [&_.form-grid]:gap-x-4 md:[&_.form-grid]:grid-cols-2 [&_.form-actions]:flex [&_.form-actions]:flex-wrap [&_.form-actions]:gap-2 [&_.filter-bar]:mb-4 [&_.filter-bar]:flex [&_.filter-bar]:flex-wrap [&_.filter-bar]:items-end [&_.filter-bar]:gap-3 [&_.filter-bar_.field]:mb-0 [&_.actions]:flex [&_.actions]:flex-wrap [&_.actions]:gap-2
            [&_.info-grid]:grid [&_.info-grid]:gap-3 md:[&_.info-grid]:grid-cols-2 [&_.info-card]:rounded-xl [&_.info-card]:border [&_.info-card]:border-slate-100 [&_.info-card]:bg-slate-50 [&_.info-card]:p-4 dark:[&_.info-card]:border-slate-800 dark:[&_.info-card]:bg-slate-800/50 [&_.info-label]:mb-1 [&_.info-label]:block [&_.info-label]:text-xs [&_.info-label]:font-bold [&_.info-label]:text-slate-400 [&_.dpl-queue-item]:flex [&_.dpl-queue-item]:items-center [&_.dpl-queue-item]:justify-between [&_.dpl-queue-item]:gap-3 [&_.dpl-queue-item]:border-b [&_.dpl-queue-item]:border-slate-100 [&_.dpl-queue-item]:py-3 [&_.dpl-queue-item:last-child]:border-0 [&_.dpl-queue-item_small]:mt-0.5 [&_.dpl-queue-item_small]:block [&_.dpl-queue-item_small]:text-xs [&_.dpl-queue-item_small]:text-slate-400 [&_.dpl-group-list]:space-y-2 [&_.dpl-group-item]:flex [&_.dpl-group-item]:items-center [&_.dpl-group-item]:justify-between [&_.dpl-group-item]:gap-3 [&_.dpl-group-item]:rounded-xl [&_.dpl-group-item]:bg-slate-50 [&_.dpl-group-item]:p-3 dark:[&_.dpl-group-item]:bg-slate-800/50 [&_.dpl-group-item_small]:block [&_.dpl-group-item_small]:text-xs [&_.dpl-group-item_small]:text-slate-400
            [&_.stempel]:inline-flex [&_.stempel]:rounded-full [&_.stempel]:px-2.5 [&_.stempel]:py-1 [&_.stempel]:text-[11px] [&_.stempel]:font-extrabold [&_.stempel]:ring-1 [&_.stempel-menunggu]:bg-amber-50 [&_.stempel-menunggu]:text-amber-700 [&_.stempel-menunggu]:ring-amber-200 [&_.stempel-divalidasi]:bg-blue-50 [&_.stempel-divalidasi]:text-blue-700 [&_.stempel-divalidasi]:ring-blue-200 [&_.stempel-diterima]:bg-emerald-50 [&_.stempel-diterima]:text-emerald-700 [&_.stempel-diterima]:ring-emerald-200 [&_.stempel-ditolak]:bg-rose-50 [&_.stempel-ditolak]:text-rose-700 [&_.stempel-ditolak]:ring-rose-200 [&_.badge-count]:inline-flex [&_.badge-count]:rounded-full [&_.badge-count]:bg-violet-100 [&_.badge-count]:px-2 [&_.badge-count]:py-0.5 [&_.badge-count]:text-[11px] [&_.badge-count]:font-extrabold [&_.badge-count]:text-violet-700
            [&_.alert]:mb-4 [&_.alert]:rounded-xl [&_.alert]:border [&_.alert]:p-3 [&_.alert-danger]:border-rose-200 [&_.alert-danger]:bg-rose-50 [&_.alert-danger]:text-rose-700 [&_.alert-warning]:border-amber-200 [&_.alert-warning]:bg-amber-50 [&_.alert-warning]:text-amber-800 [&_.alert-info]:border-blue-200 [&_.alert-info]:bg-blue-50 [&_.alert-info]:text-blue-800 [&_.empty]:py-5 [&_.empty]:text-center [&_.empty]:text-sm [&_.empty]:text-slate-400 [&_.map-box]:h-72 [&_.map-box]:w-full [&_.map-box]:overflow-hidden [&_.map-box]:rounded-xl [&_.map-box-lg]:h-96 [&_.map-editor-actions]:mt-3 [&_.map-editor-actions]:flex [&_.map-editor-actions]:flex-wrap [&_.map-editor-actions]:gap-2 [&_.export-grid]:grid [&_.export-grid]:gap-3 md:[&_.export-grid]:grid-cols-3 [&_.export-item]:rounded-xl [&_.export-item]:border [&_.export-item]:border-slate-100 [&_.export-item]:p-4 dark:[&_.export-item]:border-slate-800 [&_.export-links]:mt-3 [&_.export-links]:flex [&_.export-links]:flex-col [&_.export-links]:gap-2 [&_.star-icon]:inline-block [&_.star-icon]:h-4 [&_.star-icon]:w-4 [&_.star-icon]:fill-slate-200 [&_.star-icon]:text-slate-200 [&_.star-icon.filled]:fill-amber-400 [&_.star-icon.filled]:text-amber-400 [&_.star-rating]:flex [&_.star-rating]:items-center [&_.star-rating]:gap-1 [&_.star-label]:cursor-pointer [&_.star-label]:rounded-md [&_.star-label]:p-1 hover:[&_.star-label]:bg-amber-50 [&_.font-mono]:font-mono" >
            <?= view('partials/flash') ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<nav class="fixed inset-x-0 bottom-0 z-40 grid grid-flow-col auto-cols-fr border-t border-slate-200 bg-white px-1 py-2 shadow-[0_-8px_24px_rgba(15,23,42,.06)] lg:hidden dark:border-slate-800 dark:bg-slate-900">
    <?php foreach (array_slice($mobileMenus, 0, 4) as $menu): ?>
        <?php $active = str_starts_with('/' . $uri, rtrim($menu['url'], '/')); ?>
        <a class="flex flex-col items-center gap-1 rounded-lg py-1 text-[10px] font-extrabold <?= $active ? 'text-violet-600' : 'text-slate-400 dark:text-slate-500' ?>" href="<?= esc($menu['url']) ?>"><span class="h-5 w-5"><?= $iconSvg($menu['icon'] ?? 'home') ?></span><?= esc(explode(' ', $menu['label'])[0]) ?></a>
    <?php endforeach; ?>
    <a class="flex flex-col items-center gap-1 rounded-lg py-1 text-[10px] font-extrabold <?= str_starts_with('/' . $uri, rtrim($profilUrl, '/')) ? 'text-violet-600' : 'text-slate-400 dark:text-slate-500' ?>" href="<?= esc($profilUrl) ?>"><span class="h-5 w-5"><?= $iconSvg('user') ?></span>Profil</a>
</nav>

<div id="toast" class="toast hidden fixed bottom-24 right-4 z-50 flex w-[min(24rem,calc(100vw-2rem))] items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900" role="status" aria-live="polite" aria-atomic="true">
    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-violet-100 font-extrabold text-violet-700" id="toast-icon" aria-hidden="true">i</div><div class="min-w-0 flex-1"><strong class="block text-sm text-slate-900 dark:text-white" id="toast-title">Notifikasi</strong><p class="mt-0.5 text-sm text-slate-500" id="toast-message"></p></div><button type="button" class="text-slate-400 hover:text-slate-700" id="toast-close" aria-label="Tutup notifikasi">&times;</button>
</div>

<div id="ui-confirm" class="hidden fixed inset-0 z-50 grid place-items-center p-4" role="dialog" aria-modal="true" aria-labelledby="ui-confirm-title" aria-describedby="ui-confirm-message">
    <div class="absolute inset-0 bg-slate-950/45 backdrop-blur-sm" data-confirm-cancel></div><div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900"><div class="mb-4 grid h-11 w-11 place-items-center rounded-xl bg-rose-100 text-xl font-extrabold text-rose-600">!</div><p class="text-xs font-extrabold uppercase tracking-wider text-violet-600">Konfirmasi tindakan</p><h2 class="mt-1 text-xl font-extrabold text-slate-900 dark:text-white" id="ui-confirm-title">Lanjutkan tindakan ini?</h2><p class="mt-2 text-sm text-slate-500" id="ui-confirm-message"></p><div class="mt-6 flex justify-end gap-2"><button type="button" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-extrabold text-slate-700" data-confirm-cancel>Batal</button><button type="button" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-extrabold text-white" id="ui-confirm-submit">Ya, lanjutkan</button></div></div>
</div>

<script>
  window.KKN = { csrf: <?= json_encode(csrf_hash()) ?>, csrfName: <?= json_encode(csrf_token()) ?>, userId: <?= (int) ($user['id'] ?? 0) ?>, pusherKey: <?= json_encode($pusherKey ?? '') ?>, pusherCluster: <?= json_encode($pusherCluster ?? 'ap1') ?>, pusherEnabled: <?= json_encode(! empty($pusherEnabled)) ?>, notifReadUrl: <?= json_encode(site_url('notifikasi')) ?> };
</script>
<script src="<?= base_url('assets/js/app.js') ?>?v=<?= (int) (@filemtime(FCPATH . 'assets/js/app.js') ?: 1) ?>" defer></script>
<?php if (! empty($pusherEnabled) && ! empty($pusherKey)): ?><script src="https://js.pusher.com/8.2.0/pusher.min.js"></script><?php endif; ?>
</body>
</html>
