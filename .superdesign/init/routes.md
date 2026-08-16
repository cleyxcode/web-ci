# Route map

Routing is config-based in `app/app/Config/Routes.php`; all protected pages use
the shared `layouts/panel` shell via the controller's render helper.

| Area | Paths | Main views | Layout |
| --- | --- | --- | --- |
| Admin | `/admin`, `/admin/dashboard`, `/admin/mahasiswa`, `/admin/dpl`, `/admin/kkn`, `/admin/lokasi`, `/admin/laporan`, `/admin/evaluasi`, `/admin/pengumuman`, `/admin/audit`, `/admin/profil` | `Views/admin/**` | `layouts/panel` |
| DPL | `/dpl`, `/dpl/dashboard`, `/dpl/monitoring`, `/dpl/logbook`, `/dpl/laporan`, `/dpl/penilaian`, `/dpl/evaluasi`, `/dpl/export`, `/dpl/profil` | `Views/dpl/**` | `layouts/panel` |
| Mahasiswa | `/mahasiswa`, `/mahasiswa/dashboard`, `/mahasiswa/logbook`, `/mahasiswa/laporan`, `/mahasiswa/nilai`, `/mahasiswa/evaluasi`, `/mahasiswa/tim`, `/mahasiswa/profil` | `Views/mahasiswa/**` | `layouts/panel` |
| Auth | `/login`, `/forgot-password`, `/otp-verify`, `/reset-password` | `Views/auth/**` | `layouts/auth` |

`app/app/Config/Routes.php` is the complete route source. Route changes are
out of scope for this UI migration.
