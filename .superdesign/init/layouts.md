# Shared layouts

## `app/app/Views/layouts/panel.php` — authenticated application shell

The shell creates a role-aware desktop sidebar, responsive mobile navigation,
top bar, notification panel, toast, and confirmation dialog. It receives
`$title`, `$content`, `$user`, `$menus`, `$notifikasiAll`, and `$unreadCount`.
It renders every admin, DPL, and mahasiswa page.

Key render structure:

```php
<div class="panel">
  <aside class="sidebar">...brand, role menu, current user, logout...</aside>
  <div class="main">
    <header class="topbar">...title, theme switch, notification, profile...</header>
    <main class="content"><?= view('partials/flash') ?><?= $content ?? '' ?></main>
  </div>
</div>
<nav class="mobile-nav">...role menu, profile, logout...</nav>
<div id="toast" class="toast hidden">...</div>
<div id="ui-confirm" class="ui-modal hidden">...</div>
```

Its inline `$iconSvg` function supplies the current navigation icon set
(`home`, `users`, `academic`, `group`, `map`, `doc`, `bell`, `eye`, `check`,
`star`, `book`, `upload`, `chat`, `user`, `moon`, `sun`, `chart`, `download`,
`clipboard`, `history`, `settings`, `logout`). The layout includes Chart.js and
Leaflet and loads `assets/css/app.css` plus `assets/js/app.js`.

## `app/app/Views/layouts/auth.php` — authentication shell

```php
<!DOCTYPE html>
<html lang="id"><head>...Google fonts and assets/css/app.css...</head>
<body>
  <main class="auth-shell"><section class="auth-card"><?= $content ?? '' ?></section></main>
  <script src="<?= base_url('assets/js/app.js') ?>" defer></script>
</body></html>
```

The authenticated redesign must not alter the `panel.php` JavaScript IDs:
`theme-toggle`, `notif-btn`, `notif-panel`, `notif-read-all`, `notif-list`,
`toast`, and `ui-confirm`.
