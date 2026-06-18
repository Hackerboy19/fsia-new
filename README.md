# Forever Star India — Modular Static Structure

Extension-split build. Shared CSS/JS live once in `assets-new/`; pages and forms link to them.

```
fsia-modular/
├── assets/
│   ├── css/
│   │   ├── main.css          global tokens, typography, header, footer, nav, mobile
│   │   └── forms-master.css  form layout, inputs, consent panel, hero banner
│   ├── js/
│   │   ├── main.js           homepage effects + smart-registration modal
│   │   └── forms-handler.js  universal form validation, 48h urgency, routing
│   └── media/
│       ├── logo.gif
│       └── banners/          hero/banner images
├── public_html/              all main pages and PHP forms (e.g. forever-mrs-india-new.php)
├── layout/                   header1806.php, footer1806.php (Server-side includes)
└── README.md
```

## How the forms work
- Each `forms/register-*.html` links `../assets-new/css/main.css` + `../assets-new/css/forms-master.css`.
- Each `<form id="registrationForm" data-category="...">` carries its category name; the
  universal `../assets-new/js/forms-handler.js` reads it, validates Name/Email/Phone, runs the
  48-hour urgency countdown, and routes to `../registration-success.html`.
- Per-form unique CSS (brand `:root` colours, 3D crown, SVG posters, embedded images)
  stays inline in each form — only the shared chrome/form CSS was extracted.
- Logo resolves to `../assets-new/media/logo.gif`; banners to `../assets-new/media/banners/`.

## Path note
Because root-relative URLs (starting with `/`) have been removed for SEO optimization, ensure all PHP files in `public_html/` use relative paths pointing backward to the assets folder (e.g., `../assets-new/...`).
