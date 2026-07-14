/**
 * Balefire — shared, zero-dependency lightbox.
 *
 * Built on the native <dialog> element, so the browser supplies the top layer,
 * a real ::backdrop, focus trapping and Escape-to-close for free. No library.
 *
 * Any component can reach it the same way:
 *
 *   window.balefireLightbox.openHtml(html, { label })   — trusted markup
 *   window.balefireLightbox.openText(text, { label })   — plain text (escaped)
 *   window.balefireLightbox.openNode(node, { label })   — an existing DOM node
 *   window.balefireLightbox.openTemplate(el, { label }) — a <template>'s content
 *   window.balefireLightbox.close()
 *
 * Declarative use, no JS per consumer: any element with
 *
 *   <button data-balefire-lightbox="#some-template-id">
 *
 * opens that element's content. A delegated click handler picks it up, so markup
 * injected after load works with nothing to re-bind.
 *
 * openText() is the safe default for anything user- or DB-supplied: it sets
 * textContent, so the string can never be re-parsed as markup. Reach for
 * openHtml() only with markup you produced.
 *
 * Styling lives in component-support's view.css (.bma-lightbox*).
 *
 * Registered by component-support as the `balefire-lightbox` script handle;
 * depend on that handle rather than loading this yourself.
 */
(() => {
    if (window.balefireLightbox) {
        return; // Already installed — one instance per page.
    }

    let dialog = null;
    let stage = null;

    /* ---------------------------------------------------------------
       DIALOG (lazy — pages that never open one never touch the DOM)
       --------------------------------------------------------------- */
    const ensureDialog = () => {
        if (dialog) return dialog;

        dialog = document.createElement('dialog');
        dialog.className = 'bma-lightbox';
        dialog.innerHTML = `
            <button type="button" class="bma-lightbox__close" data-close aria-label="Close">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
            <div class="bma-lightbox__stage" data-stage></div>
        `;

        stage = dialog.querySelector('[data-stage]');

        dialog.querySelector('[data-close]').addEventListener('click', () => close());

        // Clicking the backdrop means clicking the dialog itself, since its
        // content sits in child elements.
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) close();
        });

        // Let go of the content when it closes, so nothing lingers in the DOM.
        dialog.addEventListener('close', () => {
            stage.replaceChildren();
        });

        document.body.append(dialog);
        return dialog;
    };

    const open = (label) => {
        const d = ensureDialog();
        d.setAttribute('aria-label', label || 'Dialog');
        d.showModal();
        return d;
    };

    const close = () => {
        if (dialog && dialog.open) dialog.close();
    };

    /* ---------------------------------------------------------------
       PUBLIC API
       --------------------------------------------------------------- */
    const openNode = (node, options = {}) => {
        if (!node) return;
        ensureDialog();
        stage.replaceChildren(node);
        open(options.label);
    };

    const openHtml = (html, options = {}) => {
        ensureDialog();
        stage.innerHTML = String(html ?? '');
        open(options.label);
    };

    // Safe for anything from the database or a user: never parsed as markup.
    const openText = (text, options = {}) => {
        ensureDialog();
        const p = document.createElement('div');
        p.className = 'bma-lightbox__text';
        p.textContent = String(text ?? '');
        stage.replaceChildren(p);
        open(options.label);
    };

    const openTemplate = (template, options = {}) => {
        if (!template) return;
        const content = template.content
            ? template.content.cloneNode(true)
            : template.cloneNode(true);
        ensureDialog();
        stage.replaceChildren(content);
        open(options.label);
    };

    window.balefireLightbox = { openNode, openHtml, openText, openTemplate, close };

    /* ---------------------------------------------------------------
       DECLARATIVE TRIGGERS (delegated — works for injected markup too)
       --------------------------------------------------------------- */
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-balefire-lightbox]');
        if (!trigger) return;

        const selector = trigger.getAttribute('data-balefire-lightbox');
        if (!selector) return;

        const target = document.querySelector(selector);
        if (!target) return;

        event.preventDefault();

        const label = trigger.getAttribute('data-balefire-lightbox-label') || undefined;

        if (target.tagName === 'TEMPLATE') {
            openTemplate(target, { label });
        } else {
            openNode(target.cloneNode(true), { label });
        }
    });
})();
