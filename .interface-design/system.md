# KKN Tematik UKIM — Interface System

## Direction & Feel
Utilitarian-academic, nuansa lapangan Maluku. Tenang, padat-informatif, borders-only depth.

## Depth Strategy
Borders-only. No drop shadows. Surface elevation via lightness shift only.

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
- Sidebar: same bg as canvas, 240px, border-right
- Buttons: 8px radius, primary fill biru-laut
- Cards: 12px radius, kapas-gading, border only
- Inputs: inset #EDE9E3, 8px radius

## Role Layouts
- auth.php — centered card login/OTP
- panel.php — shared admin/dpl/mahasiswa shell
