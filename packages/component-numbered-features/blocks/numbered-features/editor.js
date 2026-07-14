(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/numbered-features",
    "title": "Numbered Features",
    "category": "balefire",
    "icon": "editor-ol",
    "description": "Numbered points in a 2-up grid. Hovering a point swaps its number for an image and raises a card background.",
    "keywords": ["features", "numbered", "why", "benefits", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "The B&T Difference" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "ctaLabel": { "type": "string", "default": "" },
        "ctaUrl": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-numbered-features-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

const EMPTY_ITEM = { title: '', text: '', imageId: 0 };

const pad = (n) => String(n).padStart(2, '0');

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];

        const updateItem = (index, changes) => {
            setAttributes({
                items: items.map((item, i) => (i === index ? { ...item, ...changes } : item)),
            });
        };

        const addItem = () => setAttributes({ items: [...items, { ...EMPTY_ITEM }] });
        const removeItem = (index) => setAttributes({ items: items.filter((_, i) => i !== index) });

        const moveItem = (index, delta) => {
            const target = index + delta;
            if (target < 0 || target >= items.length) return;
            const next = [...items];
            [next[index], next[target]] = [next[target], next[index]];
            setAttributes({ items: next });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-numbered-features',
            style: { background: '#fff', padding: '32px', border: '1px solid #e8e8e8' },
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
                    }),
                    el(TextareaControl, {
                        label: __('Description', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(TextControl, {
                        label: __('CTA label', 'balefire'),
                        value: attributes.ctaLabel || '',
                        onChange: (value) => setAttributes({ ctaLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('CTA URL', 'balefire'),
                        type: 'url',
                        value: attributes.ctaUrl || '',
                        onChange: (value) => setAttributes({ ctaUrl: value }),
                    })
                ),

                items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: pad(index + 1) + ' — ' + (item.title || __('Untitled', 'balefire')),
                    initialOpen: false,
                },
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: item.title || '',
                        onChange: (value) => updateItem(index, { title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Text', 'balefire'),
                        rows: 5,
                        value: item.text || '',
                        onChange: (value) => updateItem(index, { text: value }),
                    }),
                    el(MediaUpload, {
                        onSelect: (media) => updateItem(index, { imageId: media.id || 0 }),
                        allowedTypes: ['image'],
                        value: item.imageId,
                        render: ({ open }) => el(Button, {
                            variant: 'secondary',
                            onClick: open,
                        }, item.imageId
                            ? __('Change hover image', 'balefire')
                            : __('Set hover image', 'balefire')),
                    }),
                    el('p', { style: { fontSize: '12px', color: '#757575', marginTop: '4px' } },
                        __('Shown in place of the number while the point is hovered. Square crops work best.', 'balefire')),
                    item.imageId ? el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        onClick: () => updateItem(index, { imageId: 0 }),
                    }, __('Remove image', 'balefire')) : null,
                    el('div', { style: { display: 'flex', gap: '8px', marginTop: '12px' } },
                        el(Button, {
                            variant: 'secondary',
                            disabled: index === 0,
                            onClick: () => moveItem(index, -1),
                        }, __('Move up', 'balefire')),
                        el(Button, {
                            variant: 'secondary',
                            disabled: index === items.length - 1,
                            onClick: () => moveItem(index, 1),
                        }, __('Move down', 'balefire')),
                        el(Button, {
                            variant: 'tertiary',
                            isDestructive: true,
                            onClick: () => removeItem(index),
                        }, __('Remove', 'balefire'))
                    )
                )),

                el(PanelBody, { title: __('Add point', 'balefire'), initialOpen: items.length === 0 },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add point', 'balefire'))
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                    attributes.eyebrow || ''),
                el('h2', { style: { margin: '0 0 16px', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.1 } },
                    attributes.title || __('Numbered Features', 'balefire')),
                el('div', {
                    style: { display: 'grid', gridTemplateColumns: 'repeat(2, minmax(0, 1fr))', gap: '8px' },
                },
                    items.length === 0
                        ? el('em', { style: { color: '#747474' } }, __('No points yet — add one from the block sidebar.', 'balefire'))
                        : items.map((item, i) => el('div', {
                            key: i,
                            style: { display: 'flex', gap: '16px', padding: '16px', borderRadius: '8px', background: i === 0 ? '#f4f4f4' : 'transparent' },
                        },
                            el('span', { style: { fontFamily: 'monospace', fontWeight: 700, fontSize: '20px' } }, pad(i + 1)),
                            el('div', null,
                                el('strong', { style: { display: 'block' } }, item.title || __('Point title', 'balefire')),
                                item.imageId
                                    ? el('span', { style: { fontSize: '11px', color: '#747474' } }, __('hover image set', 'balefire'))
                                    : el('span', { style: { fontSize: '11px', color: '#d72b27' } }, __('no hover image', 'balefire'))
                            )
                        ))
                )
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
