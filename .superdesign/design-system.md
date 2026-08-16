# KKN Tematik Monitoring — UI Migration System

## Product context

KKN Tematik Monitoring is a field-operation system for three roles: **Admin**
manages people, KKN groups, places, announcements and reports; **DPL** validates
student work and monitors assigned groups; **Mahasiswa** records logbooks,
uploads reports, sees evaluation/grades and manages team GPS. The UI is used for
routine academic operations, therefore must be clear, compact, reliable and
readable outdoors.

## Approved visual source

The four user-provided images in `app/ui-referensse/` are the source of truth:
an airy desktop dashboard with a slim gradient sidebar, bright statistic cards,
light gray workspace, rounded white panels, small Lucide-style outline icons,
and a responsive mobile shell. Adapt their visual language to every existing
screen; retain this application's real labels, routes, controls and data.

## Visual tokens

| Token | Value |
| --- | --- |
| Workspace | `#F7F8FC` |
| Surface | `#FFFFFF` |
| Ink | `#172033` |
| Muted | `#687385` |
| Line | `#E8ECF3` |
| Violet | `#6D3FD2` |
| Blue | `#2989F3` |
| Emerald | `#11B981` |
| Amber | `#F5A623` |
| Coral | `#F2594B` |
| Admin sidebar | `linear-gradient(180deg,#3B248A 0%,#633CCB 100%)` |
| Mahasiswa sidebar | `linear-gradient(180deg,#0E74C9 0%,#0758A9 100%)` |
| DPL sidebar | `linear-gradient(180deg,#0B9D83 0%,#057760 100%)` |

## Type, spacing and shape

- Font: **Nunito Sans** for all UI text; `ui-monospace` only for codes and
  numeric data. Use the Google Fonts CDN.
- Type scale: 12/14/16px body, 20–24px section titles, 26–30px page titles;
  headings 700–800 weight.
- Space: 4px base; common gaps 8, 12, 16, 20, 24, 32.
- Surface: 12–16px radius, a 1px `#E8ECF3` border, and restrained
  `0 4px 18px rgba(22, 34, 59, .055)` shadow.
- Icon blocks: 42–48px rounded 12px tile, low-opacity same-color surface,
  saturated icon foreground.

## Layout rules

- Desktop: 260px fixed left sidebar; white topbar, 72px tall; workspace has
  28–32px padding; tables and charts use white cards.
- Sidebar: brand at top, profile identity below it, nav items with 10–12px
  padding and a translucent white active pill. Avoid dark borders.
- Mobile (< 768px): hide desktop sidebar; topbar becomes compact; show a fixed
  white bottom nav with four or five primary destinations. Content leaves room
  for the bottom nav.
- Dashboard: action/summary row first, responsive 4-col stat grid (2 cols on
  mobile), then charts/queue/table cards. Do not use decorative hero gradients
  in the content canvas.
- Forms and lists: preserve every field, action, status, route and table data;
  improve scanability with 44px touch targets, clear labels, visible focus
  rings, and horizontal overflow only as a last resort.

## Role mapping

- **Admin** uses violet navigation with student/DPL/location/activity overview.
- **DPL** uses emerald navigation with pending validation/review queue emphasis.
- **Mahasiswa** uses blue navigation with KKN period, progress and quick actions
  emphasis.

## Motion and accessibility

- Use a 160–220ms ease-out transition for navigation, buttons and surfaces;
  honour `prefers-reduced-motion`.
- Use native buttons/links, semantic tables/forms, distinct keyboard focus,
  contrast-safe text, labels for icon-only controls, and no color-only status.

## Technical constraints

- Tailwind must use the online Play CDN (`https://cdn.tailwindcss.com`) because
  there is no Node build pipeline.
- Use Iconify's online web component for new standard icons; retain SVGs where
  existing JavaScript requires them.
- Do not alter PHP controllers, route definitions, inputs, form names, IDs,
  JavaScript hooks or user-facing behavior. This migration is presentation only.
