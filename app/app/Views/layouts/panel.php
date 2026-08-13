<?php
$uri = uri_string();
$user = $user ?? current_user();
$menus = $menus ?? panel_menus($user['role'] ?? '');
$iconSvg = static function (string $name): string {
    $icons = [
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-10.5z"/>',
        'users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm13 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'academic' => '<path stroke-linecap="round" stroke-linejoin="round" d="M22 10 12 5 2 10l10 5 10-5zm0 0v6M6 12.5V17c0 1.5 2.7 3 6 3s6-1.5 6-3v-4.5"/>',
        'group' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm14 10v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'map' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5-2V4l5 2 6-2 5 2v14l-5-2-6 2zM9 6v14M15 4v14"/>',
        'doc' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm0 0v6h6M8 13h8M8 17h5"/>',
        'bell' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/>',
        'eye' => '<path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>',
        'check' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>',
        'star' => '<path stroke-linecap="round" stroke-linejoin="round" d="m12 2 3.09 6.26L22 9.27l-5 4.87L18.18 22 12 18.56 5.82 22 7 14.14l-5-4.87 6.91-1.01L12 2z"/>',
        'book' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 4v15.5A2.5 2.5 0 0 1 6.5 17H20V4H6.5A2.5 2.5 0 0 0 4 6.5"/>',
        'upload' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/>',
        'chat' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>',
        'user' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>',
        'moon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>',
        'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5M4 19h16M8 17V10M12 17V7M16 17v-4"/>',
        'download' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/>',
        'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>',
    ];
    $path = $icons[$name] ?? $icons['home'];

    return '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">' . $path . '</svg>';
};
$mobileMenus = array_values(array_filter($menus, static fn ($m) => ! empty($m['mobile'])));
if ($mobileMenus === []) {
    $mobileMenus = array_slice($menus, 0, 5);
}
$initial = strtoupper(mb_substr($user['nama'] ?? 'U', 0, 1));
$unread = (int) ($unreadCount ?? count($notifikasi ?? []));
$profilUrl = match ($user['role'] ?? '') {
    'admin' => site_url('admin/profil'),
    'mahasiswa' => site_url('mahasiswa/profil'),
    default => '#',
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Panel') ?> — KKN Tematik UKIM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" defer></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
    <script>
      (function () {
        const t = localStorage.getItem('kkn-theme');
        if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.setAttribute('data-theme', 'dark');
        }
      })();
    </script>
</head>
<body>
<div class="panel">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <a class="mark" href="<?= esc($menus[0]['url'] ?? '/') ?>">
                <span>UK</span>
                KKN Tematik UKIM
            </a>
            <small>Monitoring lapangan</small>
        </div>
        <div class="nav-label">Menu</div>
        <nav>
            <?php foreach ($menus as $menu): ?>
                <?php $active = str_starts_with('/' . $uri, rtrim($menu['url'], '/')) || $uri === ltrim($menu['url'], '/'); ?>
                <a class="nav-item <?= $active ? 'active' : '' ?>" href="<?= esc($menu['url']) ?>">
                    <?= $iconSvg($menu['icon'] ?? 'home') ?>
                    <?= esc($menu['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= esc($profilUrl) ?>" class="user-chip" style="text-decoration:none;color:inherit">
                <div class="avatar"><?= esc($initial) ?></div>
                <div class="meta">
                    <strong><?= esc($user['nama'] ?? '') ?></strong>
                    <span><?= esc($user['role'] ?? '') ?></span>
                </div>
            </a>
            <a class="btn btn-secondary btn-sm" href="<?= site_url('logout') ?>" style="width:100%;margin-top:10px;text-align:center">Logout</a>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div>
                <h1><?= esc($title ?? '') ?></h1>
                <p class="sub">Fakultas Ilmu Komputer · UKIM</p>
            </div>
            <div class="topbar-actions">
                <button type="button" class="theme-toggle" id="theme-toggle" title="Mode gelap/terang" aria-label="Toggle dark mode">
                    <span class="icon-sun"><?= $iconSvg('sun') ?></span>
                    <span class="icon-moon"><?= $iconSvg('moon') ?></span>
                </button>
                <div class="notif-wrap" id="notif-wrap">
                    <button type="button" class="notif-btn" id="notif-btn" title="Notifikasi" aria-label="Notifikasi" aria-expanded="false">
                        <?= $iconSvg('bell') ?>
                        <?php if ($unread > 0): ?><span class="dot" id="notif-dot"></span><?php endif; ?>
                    </button>
                    <div class="notif-panel" id="notif-panel" hidden>
                        <div class="notif-panel-head">
                            <strong>Notifikasi</strong>
                            <?php if ($unread > 0): ?>
                                <button type="button" class="btn-link" id="notif-read-all">Tandai dibaca</button>
                            <?php endif; ?>
                        </div>
                        <div class="notif-panel-body" id="notif-list">
                            <?php if (empty($notifikasiAll)): ?>
                                <p class="notif-empty">Belum ada notifikasi</p>
                            <?php else: ?>
                                <?php foreach ($notifikasiAll as $n): ?>
                                    <div class="notif-item <?= $n['is_read'] ? '' : 'unread' ?> notif-type-<?= esc($n['type']) ?>" data-id="<?= (int) $n['id'] ?>">
                                        <strong><?= esc($n['judul']) ?></strong>
                                        <p><?= esc(mb_strimwidth($n['pesan'], 0, 80, '…')) ?></p>
                                        <small><?= format_tanggal($n['created_at'] ?? null) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="content">
            <?= view('partials/flash') ?>
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<nav class="mobile-nav">
    <?php foreach ($mobileMenus as $menu): ?>
        <?php $active = str_starts_with('/' . $uri, rtrim($menu['url'], '/')); ?>
        <a class="<?= $active ? 'active' : '' ?>" href="<?= esc($menu['url']) ?>">
            <?= $iconSvg($menu['icon'] ?? 'home') ?>
            <?= esc(explode(' ', $menu['label'])[0]) ?>
        </a>
    <?php endforeach; ?>
</nav>

<div id="toast" class="toast hidden" role="status" aria-live="polite">
    <span id="toast-message"></span>
</div>

<script>
  window.KKN = {
    csrf: <?= json_encode(csrf_hash()) ?>,
    csrfName: <?= json_encode(csrf_token()) ?>,
    userId: <?= (int) ($user['id'] ?? 0) ?>,
    pusherKey: <?= json_encode($pusherKey ?? '') ?>,
    pusherCluster: <?= json_encode($pusherCluster ?? 'ap1') ?>,
    pusherEnabled: <?= json_encode(! empty($pusherEnabled)) ?>,
    notifReadUrl: <?= json_encode(site_url('notifikasi')) ?>,
  };
</script>
<script src="<?= base_url('assets/js/app.js') ?>" defer></script>
<?php if (! empty($pusherEnabled) && ! empty($pusherKey)): ?>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<?php endif; ?>
</body>
</html>
