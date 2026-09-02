(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/stat-band",
    "title": "Stat Band",
    "category": "balefire",
    "icon": "chart-bar",
    "description": "Toned band with a lead paragraph across the top and 2–4 cells of icon, big value with optional unit, and label. Full-bleed or wrapped in a rounded card, with an optional buttons row.",
    "keywords": ["stats", "numbers", "impact", "band", "metrics", "balefire"],
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
        "tone": { "type": "string", "default": "primary" },
        "lead": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "columns": { "type": "number", "default": 4 },
        "radius": { "type": "string", "default": "none" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-stat-band-editor",
    "style": "balefire-stat-band"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Same list as SectionStyles::tones() in component-support.
const TONES = [
    { label: __('White', 'balefire'), value: 'white' },
    { label: __('Light', 'balefire'), value: 'light' },
    { label: __('Surface', 'balefire'), value: 'surface' },
    { label: __('Surface (muted)', 'balefire'), value: 'surface-muted' },
    { label: __('Primary', 'balefire'), value: 'primary' },
    { label: __('Secondary', 'balefire'), value: 'secondary' },
    { label: __('Accent', 'balefire'), value: 'accent' },
    { label: __('Dark', 'balefire'), value: 'dark' },
];
const DARK_TONES = ['primary', 'dark', 'accent'];

const EMPTY_ITEM = { value: '', unit: '', label: '', iconSvg: '', iconId: 0 };

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const isDark = DARK_TONES.includes(attributes.tone);
        const columns = [2, 3, 4].includes(Number(attributes.columns)) ? Number(attributes.columns) : 4;

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
            className: 'bma-editor-preview bma-stat-band',
            style: {
                background: attributes.radius === 'card' ? '#f4f4f4' : (isDark ? '#1f2328' : '#fff'),
                color: isDark ? '#fff' : '#171717',
                padding: '32px',
                border: '1px solid #e0e0e0',
            },
        });

        const bandStyle = attributes.radius === 'card'
            ? { background: isDark ? '#1f2328' : '#fff', borderRadius: '16px', padding: '24px', color: isDark ? '#fff' : '#171717' }
            : {};

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Band', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'primary',
                        options: TONES,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Shape', 'balefire'),
                        value: attributes.radius || 'none',
                        options: [
                            { label: __('Full-bleed band', 'balefire'), value: 'none' },
                            { label: __('Rounded card inside the container', 'balefire'), value: 'card' },
                        ],
                        onChange: (value) => setAttributes({ radius: value }),
                    }),
                    el(SelectControl, {
                        label: __('Columns', 'balefire'),
                        value: String(columns),
                        options: [
                            { label: '2', value: '2' },
                            { label: '3', value: '3' },
                            { label: '4', value: '4' },
                        ],
                        onChange: (value) => setAttributes({ columns: Number(value) }),
                    }),
                    el(TextareaControl, {
                        label: __('Lead paragraph', 'balefire'),
                        help: __('Runs across the top of the band. Basic HTML (strong, em, a) is allowed.', 'balefire'),
                        value: attributes.lead || '',
                        onChange: (value) => setAttributes({ lead: value }),
                    })
                ),

                items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: (index + 1) + ' — ' + (item.value || item.label || __('Untitled', 'balefire')),
                    initialOpen: false,
                },
                    el(TextControl, {
                        label: __('Value', 'balefire'),
                        value: item.value || '',
                        onChange: (value) => updateItem(index, { value }),
                    }),
                    el(TextControl, {
                        label: __('Unit', 'balefire'),
                        help: __('Optional suffix shown in the highlight colour, e.g. + or %.', 'balefire'),
                        value: item.unit || '',
                        onChange: (value) => updateItem(index, { unit: value }),
                    }),
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: item.label || '',
                        onChange: (value) => updateItem(index, { label: value }),
                    }),
                    el(MediaUpload, {
                        onSelect: (media) => updateItem(index, { iconId: media.id || 0 }),
                        allowedTypes: ['image'],
                        value: item.iconId,
                        render: ({ open }) => el(Button, {
                            variant: 'secondary',
                            onClick: open,
                        }, item.iconId
                            ? __('Change icon image', 'balefire')
                            : __('Set icon image', 'balefire')),
                    }),
                    item.iconId ? el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        onClick: () => updateItem(index, { iconId: 0 }),
                    }, __('Remove icon image', 'balefire')) : null,
                    el(TextareaControl, {
                        label: __('Icon SVG', 'balefire'),
                        help: __('Inline <svg> markup, used when no icon image is set. Use fill="currentColor" so it takes the highlight colour.', 'balefire'),
                        rows: 4,
                        value: item.iconSvg || '',
                        onChange: (value) => updateItem(index, { iconSvg: value }),
                    }),
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

                el(PanelBody, { title: __('Add stat', 'balefire'), initialOpen: items.length === 0 },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add stat', 'balefire'))
                ),

                el(PanelBody, { title: __('Primary Button', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.primaryLabel || '',
                        onChange: (value) => setAttributes({ primaryLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.primaryUrl || '',
                        onChange: (value) => setAttributes({ primaryUrl: value }),
                    })
                ),
                el(PanelBody, { title: __('Secondary Button', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.secondaryLabel || '',
                        onChange: (value) => setAttributes({ secondaryLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.secondaryUrl || '',
                        onChange: (value) => setAttributes({ secondaryUrl: value }),
                    })
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('div', { style: bandStyle },
                    el('p', { style: { margin: '0 0 12px', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', opacity: 0.7 } }, __('Stat Band', 'balefire')),
                    attributes.lead ? el('p', { style: { margin: '0 0 16px', fontSize: '15px', opacity: 0.9 } }, attributes.lead.replace(/<[^>]+>/g, '')) : null,
                    el('div', {
                        style: { display: 'grid', gridTemplateColumns: 'repeat(' + columns + ', minmax(0, 1fr))', gap: '12px', borderTop: '1px solid rgba(128,128,128,0.3)', paddingTop: '16px' },
                    },
                        items.length === 0
                            ? el('em', { style: { opacity: 0.7, gridColumn: '1 / -1' } }, __('No stats yet — add one from the block sidebar.', 'balefire'))
                            : items.map((item, i) => el('div', { key: i },
                                (item.iconId || item.iconSvg) ? el('span', { style: { display: 'block', fontSize: '11px', opacity: 0.6, marginBottom: '6px' } }, __('icon', 'balefire')) : null,
                                el('strong', { style: { display: 'block', fontSize: '28px', lineHeight: 1 } },
                                    (item.value || '—') + (item.unit || '')),
                                el('span', { style: { display: 'block', fontSize: '12px', opacity: 0.8, marginTop: '6px' } }, item.label || __('Label', 'balefire'))
                            ))
                    )
                ),
                (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                    style: { marginTop: '16px', display: 'flex', gap: '8px', flexWrap: 'wrap', justifyContent: 'center' },
                },
                    attributes.primaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '8px', background: '#171717', color: '#fff', fontSize: '13px', fontWeight: 700 } }, attributes.primaryLabel) : null,
                    attributes.secondaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '8px', border: '1px solid currentColor', fontSize: '13px', fontWeight: 700 } }, attributes.secondaryLabel) : null
                ) : null
            )
        );
    },
    save: () => null,
});
})();
