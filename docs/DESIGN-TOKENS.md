# Design Token Contract

Components in this library are **brand-agnostic**. They use semantic
Tailwind utility classes (`bg-primary`, `text-accent`, `font-headline`).
Each consumer theme is responsible for defining what those names resolve
to via a Tailwind v4 `@theme` block.

**Change the brand = change theme.css values. Components never change.**

---

## How it works

Three layers, left to right. Only the leftmost column changes per site.

```
theme.css (per-site)              app.css @theme (boilerplate)         component Blade
────────────────────────          ───────────────────────────          ──────────────
--vmg-blue: #00338f        →     --color-primary: var(--vmg-blue)  →  bg-primary
--vmg-red: #c8102e         →     --color-accent: var(--vmg-red)    →  text-accent
--font-heading: "DM Serif" →     --font-headline: var(--...)       →  font-headline
```

The component says "use the primary color." The theme says "primary is
blue." This separation means the same CTA Banner block looks correct on
every client site without touching component code.

---

## Required tokens (the contract)

Every consumer Sage theme MUST define these tokens in its Tailwind v4
`@theme` block. Components in this library will only use these names.

### Colors (semantic names — never raw palette slots)

| Tailwind utility | @theme variable | Usage |
|-----------------|-----------------|-------|
| `bg-primary` | `--color-primary` | Primary brand color (buttons, tone: primary) |
| `text-primary` | `--color-primary` | Text in primary color |
| `bg-primary-dark` | `--color-primary-dark` | Hover states for primary |
| `bg-secondary` | `--color-secondary` | Secondary brand color (tone: secondary) |
| `bg-accent` | `--color-accent` | Accent/CTA highlight color |
| `bg-accent-dark` | `--color-accent-dark` | Accent hover |
| `bg-dark` | `--color-dark` | Dark backgrounds (tone: dark) |
| `text-dark` | `--color-dark` | Body text on light backgrounds |
| `bg-surface` | `--color-surface` | Light surface/card background (tone: light) |
| `bg-muted` | `--color-muted` | Subtle backgrounds, muted sections |
| `text-muted` | `--color-muted` | Secondary/muted text |

### Typography

| Tailwind utility | @theme variable | Usage |
|-----------------|-----------------|-------|
| `font-headline` | `--font-headline` | Heading font family |
| `font-body` | `--font-body` | Body text font family |

### Optional tokens

These are used by some components but have sensible fallbacks:

| Tailwind utility | @theme variable | Default | Usage |
|-----------------|-----------------|---------|-------|
| `shadow-card` | `--shadow-card` | `0 6px 20px rgb(0 0 0 / 0.08)` | Card elevation |
| `rounded-card` | `--radius-card` | `2rem` | Card/banner radius |
| `max-w-content` | `--container-content` | *(none — required by the guide-page components)* | Content column of guide-hero, prose-section, link-card-grid, cta-band |

---

## Consumer theme setup (Tailwind v4)

The consumer Sage theme provides two files:

### 1. theme.css — brand variables (site-specific, the only per-site file)

Mirrors the davidtours pattern: raw brand swatches and font names as CSS
custom properties. This is literally the only file that changes between
client sites.

```css
/* theme.css — David Tours example */

:root {
    /* Brand swatches (from client palette) */
    --vmg-blue: #00338f;
    --vmg-blue-dark: #00186c;
    --vmg-red: #c8102e;
    --vmg-red-dark: #84081c;
    --vmg-slate: #6f779d;
    --vmg-body: #2e2e2e;
    --black: #1a1a1a;
    --white: #ffffff;

    /* Typography */
    --font-heading: "DM Serif Display", Georgia, serif;
    --font-body: "Montserrat", system-ui, sans-serif;
}
```

### 2. app.css — Tailwind import + @theme mapping (boilerplate, copy once)

Maps the brand vars to the semantic token names the components use.
Copy this into every new Sage site's CSS entry point and adjust the
`var()` references to match that site's theme.css variable names.

```css
/* app.css */

@import "tailwindcss";

@theme {
    /* Colors — mapped from brand vars in theme.css */
    --color-primary: var(--vmg-blue);
    --color-primary-dark: var(--vmg-blue-dark);
    --color-secondary: var(--vmg-red);
    --color-accent: var(--vmg-red);
    --color-accent-dark: var(--vmg-red-dark);
    --color-dark: var(--black);
    --color-surface: #f4f4f4;
    --color-muted: var(--vmg-slate);

    /* Typography */
    --font-headline: var(--font-heading);
    --font-body: var(--font-body);

    /* Optional */
    --radius-card: 2rem;
    --shadow-card: 0 6px 20px rgb(0 0 0 / 0.08);
}
```

That's it. The `@theme` block generates all the utility classes
(`bg-primary`, `text-accent`, `font-headline`, etc.) that the components
reference. Tailwind v4 resolves the `var()` chain at runtime.

---

## Adding a new client site

1. Copy `theme.css` from an existing site
2. Swap the brand color values and font names
3. Update the `var()` references in app.css `@theme` to point at the
   new site's variable names
4. All components from the library work immediately

The 30-second rebrand: change 6 hex values in theme.css, rebuild.

