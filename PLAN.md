# Balefire Sage Components — Shared Block + Blade Library

**Prepared for:** Scott
**Author:** Jeremy
**Date:** June 2026

---

## TL;DR

Standardize the agency on **Sage 10 + Tailwind CSS** backed by a shared
**Gutenberg block + Blade component library** distributed via Composer.
Every new client site pulls from the same pool of reusable sections.
Build a section once, ship it everywhere. WPBakery sites stay untouched.

Accu-shot.com is already being built on Sage, giving us a running start
on the component catalog. What we extract from that project becomes the
foundation of the library.

---

## Sage vs EtchWP

We evaluated two paths for the new stack.

### Sage 10

Sage is an open-source WordPress theme framework by Roots (MIT licensed,
10+ years of development, large community). It uses Blade templates,
Tailwind CSS, and a modern build pipeline (Bud).

Why it fits:

- Native Tailwind CSS. No framework lock-in, no proprietary CSS system.
- Components are real files (PHP, Blade, JS). Version controlled,
  diffable in git, distributable via Composer packages.
- Zero vendor lock-in. MIT licensed. If Roots disappeared tomorrow,
  every site keeps working.
- Skills transfer beyond WordPress. Blade and Tailwind are standard
  web dev skills the team can use anywhere.
- We already have a live Sage build (accu-shot.com) to extract from.

### EtchWP

Etch (by Digital Gravy / Kevin Geary) is a visual builder that outputs
clean code and syncs to Gutenberg blocks. Genuinely impressive tool.

Why it doesn't fit:

- Uses Automatic.css (ACSS), not Tailwind. Fundamentally different CSS
  methodology from what we're committing to.
- Components are JSON blobs stored in the WordPress database, not
  version-controlled PHP files. Cannot be wired to Composer. Cannot be
  diffed, reviewed, or packaged.
- Proprietary and commercial. Agency and every client site locked to
  Digital Gravy's licensing, pricing, and product roadmap.
- No migration path out. Components built in Etch are Etch-specific JSON.
  Leaving means rewriting, not porting.
- Still v1.5.x. Young product with an explicitly experimental API surface.

Etch is the right tool for a different kind of agency. It's the wrong
tool for a Composer-distributed, Tailwind-based component library.

---

## The Architecture: Blade + Blocks Blend

Each component in the library is built on a three-layer model:

### Layer 1: Blade Component (the markup)

The single source of truth for the component's HTML and Tailwind classes.
A Blade file with typed props, slots, conditionals, and loops.

```
resources/views/components/cta-banner.blade.php
```

```blade
@props([
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'tone' => 'primary',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

<section {{ $attributes->class(['rounded-[2rem] px-6 py-8 md:px-10 md:py-12', ...]) }}>
    <div class="mx-auto flex max-w-[72rem] flex-col gap-8 md:flex-row md:items-end md:justify-between">
        @unless(empty($eyebrow))
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-current/80">{{ $eyebrow }}</p>
        @endunless
        @unless(empty($title))
            <h2 class="text-3xl font-headline leading-[1.05] md:text-5xl">{{ $title }}</h2>
        @endunless
        {{-- buttons, content, etc --}}
    </div>
</section>
```

### Layer 2: Gutenberg Block (client editing UI)

A thin wrapper that maps block attributes to Blade component props. The
block editor is just the editing interface. The frontend is pure
server-rendered HTML from the Blade file.

```
blocks/cta-banner/
  block.json      metadata, attributes, supports
  render.php      maps block attrs -> Blade props, echoes rendered view
  editor.js       InspectorControls (React/JSX)
```

render.php is just glue:

```php
echo view('bma::components.cta-banner', [
    'eyebrow'      => $attributes['eyebrow'] ?? '',
    'title'        => $attributes['title'] ?? '',
    'tone'         => $attributes['tone'] ?? 'primary',
    'primaryLabel' => $attributes['primaryLabel'] ?? '',
    'primaryUrl'   => $attributes['primaryUrl'] ?? '',
])->render();
```

### Layer 3: Shortcode (optional backward compat)

For WPBakery sites during migration. Same Blade view, different entry point.

```php
// [bma_cta_banner title="..." tone="dark"]
add_shortcode('bma_cta_banner', function($atts) {
    return view('bma::components.cta-banner', shortcode_atts([...], $atts))->render();
});
```

### Why PHP render callbacks (not React save functions)

- Markup lives entirely in PHP/Blade. Diffs cleanly in git.
- No block deprecation churn when attributes change.
- ACF fields, WP queries, conditional logic all run server-side.
- Same proven pattern nnnode already uses.

### When to use each layer

| Layer             | Used for                                            | Who places it               |
| ----------------- | --------------------------------------------------- | --------------------------- |
| Blade only        | Headers, footers, page layouts, structural wrappers | Developer (theme templates) |
| Blade + Block     | CTAs, hero sections, feature grids, testimonials    | Client (Gutenberg editor)   |
| Blade + Shortcode | Backward compat for WPBakery migration              | Legacy content              |

Same Blade file powers all three. Edit once, update everywhere.

---

## The Monorepo

All components live in a single git repo (`balefire-sage-components`) as
sub-packages under `packages/`. This mirrors the existing
`balefire-components` monorepo pattern already proven on David Tours.

### Monorepo structure

```
balefire-sage-components/                   (one git repo, public)
  composer.json                              monorepo root — path repos
  packages/
    component-cta-banner/
      composer.json                          PSR-4 autoload + bootstrap
      src/bootstrap.php                      registers block + shortcode
      blocks/cta-banner/
        block.json
        render.php
        editor.js
      resources/views/components/
        cta-banner.blade.php                 single source of truth (markup)
      acf-json/                              ACF field definitions (if needed)

    component-hero/
      composer.json
      src/bootstrap.php
      blocks/hero/
        block.json
        render.php
        editor.js
      resources/views/components/
        hero.blade.php
      acf-json/

    component-buttons/
      ...
```

### Selective installation — three paths

Sites only install the components they actually use. The monorepo is the
single source; consumers pick what they need.

**Local dev (monorepo itself):**

The monorepo's own composer.json uses path repos with symlinks:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "./packages/*",
      "options": { "symlink": true }
    }
  ]
}
```

Developers edit a package, the symlink reflects it instantly. This is the
same pattern as the existing balefire-components monorepo.

**WPE sites (Path A — git subtree + committed vendor/):**

The consumer site git-subtrees the monorepo into the site repo (same as
David Tours does today), then selectively requires only the packages it
needs:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "wp-content/themes/balefire-sage/components/packages/*",
      "options": { "symlink": false }
    }
  ],
  "require": {
    "balefireict/component-cta-banner": "dev-main",
    "balefireict/component-hero": "dev-main"
  }
}
```

vendor/ is committed and deployed via GitPush. Only the required packages
land in vendor/. No server-side Composer. Proven on David Tours.

**VPS / Trellis sites (Path B — Forgejo Composer registry):**

Forgejo has a built-in Composer package registry. A CI job publishes each
sub-package to the registry on push to main. Consumer sites then require
packages from the registry URL with no subtree at all:

```json
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://git.balefireagency.com/api/packages/balefire/composer"
    }
  ],
  "require": {
    "balefireict/component-cta-banner": "dev-main"
  }
}
```

Composer runs on deploy (Trellis). Fully selective, no subtree, no
committed vendor/. This is the cleanest path for VPS sites.

### Consumer site wiring (one-time theme setup)

1. `composer require balefireict/component-cta-banner` (and others)
2. Each package auto-registers its Gutenberg block on init
3. Sage theme registers the Blade view namespace once:
   `bma::` -> vendor package views directories
4. Tailwind content config scans vendor paths:
   `vendor/balefireict/*/resources/views/**/*.blade.php`
   `vendor/balefireict/*/blocks/**/*.php`
5. Blocks appear in Gutenberg inserter, render server-side via Blade

Build a component once. Every site that requires it gets it. Update the
package. Every site gets the improvement.

---

## Repository

### Hosting

Self-hosted Forgejo on the existing Hetzner VPS (40+ days uptime).
Public repos at `git.balefireagency.com`.

- Component library monorepo: public (no secrets, just reusable UI)
- Client site repos: private on the same Forgejo instance
- Forgejo Composer registry: public read (components are open)

### Why Forgejo instead of GitHub

- Agency code stays on agency infrastructure
- No per-seat or private-repo costs
- Built-in Composer registry for selective package install
- We already use Forgejo for personal projects (forge.nusserstudios.com)

### Why a monorepo (not one repo per component)

- One PR can touch multiple components (e.g., updating a shared token
  across hero + cta + feature-grid simultaneously)
- Atomic versioning — all components share a coherent release history
- Simpler CI — one pipeline lints, tests, and publishes all packages
- Fewer repos to manage, clone, and keep in sync
- Proven pattern: we already run balefire-components this way

---

## Deployment: Two Paths

### Path A: WP Engine (existing clients)

- Sage theme in standard `wp-content/themes/` layout
- `vendor/` committed to repo (no server-side Composer)
- `dist/` (compiled assets) committed to repo
- Deploy via existing GitPush workflow
- No Bedrock, no Trellis, no workflow changes
- This is the same model David Tours already uses

### Path B: VPS (new projects needing full control)

- Full Roots stack: Bedrock + Sage + Trellis
- Bedrock: Composer-managed WordPress core, plugins, themes
- Trellis: Ansible-provisioned server (nginx, PHP-FPM, MariaDB, Redis)
- Atomic deploys via symlink swap (instant rollback)
- Environment parity (dev/staging/prod from same Ansible config)
- Composer runs on deploy (no committed vendor/)
- Use case: dedicated server projects where WordPress is the whole box

Both paths consume the same block library with zero modifications to the
component packages. The packages don't know or care where they're installed.

---

## Starting Advantage: Accu-Shot

Accu-shot.com is already being built on Sage. The sections we build for
that project (hero, features, CTA, testimonials, etc.) become the first
entries in the component library. This means:

- Phase 1 isn't starting from a blank slate. We're extracting from a real
  production build.
- The first blocks are battle-tested on a real client site before they
  enter the library.
- The extraction process itself validates the tooling and package shape.
- Accu-shot stays on its current build. We just pull the section markup
  into properly-structured Composer packages alongside it.

---

## Team Impact

### What Changes

- Templating: Blade components instead of raw PHP files
- Styling: Tailwind utility classes instead of vanilla CSS
- Editor UI: React/JSX for block InspectorControls (nnnode pattern)
- Build tool: Bud (Sage's build system, similar to Vite)

### What Stays the Same

- WordPress core, ACF PRO, WP-CLI, client management
- Existing WPBakery sites (untouched, fully supported)
- WPE deployment workflow (GitPush, committed vendor/)

### Learning Curve

| Skill                   | Time to productive | Notes                                                      |
| ----------------------- | ------------------ | ---------------------------------------------------------- |
| Tailwind CSS            | 1 week             | Utility-class mindset clicks fast for anyone who knows CSS |
| Blade templating        | 2-3 days           | It's PHP with cleaner syntax and components                |
| Gutenberg block API     | 1-2 weeks          | InspectorControls, attributes, render callbacks            |
| Sage conventions        | 2-3 days           | Controller/view pattern, Bud config                        |
| **Total per developer** | **2-3 weeks**      | Overlapping, not sequential                                |

---

## Timeline

### Phase 1: Foundation (Weeks 1-2)

- Forgejo on Hetzner at `git.balefireagency.com`
- Monorepo scaffold with Composer auto-discovery
- Extract 3-5 blocks from accu-shot into proper component packages
- Set up a local Sage test site, validate the full pipeline end-to-end:
  composer require -> block registers -> Tailwind picks up classes ->
  client edits in Gutenberg -> server renders Blade view

### Phase 2: Accu-Shot Completion + Library Bootstrapping (Weeks 3-5)

- Finish accu-shot.com on Sage + Tailwind
- All sections built as proper component packages from the start
- Document the workflow, pitfalls, and build guide
- accu-shot becomes the reference implementation for the team

### Phase 3: Library Expansion (Weeks 6-8)

- Port the full component catalog from existing sites (20+ blocks)
- Block documentation with screenshots and usage examples
- Write the "new Sage site build guide"
- Team training sessions

### Phase 4: Full Adoption (Week 9+)

- All new client projects start on Sage + Tailwind
- Existing WPBakery sites continue as-is
- Migrate opportunistically (major redesigns, client requests)

---

## Costs

| Item                  | Cost              | Notes                             |
| --------------------- | ----------------- | --------------------------------- |
| Forgejo VPS (Hetzner) | Already running   | Existing server, 400+ days uptime |
| Sage                  | Free              | MIT licensed                      |
| Tailwind              | Free              | Open source                       |
| Team training         | 2-3 weeks per dev | Blade + Tailwind + Gutenberg API  |

Hard cost: $0. The investment is team time.

---

## Risks and Mitigations

| Risk                           | Likelihood | Impact | Mitigation                                            |
| ------------------------------ | ---------- | ------ | ----------------------------------------------------- |
| Team adoption resistance       | Medium     | Medium | Prove on accu-shot first, then roll out with training |
| Library fragmentation          | Low        | Medium | One maintainer owns the library, PRs from devs        |
| Sage breaking changes          | Low        | Low    | Sage 10 stable, Roots 10yr track record               |
| Forgejo downtime blocks builds | Low        | Low    | Commit vendor/ on WPE; VPS uses CI with retry         |
| Client insists on WPBakery     | Low        | Low    | WPBakery sites stay as-is, no forced migration        |
| Blade learning curve           | Low        | Low    | Team has PHP experience, Blade is a small step        |

---

## What We're Asking For

1. Green-light the Forgejo setup on Hetzner (already running, just need
   the DNS record and Forgejo container)
2. Approve Phase 1: 2-week sprint to scaffold the library and extract
   the first blocks from accu-shot
3. Confirm accu-shot.com as the first Sage build and reference
   implementation for the component library
4. Identify the second Sage project for Phase 2/3 expansion

No change to existing client workflows. No new licenses. No recurring
SaaS dependencies. This is an investment in agency leverage that pays
back on the second Sage site build.
