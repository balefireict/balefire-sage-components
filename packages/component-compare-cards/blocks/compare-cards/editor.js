(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/compare-cards",
    "title": "Compare Cards",
    "category": "balefire",
    "icon": "columns",
    "description": "Side-by-side A/B comparison cards (e.g. Authentic vs Counterfeit) with check/cross cells.",
    "keywords": [
        "compare",
        "versus",
        "authentic",
        "counterfeit",
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
            "default": "grey"
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
        "leftLabel": {
            "type": "string",
            "default": "Authentic"
        },
        "rightLabel": {
            "type": "string",
            "default": "Counterfeit"
        },
        "items": {
            "type": "array",
            "default": [],
            "items": {
                "type": "object"
            }
        }
    },
    "editorScript": "balefire-compare-cards-editor"
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
            className: 'bma-editor-preview bma-compare-cards',
            style: { background: attributes.tone === 'grey' ? '#f4f4f4' : '#ffffff', padding: '32px', border: '1px solid #e0e0e0' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: 'Compare Cards', initialOpen: true },
                    el(SelectControl, { label: 'Tone', value: attributes.tone || 'grey', options: [{ label: 'grey', value: 'grey' }, { label: 'white', value: 'white' }], onChange: (v) => setAttributes({ tone: v }) }),
                    el(TextControl, { label: 'Eyebrow', value: attributes.eyebrow || '', onChange: (v) => setAttributes({ eyebrow: v }) }),
                    el(TextControl, { label: 'Title', value: attributes.title || '', onChange: (v) => setAttributes({ title: v }) }),
                    el(TextareaControl, { label: 'Content', value: attributes.content || '', onChange: (v) => setAttributes({ content: v }) }),
                    el(TextControl, { label: 'LeftLabel', value: attributes.leftLabel || '', onChange: (v) => setAttributes({ leftLabel: v }) }),
                    el(TextControl, { label: 'RightLabel', value: attributes.rightLabel || '', onChange: (v) => setAttributes({ rightLabel: v }) })
                ),
                el(PanelBody, { title: 'Items', initialOpen: true },
                    ...items.map((item, index) =>
                        el('div', { key: index, style: { border: '1px solid #ddd', borderRadius: '4px', padding: '12px', marginBottom: '12px' } },
                            el(TextControl, { label: 'Title', value: item.title || '', onChange: (v) => updateItem(index, { title: v }) }), el(TextareaControl, { label: 'Left', value: item.left || '', onChange: (v) => updateItem(index, { left: v }) }), el(TextareaControl, { label: 'Right', value: item.right || '', onChange: (v) => updateItem(index, { right: v }) }),
                            el('div', { style: { display: 'flex', gap: '8px' } },
                                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, -1) }, '↑'),
                                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, 1) }, '↓'),
                                el(Button, { size: 'small', variant: 'secondary', isDestructive: true, onClick: () => removeItem(index) }, 'Remove')
                            )
                        )
                    ),
                    el(Button, { variant: 'primary', onClick: () => setAttributes({ items: [...items, { title: '', left: '', right: '' }] }) }, 'Add Item')
                )
            ),
            el('div', blockProps,
                el('p', { style: { margin: 0, fontFamily: 'monospace', fontSize: '11px', textTransform: 'uppercase', color: '#d72b27', fontWeight: 700 } }, 'Compare Cards'),
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
