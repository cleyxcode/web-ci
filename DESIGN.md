# Design System: Sistem Monitoring KKN Tematik UKIM
**Project ID:** kkn-tematik-ukim-2026

## 1. Visual Theme & Atmosphere
Utilitarian-academic dengan nuansa lapangan Maluku — tenang, padat-informatif, dan mudah dibaca di layar kecil maupun desktop. Permukaan hangat seperti kertas logbook lapangan; struktur abu karang; aksen biru laut UKIM. Depth strategy: **borders-only** — tanpa shadow tebal, hierarki lewat perbedaan surface tipis dan border rgba.

## 2. Color Palette & Roles
* **Kertas Lapangan** (#F7F4EF) — Background canvas utama, hangat seperti kertas logbook.
* **Kapas Gading** (#FDFBF7) — Surface elevated (card, dropdown, modal).
* **Biru Laut Maluku** (#1B6B8A) — Primary action, link aktif, header accent, focus ring.
* **Biru Laut Gelap** (#134E66) — Hover state primary, teks heading accent.
* **Abu Karang** (#6B6560) — Teks sekunder dan metadata.
* **Tinta Arsip** (#2C2825) — Teks primary, headline.
* **Tinta Redup** (#9A948C) — Teks tertiary, placeholder.
* **Hijau Kelapa** (#2D7A4F) — Success: divalidasi, diterima, grade A/B.
* **Kuning Senja** (#C4920A) — Warning: menunggu validasi/review.
* **Laterit Merah** (#B83232) — Danger: ditolak, error, badge antrian urgent.
* **Border Lembut** (rgba(44,40,37,0.08)) — Separasi standar antar section.
* **Border Medium** (rgba(44,40,37,0.14)) — Card outline, sidebar divider.

## 3. Typography Rules
* **Display & Headings:** "DM Sans" — geometric-humanist, jelas di lapangan, weight 600–700.
* **Body:** "DM Sans" weight 400–500, line-height 1.6 untuk teks panjang logbook.
* **Data & NPM/NIDN:** "JetBrains Mono" tabular — angka grade, OTP, statistik dashboard.
* **Label/Nav:** DM Sans 500, uppercase tracking 0.04em untuk menu sidebar, size 12–13px.

## 4. Component Stylings
* **Buttons:** Subtly rounded corners (8px). Primary = Biru Laut Maluku fill; secondary = transparent + border. Hover darken 8%. Disabled opacity 0.5.
* **Stempel Validasi (Signature):** Badge status berbentuk stempel — border dashed 1.5px, slight rotation (-2deg), uppercase mono label. Menunggu = kuning senja; Divalidasi/Diterima = hijau kelapa; Ditolak = laterit merah.
* **Cards/Containers:** Kapas Gading background, border medium, radius 12px, padding 24px. No drop shadow.
* **Inputs/Forms:** Inset surface (#EDE9E3), border lembut, radius 8px. Focus: ring 2px biru laut.
* **Sidebar:** Same background as canvas (Kertas Lapangan), border-right medium. Active item = left bar 3px biru laut + subtle bg tint.
* **Stat Cards:** Horizontal layout — label kecil di atas, angka mono besar, tanpa icon circle generik.
* **OTP Input:** 6 kotak 48×48px, mono bold, border medium, auto-focus chain.

## 5. Layout Principles
* Base spacing unit: **4px** (multiples: 8, 12, 16, 24, 32, 48).
* Sidebar width: 240px fixed; main content max-width fluid dengan padding 24–32px.
* Dashboard DPL: hero = antrian validasi (bukan stat grid generik).
* Dashboard Mahasiswa: hero = strip periode KKN + progress bar.
* Mobile-first: sidebar collapses to bottom nav on <768px.
* Whitespace cukup antar section (32px) tapi density tinggi di tabel data.
