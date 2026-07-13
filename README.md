# balefire-sage-components

Balefire Agency's shared Sage 10 + Tailwind component library. Gutenberg
blocks backed by Blade components, distributed via Composer sub-packages.

## Architecture

Each component is a Composer sub-package under `packages/`. Every component
follows a three-layer model:

| Layer | File | Purpose |
|-------|------|---------|
| Blade component | `resources/views/components/*.blade.php` | Single source of truth for markup + Tailwind classes |
| Gutenberg block | `blocks/*/block.json`, `render.php`, `editor.js` | Client editing UI in the block editor |
| Shortcode | `src/bootstrap.php` | Optional WPBakery / legacy compat |

The Blade file is the only place markup lives. The block's `render.php`
maps attributes to Blade props and echoes the rendered view. The editor.js
provides the InspectorControls UI and a lightweight preview placeholder.

## Component catalog

All 36 blocks from the `balefire-blocks` plugin live here as
`balefireict/component-<slug>` packages, plus one shared package:

- **`component-support`** — the base package every component requires:
  `SectionStyles` token-to-class maps, `BlockAttributes` (parses
  `get_block_wrapper_attributes()` into the Blade `$attributes` bag so
  anchor/spacing/className supports reach the markup), the shared
  "Balefire" block category, and the shared frontend CSS
  (`resources/css/{view,layout,swiper}.css`) consumer themes import.
- **Layout** — container, section, grid-row, grid-cell, auto-grid,
  layout-grid, sections-flexible-widths, split-35-65, two-col-three-col
- **Cards** — card-icon-break, card-media, card-stat, simple-card,
  simple-icon-stacked-cards, simple-image-card, simple-steps-card,
  team-member-centered, logo-grid-item
- **Headers/heroes** — featured-image-header, hero-video-header,
  page-header, preheader-and-title, section-heading, centered-intro-text
- **Content rows** — feature-row, features-section, image-text-row,
  image-text-rows, image-tile-cta, case-study-compare
- **CTAs & misc** — cta-banner, cta-centered-text-ra, faq-items,
  faq-no-borders, portrait-swiper-slides, posts-grid

InnerBlocks-based blocks (container, section, grids, cards that wrap
arbitrary content) pass the rendered children through as a `content`
prop and ship no shortcode layer; the same goes for blocks whose
attributes are arrays (image-text-row, portrait-swiper-slides,
two-col-three-col), which shortcodes can't express.

## Requirements

- PHP 8.2+
- WordPress 6.4+
- Sage 10 or Acorn-powered theme (for Blade rendering)
- Tailwind CSS (theme-side build pipeline)

## Monorepo structure

```
balefire-sage-components/
  composer.json              monorepo root (path repos, symlinks)
  packages/
    component-cta-banner/
      composer.json          sub-package manifest
      src/
        bootstrap.php        registers block + shortcode on init
        Renderer.php         delegates to Blade view
      blocks/
        cta-banner/
          block.json         Gutenberg metadata
          render.php         block render callback (maps attrs -> Blade)
          editor.js          InspectorControls + editor preview
      resources/
        views/
          components/
            cta-banner.blade.php   the markup (single source of truth)
```

## Local development

The monorepo uses Composer path repos with symlinks:

```bash
cd balefire-sage-components
composer install
```

Each sub-package is symlinked into vendor/. Edit a package, the change
reflects instantly in any consumer that symlinked it.

## Consumer site wiring (one-time theme setup)

### 1. Composer require the components you want

```bash
composer require balefireict/component-cta-banner
```

Or via subtree (WPE sites — see PLAN.md for details).

### 2. Register Blade view namespace in the Sage theme

```php
// app/setup.php (or theme functions)
add_filter('acorn/view/paths', function ($paths) {
    foreach (glob(base_path('vendor/balefireict/*/resources/views')) as $dir) {
        $paths['bma::'] = $dir;
    }
    return $paths;
});
```

### 3. Define design tokens

Components use semantic Tailwind classes (`bg-primary`, `text-accent`,
`font-headline`). Each consumer theme maps these to real brand values
via a Tailwind v4 `@theme` block. See `docs/DESIGN-TOKENS.md` for the
full token contract and setup instructions.

### 4. Tailwind content config

Scan vendor paths so Tailwind's JIT picks up the component classes.
`src/**/*.php` matters too — `component-support`'s SectionStyles maps
hold the tone classes:

```js
// tailwind.config.js (or app.css @content for Tailwind v4)
content: [
    './resources/views/**/*.blade.php',
    './vendor/balefireict/*/resources/views/**/*.blade.php',
    './vendor/balefireict/*/blocks/**/*.php',
    './vendor/balefireict/*/src/**/*.php',
]
```

### 5. Import the shared component CSS

`component-support` ships the plain-CSS layer the blocks rely on
(section padding/stacking rules, hero overlays and buttons, Swiper).
Import it into the theme stylesheet so it rides the theme build:

```css
/* app.css */
@import '../../vendor/balefireict/component-support/resources/css/view.css';
```

(`view.css` pulls in `layout.css` and `swiper.css`.) That import is the
only asset step. Everything else is self-registering with **no URL
assumptions** — packages live in `vendor/`, which Bedrock keeps outside
the webroot, so nothing may ever build a URL to a package file (see
[WordPress plugins that assume your directory structure](https://roots.io/wordpress-plugins-that-assume-your-directory-structure/)):

- **Editor scripts** are plain browser-ready JS (`wp.*` globals, no
  imports, no build step). Each bootstrap registers an src-less handle
  and attaches the file's contents via `wp_add_inline_script()`.
- **Block styles** (the ten blocks with a `style.css`) work the same
  way: `block.json` points at a handle, the bootstrap inlines the CSS
  with `wp_add_inline_style()`, and WordPress prints it only where the
  block renders.

### 6. Build and go

```bash
npm run build     # Bud compiles the theme + scans vendor Blade files
```

The block appears in the Gutenberg inserter under the "Balefire" category.
Clients edit it normally. The frontend renders server-side via Blade.

## Block registration

Each package's `src/bootstrap.php` auto-registers its block via
`register_block_type()` pointing at the package's `blocks/` directory.
No manual block registration needed in the theme.

## License

MIT
