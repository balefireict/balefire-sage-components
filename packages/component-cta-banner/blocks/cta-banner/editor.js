(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/cta-banner",
    "title": "CTA Banner",
    "category": "balefire",
    "icon": "megaphone",
    "description": "Call-to-action banner with tone variants, eyebrow/title/content, and dual buttons.",
    "keywords": ["cta", "banner", "marketing", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "spacing": {
            "margin": true,
            "padding": true
        }
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "tone": { "type": "string", "default": "primary" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "primaryStyle": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-cta-banner-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (val) => setAttributes({ eyebrow: val }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (val) => setAttributes({ title: val }),
                    }),
                    el(TextareaControl, {
                        label: __('Body Content', 'balefire'),
                        value: attributes.content || '',
                        onChange: (val) => setAttributes({ content: val }),
                    })
                ),
                el(PanelBody, { title: __('Appearance', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'primary',
                        options: [
                            { label: __('Primary', 'balefire'), value: 'primary' },
                            { label: __('Secondary', 'balefire'), value: 'secondary' },
                            { label: __('Dark', 'balefire'), value: 'dark' },
                            { label: __('Light', 'balefire'), value: 'light' },
                        ],
                        onChange: (val) => setAttributes({ tone: val }),
                    })
                ),
                el(PanelBody, { title: __('Primary Button', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.primaryLabel || '',
                        onChange: (val) => setAttributes({ primaryLabel: val }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.primaryUrl || '',
                        onChange: (val) => setAttributes({ primaryUrl: val }),
                    }),
                    el(SelectControl, {
                        label: __('Style', 'balefire'),
                        value: attributes.primaryStyle || '',
                        options: [
                            { label: __('Site default', 'balefire'), value: '' },
                            { label: __('Solid', 'balefire'), value: 'solid' },
                            { label: __('Outline', 'balefire'), value: 'outline' },
                        ],
                        onChange: (val) => setAttributes({ primaryStyle: val }),
                    })
                ),
                el(PanelBody, { title: __('Secondary Button', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.secondaryLabel || '',
                        onChange: (val) => setAttributes({ secondaryLabel: val }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.secondaryUrl || '',
                        onChange: (val) => setAttributes({ secondaryUrl: val }),
                    })
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('div', { className: 'bma-cta-banner rounded-[2rem] px-6 py-8 md:px-10 md:py-12 bg-neutral-100 text-dark' },
                    el('div', { className: 'mx-auto flex max-w-[72rem] flex-col gap-8 md:flex-row md:items-end md:justify-between' },
                        el('div', { className: 'max-w-[44rem] space-y-4' },
                            attributes.eyebrow && el('p', { className: 'text-sm font-semibold uppercase tracking-[0.2em] text-dark/80' }, attributes.eyebrow),
                            attributes.title && el('h2', { className: 'text-3xl font-bold leading-[1.05] md:text-5xl' }, attributes.title),
                            attributes.content && el('div', { className: 'max-w-[62ch] text-base leading-7 text-dark/85' }, attributes.content)
                        ),
                        (attributes.primaryLabel || attributes.secondaryLabel) && el('div', { className: 'flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end' },
                            attributes.primaryLabel && el('span', { className: 'inline-flex items-center justify-center rounded-full px-6 py-3 font-semibold bg-white text-dark' }, attributes.primaryLabel),
                            attributes.secondaryLabel && el('span', { className: 'inline-flex items-center justify-center rounded-full border border-current px-6 py-3 font-semibold text-current' }, attributes.secondaryLabel)
                        )
                    )
                )
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});

})();
