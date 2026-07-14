/**
 * Front-end behaviour for balefire/reviews.
 *
 * Two things, both progressive enhancements over server-rendered markup:
 *
 *   1. Carousel — the track is a CSS scroll-snap row, so it already scrolls and
 *      swipes with zero JS. This wires the prev/next buttons and the dots to it,
 *      and keeps their state in sync with however the user scrolled.
 *
 *   2. Lightbox — the full review in a native <dialog>. Following the davidtours
 *      pattern: the browser gives us the top layer, a real ::backdrop, focus
 *      trapping and Escape-to-close for free, so there is no library here.
 *
 * With JS off you still get: readable clamped cards, a swipeable track, and the
 * review text present in the DOM.
 *
 * Loaded as an inline script — vendor/ can sit outside the webroot on Bedrock,
 * so the package never assumes a URL to its own files.
 */
(() => {
    /* ---------------------------------------------------------------
       LIGHTBOX
       Uses the shared component-support lightbox (window.balefireLightbox),
       registered as the `balefire-lightbox` script handle and declared as a
       dependency of this one — so it is always present by the time we run.
       --------------------------------------------------------------- */
    const openReview = (card) => {
        const lightbox = window.balefireLightbox;
        if (!lightbox) return; // Dependency missing — leave the clamped card as-is.

        const name = card.getAttribute('data-name') || '';
        const location = card.getAttribute('data-location') || '';
        const body = card.getAttribute('data-body') || '';

        // Build the review's own content, then hand the node to the generic
        // lightbox. The lightbox stays review-agnostic; the shape lives here.
        const wrap = document.createElement('div');
        wrap.className = 'bma-review-full';

        const quote = document.createElement('blockquote');
        quote.className = 'bma-lightbox__text';
        // textContent, never innerHTML: the body is plain text from the
        // database and must never be re-parsed as markup.
        quote.textContent = body;
        wrap.append(quote);

        const footer = document.createElement('footer');
        footer.className = 'bma-review-full__by';

        const who = document.createElement('p');
        who.className = 'bma-review-full__name';
        who.textContent = name;
        footer.append(who);

        if (location !== '') {
            const where = document.createElement('p');
            where.className = 'bma-review-full__location';
            where.textContent = location;
            footer.append(where);
        }

        wrap.append(footer);

        lightbox.openNode(wrap, { label: name ? `Review from ${name}` : 'Full review' });
    };

    /* ---------------------------------------------------------------
       CAROUSEL — loops seamlessly.

       The technique: append one aria-hidden clone of every slide after the
       originals, let the user scroll into that clone region, and the moment
       the scroll settles there, silently re-base scrollLeft back by the
       originals' width. The pixels are identical, so the jump is invisible —
       "next" therefore works forever. "prev" from the first page does the
       inverse: an invisible jump forward into the clones, then a smooth step
       back. Swiping past the end gets the same treatment via the settle
       handler, so touch loops too.
       --------------------------------------------------------------- */
    const initCarousel = (root) => {
        const track = root.querySelector('[data-track]');
        const prev = root.querySelector('[data-prev]');
        const next = root.querySelector('[data-next]');
        const dots = Array.from(root.querySelectorAll('[data-dot]'));

        // Originals only: if init ever runs against a track that has already
        // been set up, treating existing clones as slides would clone the
        // clones and the loop math would double.
        const slides = Array.from(track ? track.children : [])
            .filter((el) => !el.hasAttribute('data-clone'));

        if (!track || slides.length === 0 || track.querySelector('[data-clone]')) return;

        /*
         * Measure the real distance between two slides rather than assuming it
         * equals the slide's width — the track has a gap, so width alone is
         * short by one gap per slide and every page lands a little further left
         * than it should. Scroll-snap papers over it, but the dots then
         * disagree with what you are looking at. Measure the stride instead.
         */
        const stride = () => {
            if (slides.length > 1) {
                const gap = slides[1].offsetLeft - slides[0].offsetLeft;
                if (gap > 0) return gap;
            }
            return slides[0].getBoundingClientRect().width || 1;
        };

        // How many slides fit right now — drives paging and dot count.
        const perView = () => Math.max(1, Math.round(track.clientWidth / stride()));

        const pageCount = () => Math.max(1, Math.ceil(slides.length / perView()));

        /*
         * Clones for the loop. aria-hidden + tabindex=-1: they are scenery for
         * the wrap-around, and must be invisible to screen readers and the tab
         * order or every review would be announced twice. Skipped when all
         * slides already fit (nothing to scroll, nothing to loop).
         */
        const loop = slides.length > perView();

        if (loop) {
            slides.forEach((slide) => {
                const clone = slide.cloneNode(true);
                clone.setAttribute('data-clone', '');
                clone.setAttribute('aria-hidden', 'true');
                clone.querySelectorAll('a, button, [tabindex]').forEach((el) => {
                    el.tabIndex = -1;
                });
                track.append(clone);
            });
        }

        // Width of one full set — the distance the invisible re-base jumps.
        const setWidth = () => slides.length * stride();

        const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // Instant, invisible reposition: suspend smooth scrolling for one write.
        const jumpBy = (delta) => {
            const previous = track.style.scrollBehavior;
            track.style.scrollBehavior = 'auto';
            track.scrollLeft += delta;
            track.style.scrollBehavior = previous;
        };

        /*
         * Re-base once the user is inside the clone region. Called from the
         * settle handler, never mid-animation — interrupting a smooth scroll
         * with a scrollLeft write would make the loop stutter.
         */
        const normalize = () => {
            if (!loop) return;
            const width = setWidth();
            if (track.scrollLeft >= width) {
                jumpBy(-width);
            }
        };

        // Page index the dots should report — clone region maps onto page 0.
        const currentPage = () => {
            const raw = Math.round(track.scrollLeft / (stride() * perView()));
            return loop ? raw % pageCount() : Math.min(raw, pageCount() - 1);
        };

        const goToOffset = (left) => {
            track.scrollTo({
                left,
                behavior: reducedMotion() ? 'auto' : 'smooth',
            });
        };

        const goToPage = (page) => {
            const target = Math.min(Math.max(page, 0), pageCount() - 1);
            const index = Math.min(target * perView(), slides.length - 1);
            goToOffset(slides[index].offsetLeft - slides[0].offsetLeft);
        };

        const goNext = () => {
            if (!loop) {
                goToPage(currentPage() + 1);
                return;
            }
            // One page forward, clone region included — the settle handler
            // re-bases once we arrive, so "next" never runs out of road.
            const pageStride = stride() * perView();
            const rawPage = Math.round(track.scrollLeft / pageStride);
            goToOffset(Math.min((rawPage + 1) * pageStride, setWidth()));
        };

        const goPrev = () => {
            if (!loop) {
                goToPage(currentPage() - 1);
                return;
            }
            if (track.scrollLeft <= 1) {
                // At the very start: invisibly re-base into the identical clone
                // region, then step back smoothly — reads as wrapping to the end.
                jumpBy(setWidth());
            }
            const pageStride = stride() * perView();
            const rawPage = Math.round(track.scrollLeft / pageStride);
            goToOffset(Math.max((rawPage - 1) * pageStride, 0));
        };

        const sync = () => {
            const page = currentPage();
            const pages = pageCount();

            dots.forEach((dot, i) => {
                const active = i === page;
                dot.setAttribute('data-active', active ? 'true' : 'false');
                dot.setAttribute('aria-current', active ? 'true' : 'false');
                // Dots beyond the current page count are meaningless.
                dot.hidden = i >= pages;
            });

            // A looping carousel has no ends to disable at.
            if (prev) prev.disabled = !loop && page <= 0;
            if (next) next.disabled = !loop && page >= pages - 1;
        };

        if (prev) prev.addEventListener('click', goPrev);
        if (next) next.addEventListener('click', goNext);

        dots.forEach((dot, i) => dot.addEventListener('click', () => goToPage(i)));

        /*
         * Settle detection: native `scrollend` where it exists, a quiet-period
         * timer where it does not (Safari). This is where the loop's re-base
         * happens, so swipes and momentum scrolling wrap exactly like the
         * buttons do.
         */
        let settleTimer = null;
        const onSettle = () => {
            normalize();
            sync();
        };

        if ('onscrollend' in track) {
            track.addEventListener('scrollend', onSettle);
        }

        let raf = null;
        track.addEventListener('scroll', () => {
            if (raf) cancelAnimationFrame(raf);
            raf = requestAnimationFrame(sync);

            if (!('onscrollend' in track)) {
                if (settleTimer) clearTimeout(settleTimer);
                settleTimer = setTimeout(onSettle, 120);
            }
        }, { passive: true });

        window.addEventListener('resize', sync);

        sync();
    };

    /* ---------------------------------------------------------------
       BOOT
       --------------------------------------------------------------- */
    /* ---------------------------------------------------------------
       CLAMP DETECTION
       Only offer "read more" where the text really is cut off. Whether three
       clamped lines truncate a review depends on how it wraps at the card's
       width — a character count cannot know that, and gets it wrong both ways.
       Measure the element instead: a clamped <p> whose content is taller than
       its box is being truncated.
       --------------------------------------------------------------- */
    const syncClamps = (root) => {
        root.querySelectorAll('[data-review]').forEach((card) => {
            const body = card.querySelector('[data-review-body]');
            const more = card.querySelector('[data-review-more]');
            if (!body || !more) return;

            // 1px of slack: sub-pixel line heights round unpredictably.
            more.hidden = body.scrollHeight <= body.clientHeight + 1;
        });
    };

    const init = (root) => {
        initCarousel(root);

        syncClamps(root);

        // Re-measure when the cards change width (breakpoints) or the font loads,
        // either of which changes where the text wraps.
        window.addEventListener('resize', () => syncClamps(root));

        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(() => syncClamps(root));
        }

        // Delegated: one handler per block, regardless of card count.
        root.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-review-more]');
            if (!trigger) return;

            const card = trigger.closest('[data-review]');
            if (card) openReview(card);
        });
    };

    const ready = (callback) => {
        if (document.readyState !== 'loading') {
            callback();
            return;
        }
        document.addEventListener('DOMContentLoaded', callback, { once: true });
    };

    ready(() => {
        document.querySelectorAll('.bma-reviews').forEach(init);
    });
})();
