# Viscode Creative

Company profile website for Viscode Creative: web development, graphic design, and ID card & event services.

Static HTML, CSS, and vanilla JavaScript. No build step.

## Features

- Responsive single-page layout
- ID / EN language toggle (persisted in `localStorage`)
- WhatsApp consultation links configured from one place
- Scroll reveal animations

## Structure

```
.
├── index.html
├── style.css
├── script.js
└── assets/
    ├── logo.png
    └── favicon.png
```

## Configuration

Set the WhatsApp number in `script.js`:

```js
var WA_NUMBER = '62XXXXXXXXXX'; // international format, no + or spaces
```

English translations live in the `EN` object in `script.js`.

## Run locally

```bash
npx serve
```

Or open `index.html` directly in a browser.

## Deploy

```bash
npm i -g vercel
vercel --prod
```

Preset: **Other**, no build command.
