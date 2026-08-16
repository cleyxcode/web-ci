# Key page dependency trees

## `/admin/dashboard` — command dashboard

Entry: `app/app/Views/admin/dashboard.php`

Dependencies:
- `app/app/Views/layouts/panel.php`
- `app/app/Views/partials/flash.php`
- `app/public/assets/css/app.css`
- Chart.js (remote)

Renders welcome/action area, four summary cards, a location-activity chart,
activity-status donut, and recent operational data.

## `/dpl/dashboard` — supervisor workspace

Entry: `app/app/Views/dpl/dashboard.php`

Dependencies:
- `app/app/Views/layouts/panel.php`
- `app/app/Views/partials/logbook-filter.php`
- `app/app/Views/partials/map.php` (when location exists)
- `app/app/Views/partials/flash.php`
- `app/public/assets/css/app.css`

Renders supervised-student metrics, validation/review queues, group progress,
and GPS-aware location summary.

## `/mahasiswa/dashboard` — student field workspace

Entry: `app/app/Views/mahasiswa/dashboard.php`

Dependencies:
- `app/app/Views/layouts/panel.php`
- `app/app/Views/partials/icon.php`
- `app/app/Views/partials/logbook-filter.php`
- `app/app/Views/partials/map.php` (when location exists)
- `app/app/Views/partials/flash.php`
- `app/public/assets/css/app.css`

Renders the current KKN group/progress, summary stats, quick actions, recent
logbook table, and announcements.
