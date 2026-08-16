# Extractable components

## PanelShell
- Source: `app/app/Views/layouts/panel.php`
- Category: layout
- Description: Role-aware sidebar, header, mobile navigation, notification UI, toast, and confirm dialog.
- Extractable props: `role`, `activeItem`, `userName`, `unreadCount`, `menuItems`.
- Hardcoded: KKN Tematik brand treatment, icon names, desktop/mobile breakpoint behavior.

## RoleSidebar
- Source: `app/app/Views/layouts/panel.php`
- Category: layout
- Description: Brand, role-specific navigation, profile affordance, and logout action.
- Extractable props: `role`, `activeItem`, `userName`, `menuItems`.
- Hardcoded: Brand label and icon treatment.

## TopBar
- Source: `app/app/Views/layouts/panel.php`
- Category: layout
- Description: Page title, notification control, theme toggle, and current-user link.
- Extractable props: `pageTitle`, `userName`, `role`, `unreadCount`.
- Hardcoded: control icon set and university label.

## SummaryStatCard
- Source: `app/app/Views/admin/dashboard.php`, `app/app/Views/dpl/dashboard.php`, `app/app/Views/mahasiswa/dashboard.php`
- Category: basic
- Description: Colored compact statistic card with an icon, label, value, and support text.
- Extractable props: `tone`, `icon`, `label`, `value`, `caption`.
- Hardcoded: individual business labels.

## StatusBadge
- Source: `app/public/assets/css/app.css`
- Category: basic
- Description: Semantic workflow label used in tables and validation queues.
- Extractable props: `status`.
- Hardcoded: visual mapping for sent, reviewed, accepted, rejected, and pending states.
