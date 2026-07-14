/**
 * Front-end behaviour for balefire/product-switcher.
 *
 * Progressive enhancement over a server-rendered tabs pattern: PHP marks the
 * first tab selected and hides the rest, so the block is coherent with JS off
 * (you see the first product). This wires up switching and keyboard support.
 *
 * Loaded as an inline script — vendor/ can sit outside the webroot on Bedrock,
 * so the package never assumes a URL to its own files.
 */
(() => {
    const init = (root) => {
        const tabs = Array.from(root.querySelectorAll('[role="tab"]'));
        const panels = Array.from(root.querySelectorAll('[role="tabpanel"]'));
        const dots = Array.from(root.querySelectorAll('[data-dot]'));

        if (tabs.length === 0) {
            return;
        }

        const select = (index, { focus = false } = {}) => {
            tabs.forEach((tab, i) => {
                const active = i === index;
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
                // Roving tabindex: only the selected tab is in the tab order,
                // arrow keys move between them.
                tab.tabIndex = active ? 0 : -1;
            });

            panels.forEach((panel, i) => {
                panel.toggleAttribute('hidden', i !== index);
            });

            dots.forEach((dot, i) => {
                dot.setAttribute('data-active', i === index ? 'true' : 'false');
            });

            if (focus) {
                tabs[index].focus();
            }
        };

        tabs.forEach((tab, i) => {
            tab.addEventListener('click', () => select(i));

            tab.addEventListener('keydown', (event) => {
                const last = tabs.length - 1;
                let next = null;

                if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                    next = i === last ? 0 : i + 1;
                } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                    next = i === 0 ? last : i - 1;
                } else if (event.key === 'Home') {
                    next = 0;
                } else if (event.key === 'End') {
                    next = last;
                }

                if (next !== null) {
                    event.preventDefault();
                    select(next, { focus: true });
                }
            });
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
        document.querySelectorAll('.bma-product-switcher').forEach(init);
    });
})();
