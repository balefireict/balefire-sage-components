(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/reviews",
    "title": "Reviews",
    "category": "balefire",
    "icon": "format-quote",
    "description": "Testimonials carousel. Cards clamp to three lines; the ellipsis opens the full review in a lightbox.",
    "keywords": ["reviews", "testimonials", "quotes", "carousel", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "Reviews From The Field" },
        "title": { "type": "string", "default": "Trusted by Shooters, Hunters, and Professionals" },
        "count": { "type": "number", "default": 9 },
        "orderby": { "type": "string", "default": "date" },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-reviews-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, RangeControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const ORDER_OPTIONS = [
    { label: __('Newest first', 'balefire'), value: 'date' },
    { label: __('Random', 'balefire'), value: 'rand' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-reviews',
            style: { background: '#f4f4f4', padding: '32px', textAlign: 'center' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    })
                ),
                el(PanelBody, { title: __('Reviews', 'balefire'), initialOpen: true },
                    el(RangeControl, {
                        label: __('How many', 'balefire'),
                        help: __('Pulled from Reviews. Three show at a time; the rest are reachable with the arrows.', 'balefire'),
                        value: Number(attributes.count) || 9,
                        min: 1,
                        max: 24,
                        onChange: (value) => setAttributes({ count: value }),
                    }),
                    el(SelectControl, {
                        label: __('Order', 'balefire'),
                        value: attributes.orderby || 'date',
                        options: ORDER_OPTIONS,
                        onChange: (value) => setAttributes({ orderby: value }),
                    })
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                    attributes.eyebrow || ''),
                el('h2', { style: { margin: '0 0 24px', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.1 } },
                    attributes.title || __('Reviews', 'balefire')),
                el('div', { style: { display: 'flex', gap: '16px', justifyContent: 'center' } },
                    [0, 1, 2].map((i) => el('div', {
                        key: i,
                        style: {
                            flex: '1 1 0',
                            maxWidth: '260px',
                            background: '#fff',
                            borderRadius: '8px',
                            padding: '16px',
                            textAlign: 'left',
                        },
                    },
                        el('span', { style: { color: '#d72b27', fontSize: '20px', fontWeight: 700 } }, '“'),
                        el('p', { style: { color: '#2e2e2e', fontSize: '13px', margin: '8px 0' } },
                            __('Review text, clamped to three lines…', 'balefire')),
                        el('p', { style: { fontWeight: 600, fontSize: '13px', margin: 0 } }, __('Reviewer name', 'balefire'))
                    ))
                ),
                el('p', { style: { color: '#747474', fontSize: '12px', marginBottom: 0 } },
                    __('Live reviews render on the front end.', 'balefire'))
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
