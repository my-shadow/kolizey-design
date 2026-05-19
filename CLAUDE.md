# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Landing page + admin panel for **Дизайн-студія Колізей** — a print/design center with internet café.
Local dev URL: `http://design.loc`

## Stack

- **Backend:** Pure PHP 7.4 (avoid PHP 8+ functions like `str_contains`, `str_starts_with`, `match` expressions)
- **Data:** Flat-file JSON (`data.json`) — no database
- **Frontend:** Tailwind CSS (CDN), Font Awesome 6.5 (CDN), vanilla JS
- **Auth:** PHP sessions, single admin password stored in `data.json`
- **No build step** — files are served directly by Apache (OSPanel)

## File Structure

```
index.php      — Landing page (all sections in one file)
admin.php      — Admin panel (login + 2 tabs: Контент / Налаштування)
data.json      — Settings store (no bookings, no requests)
.htaccess      — Removes .php extension from URLs
photos/        — Gallery images (photo_2.jpg … photo_10.jpg)
source-files/  — Original uploaded assets (not served)
```

## Key Patterns

### Data access
```php
$data = json_decode(file_get_contents('data.json'), true);
$s = $data['settings'];
function e($v)        { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
function sv($k, $d='') { global $s; return htmlspecialchars($s[$k] ?? $d, ENT_QUOTES, 'UTF-8'); }
```

### Saving data
```php
file_put_contents('data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
```

### Phone → messenger links
Phones stored as `096 890 40 55` (without country code).
International format: `+38` . `preg_replace('/\D/', '', $phone)`
- Telegram: `https://t.me/+380...`
- Viber: `viber://chat?number=%2B380...`
- WhatsApp: `https://wa.me/380...` (no `+`)

## Admin Panel

URL: `/admin`
Default password: `kolisey`

**Tab 1 — Контент:** SEO/meta (with SERP preview), hero title/desc, promo ticker, all service prices, internet café section, footer text.
**Tab 2 — Налаштування:** Business name, address, phones, Google Analytics ID, admin password.

No bookings, no Telegram bot — contact is handled via messenger/call links on the frontend.

## Services & Price Keys

| Price key | Service |
|---|---|
| `price_copy_bw` / `price_copy_color` | Ксерокопія |
| `price_lam_a4` / `price_lam_a3` | Ламінування |
| `price_scan` | Сканування |
| `price_id_photo` | Фото на документи |
| `price_photo_a6` / `price_photo_a4` / `price_photo_a3` | Друк фото |
| `price_mug` | Друк на чашках |
| `price_tshirt` | Друк на футболках |
| `price_frame` | Фото в рамці |
| `price_internet_hour` | Інтернет-кафе (порожнє = не відображати) |

## Photos

`photos/photo_2.jpg` — used as hero image
`photos/photo_10.jpg` — used as internet café section image
`photos/photo_3–9.jpg` — gallery (CSS infinite-scroll, two rows)

## Language & Locale

All UI is in Ukrainian. Timezone: `Europe/Kyiv`.
