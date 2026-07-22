(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/split-feature",
    "title": "Split Feature",
    "category": "balefire",
    "icon": "align-pull-right",
    "description": "Two-column guide-page section: eyebrow/title/copy/buttons beside an image, stat card, or nested content (tables).",
    "keywords": [
        "split",
        "feature",
        "stat",
        "table",
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
        "eyebrow": {
            "type": "string",
            "default": ""
        },
        "title": {
            "type": "string",
            "default": ""
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "primaryLabel": {
            "type": "string",
            "default": ""
        },
        "primaryUrl": {
            "type": "string",
            "default": ""
        },
        "secondaryLabel": {
            "type": "string",
            "default": ""
        },
        "secondaryUrl": {
            "type": "string",
            "default": ""
        },
        "mediaType": {
            "type": "string",
            "default": "content"
        },
        "mediaSide": {
            "type": "string",
            "default": "right"
        },
        "imageId": {
            "type": "number",
            "default": 0
        },
        "imageUrl": {
            "type": "string",
            "default": ""
        },
        "imageAlt": {
            "type": "string",
            "default": ""
        },
        "statValue": {
            "type": "string",
            "default": ""
        },
        "statLabel": {
            "type": "string",
            "default": ""
        },
        "statNote": {
            "type": "string",
            "default": ""
        }
    },
    "editorScript": "balefire-split-feature-editor"
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
            className: 'bma-editor-preview bma-split-feature',
            style: { background: attributes.tone === 'grey' ? '#f4f4f4' : '#ffffff', padding: '32px', border: '1px solid #e0e0e0' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: 'Split Feature', initialOpen: true },
                    el(SelectControl, { label: 'Tone', value: attributes.tone || 'white', options: [{ label: 'white', value: 'white' }, { label: 'grey', value: 'grey' }], onChange: (v) => setAttributes({ tone: v }) }),
                    el(TextControl, { label: 'Eyebrow', value: attributes.eyebrow || '', onChange: (v) => setAttributes({ eyebrow: v }) }),
                    el(TextControl, { label: 'Title', value: attributes.title || '', onChange: (v) => setAttributes({ title: v }) }),
                    el(TextareaControl, { label: 'Content', value: attributes.content || '', onChange: (v) => setAttributes({ content: v }) }),
                    el(TextControl, { label: 'PrimaryLabel', value: attributes.primaryLabel || '', onChange: (v) => setAttributes({ primaryLabel: v }) }),
                    el(TextControl, { label: 'PrimaryUrl', value: attributes.primaryUrl || '', onChange: (v) => setAttributes({ primaryUrl: v }) }),
                    el(TextControl, { label: 'SecondaryLabel', value: attributes.secondaryLabel || '', onChange: (v) => setAttributes({ secondaryLabel: v }) }),
                    el(TextControl, { label: 'SecondaryUrl', value: attributes.secondaryUrl || '', onChange: (v) => setAttributes({ secondaryUrl: v }) }),
                    el(SelectControl, { label: 'MediaType', value: attributes.mediaType || 'content', options: [{ label: 'content', value: 'content' }, { label: 'image', value: 'image' }, { label: 'stat', value: 'stat' }], onChange: (v) => setAttributes({ mediaType: v }) }),
                    el(SelectControl, { label: 'MediaSide', value: attributes.mediaSide || 'right', options: [{ label: 'right', value: 'right' }, { label: 'left', value: 'left' }], onChange: (v) => setAttributes({ mediaSide: v }) }),
                    el(MediaUpload, {
                        onSelect: (media) => setAttributes({ imageId: media.id || 0, imageUrl: media.url || '', imageAlt: media.alt || '' }),
                        allowedTypes: ['image'],
                        value: attributes.imageId,
                        render: ({ open }) => el('button', { className: 'components-button is-secondary', onClick: open }, attributes.imageUrl ? 'Change Image' : 'Select Image'),
                    }),
                    el(TextControl, { label: 'StatValue', value: attributes.statValue || '', onChange: (v) => setAttributes({ statValue: v }) }),
                    el(TextControl, { label: 'StatLabel', value: attributes.statLabel || '', onChange: (v) => setAttributes({ statLabel: v }) }),
                    el(TextareaControl, { label: 'StatNote', value: attributes.statNote || '', onChange: (v) => setAttributes({ statNote: v }) })
                )
            ),
            el('div', blockProps,
                el('p', { style: { margin: 0, fontFamily: 'monospace', fontSize: '11px', textTransform: 'uppercase', color: '#d72b27', fontWeight: 700 } }, 'Split Feature'),
                el('h2', { style: { margin: '6px 0 0', textTransform: 'uppercase', fontSize: '24px', color: '#171717' } }, attributes.title || '(untitled)'),
                attributes.content ? el('p', { style: { margin: '10px 0 0', fontSize: '14px', color: '#2e2e2e' } }, attributes.content) : null,
                items.length ? el('p', { style: { margin: '10px 0 0', fontSize: '12px', color: '#747474' } }, items.length + ' item(s) — edit in the sidebar') : null,
                el('div', useInnerBlocksProps({ style: { marginTop: '12px', background: '#fff', border: '1px dashed #bbb', borderRadius: '6px', padding: '12px' } }, { renderAppender: InnerBlocks.ButtonBlockAppender }))
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
