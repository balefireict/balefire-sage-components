(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/info-cards",
    "title": "Info Cards",
    "category": "balefire",
    "icon": "index-card",
    "description": "Guide-page section with heading, intro, and a grid of check-icon or numbered-step cards with bold lead-ins.",
    "keywords": ["cards", "steps", "checklist", "grid", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full"]
    },
    "attributes": {
        "align": { "type": "string", "default": "full" },
        "tone": { "type": "string", "default": "white" },
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "variant": { "type": "string", "default": "check" },
        "columns": { "type": "number", "default": 3 },
        "items": { "type": "array", "default": [], "items": { "type": "object" } }
    },
    "editorScript": "balefire-info-cards-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const numbered = attributes.variant === 'numbered';

        const updateItem = (index, patch) => {
            setAttributes({ items: items.map((item, i) => (i === index ? { ...item, ...patch } : item)) });
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
            className: 'bma-editor-preview bma-info-cards',
            style: {
                background: attributes.tone === 'grey' ? '#f4f4f4' : '#ffffff',
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
                    el(TextareaControl, {
                        label: __('Intro', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'white',
                        options: [
                            { label: __('White', 'balefire'), value: 'white' },
                            { label: __('Grey', 'balefire'), value: 'grey' },
                        ],
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Card style', 'balefire'),
                        value: attributes.variant || 'check',
                        options: [
                            { label: __('Check icon', 'balefire'), value: 'check' },
                            { label: __('Numbered steps', 'balefire'), value: 'numbered' },
                        ],
                        onChange: (value) => setAttributes({ variant: value }),
                    }),
                    el(SelectControl, {
                        label: __('Columns (desktop)', 'balefire'),
                        value: String(attributes.columns || 3),
                        options: [
                            { label: '2', value: '2' },
                            { label: '3', value: '3' },
                            { label: '4', value: '4' },
                        ],
                        onChange: (value) => setAttributes({ columns: parseInt(value, 10) || 3 }),
                    })
                ),
                el(PanelBody, { title: __('Cards', 'balefire'), initialOpen: true },
                    ...items.map((item, index) =>
                        el('div', {
                            key: index,
                            style: { border: '1px solid #ddd', borderRadius: '4px', padding: '12px', marginBottom: '12px' },
                        },
                            el(TextControl, {
                                label: numbered
                                    ? __('Step lead-in (optional)', 'balefire')
                                    : __('Lead-in (optional)', 'balefire'),
                                value: item.lead || '',
                                onChange: (value) => updateItem(index, { lead: value }),
                            }),
                            el(TextareaControl, {
                                label: __('Text', 'balefire'),
                                value: item.text || '',
                                onChange: (value) => updateItem(index, { text: value }),
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
                        onClick: () => setAttributes({ items: [...items, { lead: '', text: '' }] }),
                    }, __('Add Card', 'balefire'))
                )
            ),
            el('div', blockProps,
                attributes.eyebrow ? el('p', {
                    style: { color: '#d72b27', textTransform: 'uppercase', fontSize: '12px', fontWeight: 700, letterSpacing: '0.16em', margin: '0 0 12px' },
                }, attributes.eyebrow) : null,
                attributes.title ? el('h2', {
                    style: { textTransform: 'uppercase', fontSize: '28px', lineHeight: 1.05, margin: '0 0 12px', color: '#171717' },
                }, attributes.title) : null,
                attributes.content ? el('p', {
                    style: { color: '#2e2e2e', margin: '0 0 20px', fontSize: '15px' },
                }, attributes.content) : null,
                el('div', {
                    style: {
                        display: 'grid',
                        gridTemplateColumns: 'repeat(' + Math.min(attributes.columns || 3, 4) + ', 1fr)',
                        gap: '16px',
                    },
                },
                    (items.length ? items : [{ text: __('Add cards in the sidebar →', 'balefire') }]).map((item, index) =>
                        el('div', {
                            key: index,
                            style: {
                                background: '#fff',
                                border: '1px solid #e8e8e8',
                                borderRadius: '8px',
                                padding: '20px',
                                boxShadow: '0 6px 20px rgba(0,0,0,0.05)',
                            },
                        },
                            el('span', {
                                style: numbered
                                    ? { fontFamily: 'monospace', fontSize: '24px', fontWeight: 700, color: 'rgba(215,43,39,0.25)' }
                                    : { color: '#d72b27' },
                            }, numbered ? String(index + 1).padStart(2, '0') : '✓'),
                            el('p', { style: { margin: '8px 0 0', fontSize: '13px', color: '#2e2e2e' } },
                                item.lead ? el('strong', { style: { textTransform: 'uppercase', color: '#171717', marginRight: '4px' } }, item.lead) : null,
                                item.text || ''
                            )
                        )
                    )
                )
            )
        );
    },
    save: () => null,
});

})();
