# Theme tokens

## Compact summary

The existing system uses an academic field-logbook theme: `DM Sans` for UI,
`JetBrains Mono` for data, a warm paper background (`#F7F4EF`), ivory surface
(`#FDFBF7`), deep sea-blue primary (`#1B6B8A`), coconut-green success
(`#2D7A4F`), sunset-yellow warning (`#C4920A`), and laterite-red danger
(`#B83232`). Base spacing is 4px, buttons are 8px radius, cards 12px radius,
and desktop sidebar is 240px. The current UI is custom CSS, not Tailwind.

## Migration direction

Use Tailwind Play CDN online with a compact configuration. Retain the reference
application's visual grammar: clean white workspace, rounded cards, colored
stat icons, 260px gradient role sidebar, 64px white header, soft gray page
canvas, and responsive bottom navigation. Role gradients: admin violet,
mahasiswa blue, DPL emerald. Keep status colors semantically stable.

## Raw source

The full current CSS source is `app/public/assets/css/app.css` (3,556 lines).
It is intentionally not copied here due to size; its selectors are the runtime
compatibility contract until each view is migrated to Tailwind utilities.
