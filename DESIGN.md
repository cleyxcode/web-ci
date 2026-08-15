# Design System: Sistem Monitoring KKN Tematik UKIM
**Project ID:** kkn-tematik-ukim-2026
**Direction:** Field Command Center — pusat kendali lapangan yang terasa seperti logbook akademik digital, dengan peta sebagai sumber kebenaran untuk lokasi.

## 1. Visual Theme & Atmosphere
Utilitarian-academic dengan nuansa lapangan Maluku — hangat seperti kertas logbook, terarah seperti papan komando, dan tetap tenang saat dipakai di luar ruangan. Dashboard memprioritaskan tindakan berikutnya: validasi DPL, progres mahasiswa, dan titik GPS kelompok. Peta adalah signature element: lokasi dipilih melalui klik peta/GPS, bukan koordinat yang diketik.

Depth strategy: **quiet layered surfaces** — border rgba untuk struktur, surface shift untuk hierarki, dan shadow sangat lembut hanya untuk mengangkat kartu dari kanvas.

## 2. Color Palette & Roles
* **Kertas Lapangan** (#F7F4EF) — Background canvas utama, hangat seperti kertas logbook.
* **Kapas Gading** (#FDFBF7) — Surface elevated untuk kartu, dropdown, dan modal.
* **Biru Laut Maluku** (#1B6B8A) — Primary action, link aktif, header accent, focus ring.
* **Biru Laut Gelap** (#134E66) — Hover state primary, teks heading accent.
* **Abu Karang** (#6B6560) — Teks sekunder dan metadata.
* **Tinta Arsip** (#2C2825) — Teks primary, headline.
* **Tinta Redup** (#9A948C) — Teks tertiary, placeholder.
* **Hijau Kelapa** (#2D7A4F) — Success: divalidasi, diterima, grade A/B.
* **Kuning Senja** (#C4920A) — Warning: menunggu validasi/review.
* **Laterit Merah** (#B83232) — Danger: ditolak, error, badge antrian urgent.
* **Border Lembut** (rgba(44,40,37,0.08)) — Separasi standar antar section.
* **Border Medium** (rgba(44,40,37,0.14)) — Card outline dan sidebar divider.
* **Kabut Laut** (rgba(27,107,138,0.055)) — Tint untuk konteks aktif, status live, dan fokus navigasi.

## 3. Typography Rules
* **Display & Headings:** "DM Sans" — geometric-humanist, jelas di lapangan, weight 600–700, tracking heading sedikit rapat.
* **Body:** "DM Sans" weight 400–500, line-height 1.6 untuk teks panjang logbook.
* **Data & NPM/NIDN:** "JetBrains Mono" tabular — angka grade, OTP, statistik dashboard.
* **Label/Nav:** DM Sans 500, uppercase tracking 0.04em untuk menu sidebar, size 12–13px.

## 4. Component Stylings
* **Buttons:** Subtly rounded corners (10px). Primary = Biru Laut Maluku fill; secondary = transparent + border. Touch target minimum 44px di layar kecil. Hover darken 8%. Disabled opacity 0.5.
* **Stempel Validasi (Signature):** Badge status berbentuk stempel — border dashed 1.5px, slight rotation (-2deg), uppercase mono label. Menunggu = kuning senja; Divalidasi/Diterima = hijau kelapa; Ditolak = laterit merah.
* **Cards/Containers:** Kapas Gading background, border medium, radius 16px, padding fluid 18–26px, shadow 0 14px 34px rgba(44,40,37,0.055). Card tidak memakai shadow dramatis.
* **Inputs/Forms:** Inset surface (#EDE9E3), border lembut, radius 8px. Focus: ring 2px biru laut.
* **Sidebar:** Canvas yang sama dengan efek kaca tipis, border-right medium. Active item = left bar 2px biru laut + subtle bg tint. Di bawah 768px berubah menjadi bottom navigation yang dapat digeser horizontal.
* **Stat Cards:** Horizontal layout — label kecil di atas, angka mono besar, tanpa icon circle generik.
* **OTP Input:** 6 kotak 48×48px, mono bold, border medium, auto-focus chain.

## 5. Layout Principles
* Base spacing unit: **4px** (multiples: 8, 12, 16, 24, 32, 48).
* Sidebar width: 264px fixed; main content fluid dengan padding 18–32px.
* **Map picker:** peta memiliki tinggi fluid, tombol “Gunakan lokasi saya” dan “Hapus titik”, dengan latitude/longitude tersimpan sebagai field tersembunyi.
* Dashboard DPL: hero = antrian validasi + metrik “item perlu tindakan”; workspace dua kolom di desktop dan satu kolom di mobile.
* Dashboard Mahasiswa: hero = strip periode KKN + progress bar; kartu profil menyediakan lokasi KKN berbasis peta.
* Responsive data: tabel DPL berubah menjadi kartu berlabel pada layar kecil, bukan memaksa scroll horizontal.
* Whitespace: base spacing 4px, section gap 18–32px, density lebih tinggi hanya pada tabel dan antrian validasi.

## 6. Signature & Defaults Replaced
* **Signature:** “stempel validasi” dan field map — status memiliki bentuk stempel dashed yang mudah dipindai, sementara lokasi selalu punya konteks visual di atas peta.
* **Stat cards generik** → stat cards berwarna semantik dengan garis aksen atas dan angka mono.
* **Sidebar terpisah dari produk** → sidebar satu dunia warna dengan kanvas, hanya dipisahkan oleh border dan active rail.
* **Input koordinat manual** → map picker dengan klik/GPS; angka koordinat hanya menjadi data teknis tersembunyi.
