# KKN Tematik UKIM — Interface System

## Direction & Feel
Field Command Center: utilitarian-academic dengan nuansa lapangan Maluku. Hangat seperti logbook, terarah seperti papan komando, dan peta menjadi sumber kebenaran lokasi.

## Depth Strategy
Quiet layered surfaces. Border rgba tetap menjadi struktur utama; kartu memakai shadow sangat lembut untuk terangkat dari kanvas, tanpa shadow dramatis.

## Spacing
Base unit: 4px. Scale: 8, 12, 16, 24, 32, 48.

## Palette
- Canvas: #F7F4EF (kertas-lapangan)
- Surface: #FDFBF7 (kapas-gading)
- Primary: #1B6B8A (biru-laut)
- Ink: #2C2825, secondary #6B6560, muted #9A948C
- Success: #2D7A4F, Warning: #C4920A, Danger: #B83232
- Border: rgba(44,40,37,0.08) / 0.14

## Typography
- DM Sans (UI + headings)
- JetBrains Mono (data, OTP, grades)

## Signature: Stempel Validasi
Status badges use dashed border, slight rotation, uppercase mono. Classes: `.stempel`, `.stempel-menunggu`, `.stempel-divalidasi`, `.stempel-ditolak`, `.stempel-diterima`.

## Components
- Sidebar: same world as canvas, 264px, border-right, active rail; collapses to horizontal bottom nav below 768px
- Buttons: 10px radius, minimum 42px, primary fill biru-laut
- Cards: 16px radius, kapas-gading, border rgba + whisper-soft shadow
- Inputs: inset #EDE9E3, 10px radius, focus ring biru-laut
- Map picker: Leaflet map with click/GPS controls; coordinate inputs are hidden implementation fields

## Role Layouts
- auth.php — centered card login/OTP
- panel.php — shared admin/dpl/mahasiswa shell
- dpl dashboard — validation queue hero, action metric, responsive card-based tables
- mahasiswa profile/tim — location card and map picker instead of manual coordinate allocation
