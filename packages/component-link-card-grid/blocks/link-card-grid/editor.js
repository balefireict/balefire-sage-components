(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/link-card-grid",
    "title": "Link Card Grid",
    "category": "balefire",
    "icon": "grid-view",
    "description": "'Related Guides' section: eyebrow, uppercase heading, and a responsive grid of arrow link cards.",
    "keywords": ["links", "related", "guides", "cards", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full"]
    },
    "attributes": {
        "tone": { "type": "string", "default": "grey" },
        "align": { "type": "string", "default": "full" },
        "eyebrow": { "type": "string", "default": "Keep Reading" },
        "title": { "type": "string", "default": "Related Guides" },
        "columns": { "type": "number", "default": 3 },
        "items": { "type": "array", "default": [], "items": { "type": "object" } }
    },
    "editorScript": "balefire-link-card-grid-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];

        const updateItem = (index, patch) => {
            const next = items.map((item, i) => (i === index ? { ...item, ...patch } : item));
            setAttributes({ items: next });
        };

        const removeItem = (index) => {
            setAttributes({ items: items.filter((item, i) => i !== index) });
        };

        const moveItem = (index, delta) => {
            const target = index + delta;
            if (target < 0 || target >= items.length) return;
            const next = [...items];
            [next[index], next[target]] = [next[target], next[index]];
            setAttributes({ items: next });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-link-card-grid',
            style: {
                background: attributes.tone === 'white' ? '#ffffff' : '#f4f4f4',
                padding: '40px',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section Settings', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'grey',
                        options: [
                            { label: __('Grey', 'balefire'), value: 'grey' },
                            { label: __('White', 'balefire'), value: 'white' },
                        ],
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Columns (desktop)', 'balefire'),
                        value: String(attributes.columns || 3),
                        options: [
                            { label: '2', value: '2' },
                            { label: '3', value: '3' },
                        ],
                        onChange: (value) => setAttributes({ columns: parseInt(value, 10) || 3 }),
                    })
                ),
                el(PanelBody, { title: __('Links', 'balefire'), initialOpen: true },
                    ...items.map((item, index) =>
                        el('div', {
                            key: index,
                            style: { border: '1px solid #ddd', borderRadius: '4px', padding: '12px', marginBottom: '12px' },
                        },
                            el(TextControl, {
                                label: __('Label', 'balefire'),
                                value: item.label || '',
                                onChange: (value) => updateItem(index, { label: value }),
                            }),
                            el(TextControl, {
                                label: __('URL', 'balefire'),
                                value: item.url || '',
                                onChange: (value) => updateItem(index, { url: value }),
                            }),
                            el('div', { style: { display: 'flex', gap: '8px' } },
                                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, -1) }, '↑'),
                                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, 1) }, '↓'),
                                el(Button, { size: 'small', variant: 'secondary', isDestructive: true, onClick: () => removeItem(index) }, __('Remove', 'balefire'))
                            )
                        )
                    ),
                    el(Button, {
                        variant: 'primary',
                        onClick: () => setAttributes({ items: [...items, { label: '', url: '' }] }),
                    }, __('Add Link', 'balefire'))
                )
            ),
            el('div', blockProps,
                attributes.eyebrow ? el('p', {
                    style: { color: '#d72b27', textTransform: 'uppercase', fontSize: '12px', fontWeight: 700, letterSpacing: '0.16em', margin: '0 0 12px' },
                }, attributes.eyebrow) : null,
                attributes.title ? el('h2', {
                    style: { textTransform: 'uppercase', fontSize: '28px', lineHeight: 1.05, margin: '0 0 20px', color: '#171717' },
                }, attributes.title) : null,
                el('div', {
                    style: {
                        display: 'grid',
                        gridTemplateColumns: 'repeat(' + (attributes.columns === 2 ? 2 : 3) + ', 1fr)',
                        gap: '16px',
                    },
                },
                    (items.length ? items : [{ label: __('Add links in the sidebar →', 'balefire') }]).map((item, index) =>
                        el('div', {
                            key: index,
                            style: {
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                gap: '12px',
                                background: '#fff',
                                border: '1px solid #e8e8e8',
                                borderRadius: '8px',
                                padding: '20px',
                            },
                        },
                            el('span', {
                                style: { textTransform: 'uppercase', fontWeight: 700, fontSize: '13px', color: '#2e2e2e' },
                            }, item.label || __('(untitled)', 'balefire')),
                            el('span', { style: { color: '#d72b27' } }, '→')
                        )
                    )
                )
            )
        );
    },
    save: () => null,
});

})();
