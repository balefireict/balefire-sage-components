(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/highlight-banner",
    "title": "Highlight Banner",
    "category": "balefire",
    "icon": "tag",
    "description": "Icon + title + copy + optional CTA in a tinted or card band.",
    "keywords": [
        "banner",
        "highlight",
        "callout",
        "cta",
        "balefire"
    ],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "full"
        ]
    },
    "attributes": {
        "align": {
            "type": "string",
            "default": "full"
        },
        "tone": {
            "type": "string",
            "default": "white"
        },
        "variant": {
            "type": "string",
            "default": "tint"
        },
        "title": {
            "type": "string",
            "default": ""
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "ctaLabel": {
            "type": "string",
            "default": ""
        },
        "ctaUrl": {
            "type": "string",
            "default": ""
        }
    },
    "editorScript": "balefire-highlight-banner-editor"
};

const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const updateItem = (index, patch) => setAttributes({ items: items.map((it, i) => (i === index ? { ...it, ...patch } : it)) });
        const removeItem = (index) => setAttributes({ items: items.filter((it, i) => i !== index) });
        const moveItem = (index, delta) => {
            const t = index + delta;
            if (t < 0 || t >= items.length) return;
            const next = [...items];
            [next[index], next[t]] = [next[t], next[index]];
            setAttributes({ items: next });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-highlight-banner',
            style: { background: attributes.tone === 'grey' ? '#f4f4f4' : '#ffffff', padding: '32px', border: '1px solid #e0e0e0' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: 'Highlight Banner', initialOpen: true },
                    el(SelectControl, { label: 'Tone', value: attributes.tone || 'white', options: [{ label: 'white', value: 'white' }, { label: 'grey', value: 'grey' }], onChange: (v) => setAttributes({ tone: v }) }),
                    el(SelectControl, { label: 'Variant', value: attributes.variant || 'tint', options: [{ label: 'tint', value: 'tint' }, { label: 'card', value: 'card' }], onChange: (v) => setAttributes({ variant: v }) }),
                    el(TextControl, { label: 'Title', value: attributes.title || '', onChange: (v) => setAttributes({ title: v }) }),
                    el(TextareaControl, { label: 'Content', value: attributes.content || '', onChange: (v) => setAttributes({ content: v }) }),
                    el(TextControl, { label: 'CtaLabel', value: attributes.ctaLabel || '', onChange: (v) => setAttributes({ ctaLabel: v }) }),
                    el(TextControl, { label: 'CtaUrl', value: attributes.ctaUrl || '', onChange: (v) => setAttributes({ ctaUrl: v }) })
                )
            ),
            el('div', blockProps,
                el('p', { style: { margin: 0, fontFamily: 'monospace', fontSize: '11px', textTransform: 'uppercase', color: '#d72b27', fontWeight: 700 } }, 'Highlight Banner'),
                el('h2', { style: { margin: '6px 0 0', textTransform: 'uppercase', fontSize: '24px', color: '#171717' } }, attributes.title || '(untitled)'),
                attributes.content ? el('p', { style: { margin: '10px 0 0', fontSize: '14px', color: '#2e2e2e' } }, attributes.content) : null,
                items.length ? el('p', { style: { margin: '10px 0 0', fontSize: '12px', color: '#747474' } }, items.length + ' item(s) — edit in the sidebar') : null,
                null
            )
        );
    },
    save: () => null,
});

})();
