(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/numbered-features",
    "title": "Numbered Features",
    "category": "balefire",
    "icon": "editor-ol",
    "description": "Numbered points as a 2-up hover grid (stack) or an editorial index of rows beside an intro column (split). Rows can link out and carry their own icon and accent.",
    "keywords": ["features", "numbered", "why", "benefits", "services", "index", "balefire"],
    "textdomain": "balefire",
    "version": "1.1.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "The B&T Difference" },
        "eyebrowVariant": { "type": "string", "default": "marks" },
        "title": { "type": "string", "default": "" },
        "titleAccent": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "ctaLabel": { "type": "string", "default": "" },
        "ctaUrl": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "layout": { "type": "string", "default": "stack" },
        "tone": { "type": "string", "default": "white" },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-numbered-features-editor",
    "style": "balefire-numbered-features"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;

// Injected by src/bootstrap.php from Icons::choices().
const ICON_CHOICES = window.balefireNumberedFeatureIcons || { '': 'Number' };
const { createElement: el, Fragment } = wp.element;

const EMPTY_ITEM = { title: '', text: '', url: '', icon: '', iconSvg: '', accentColor: '', imageId: 0 };

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

const LAYOUTS = [
    { label: __('Stack — header above a 2-up hover grid', 'balefire'), value: 'stack' },
    { label: __('Split — intro column beside a list of rows', 'balefire'), value: 'split' },
];

const EYEBROW_VARIANTS = [
    { label: __('Marks (flanking brand marks)', 'balefire'), value: 'marks' },
    { label: __('Bar (short rule before the label)', 'balefire'), value: 'bar' },
    { label: __('Plain (label only)', 'balefire'), value: 'plain' },
];

const pad = (n) => String(n).padStart(2, '0');

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const layout = attributes.layout === 'split' ? 'split' : 'stack';

        // The legacy single CTA reads as the primary button until it is
        // edited; editing writes the new attribute and clears the old one.
        const primaryLabel = attributes.primaryLabel || attributes.ctaLabel || '';
        const primaryUrl = attributes.primaryUrl || attributes.ctaUrl || '';

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

        const isDarkTone = ['primary', 'secondary', 'accent', 'dark'].includes(attributes.tone);

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-numbered-features',
            style: {
                background: isDarkTone ? '#1f2a37' : '#fff',
                color: isDarkTone ? '#fff' : '#1f2a37',
                padding: '32px',
                border: '1px solid #e8e8e8',
            },
        });

        const previewRow = (item, i) => el('div', {
            key: i,
            style: layout === 'split'
                ? { display: 'flex', gap: '16px', alignItems: 'center', padding: '14px 0', borderBottom: '1px solid rgba(128,128,128,.25)' }
                : { display: 'flex', gap: '16px', padding: '16px', borderRadius: '8px', background: i === 0 && !isDarkTone ? '#f4f4f4' : 'transparent' },
        },
            el('span', {
                style: {
                    fontFamily: layout === 'split' ? 'inherit' : 'monospace',
                    fontWeight: 700,
                    fontSize: '20px',
                    color: layout === 'split' ? (item.accentColor || '#d72b27') : 'inherit',
                    minWidth: '2.2rem',
                },
            }, pad(i + 1)),
            layout === 'split' && (item.iconSvg || item.icon) ? el('span', {
                style: { width: '32px', height: '32px', borderRadius: '999px', background: item.accentColor || '#d72b27', flex: 'none' },
            }) : null,
            el('div', { style: { flex: '1 1 auto', minWidth: 0 } },
                el('strong', { style: { display: 'block' } }, item.title || __('Point title', 'balefire')),
                layout === 'split' && item.text
                    ? el('span', { style: { fontSize: '12px', opacity: 0.75 } }, item.text)
                    : null,
                layout === 'stack'
                    ? (item.imageId
                        ? el('span', { style: { fontSize: '11px', color: '#747474' } }, __('hover image set', 'balefire'))
                        : el('span', { style: { fontSize: '11px', color: '#d72b27' } }, __('no hover image', 'balefire')))
                    : null
            ),
            layout === 'split' && item.url ? el('span', { 'aria-hidden': true, style: { color: item.accentColor || '#d72b27' } }, '→') : null
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Layout', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Layout', 'balefire'),
                        value: layout,
                        options: LAYOUTS,
                        onChange: (value) => setAttributes({ layout: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'white',
                        options: TONES,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Eyebrow style', 'balefire'),
                        value: attributes.eyebrowVariant || 'marks',
                        options: EYEBROW_VARIANTS,
                        onChange: (value) => setAttributes({ eyebrowVariant: value }),
                    })
                ),

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
                    el(TextControl, {
                        label: __('Title accent clause', 'balefire'),
                        help: __('Split layout only. Rendered after the title in the italic accent style, e.g. "fits your situation."', 'balefire'),
                        value: attributes.titleAccent || '',
                        onChange: (value) => setAttributes({ titleAccent: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Description', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    })
                ),

                el(PanelBody, { title: __('Buttons', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Primary label', 'balefire'),
                        value: primaryLabel,
                        onChange: (value) => setAttributes({ primaryLabel: value, ctaLabel: '' }),
                    }),
                    el(TextControl, {
                        label: __('Primary URL', 'balefire'),
                        type: 'url',
                        value: primaryUrl,
                        onChange: (value) => setAttributes({ primaryUrl: value, ctaUrl: '' }),
                    }),
                    el(TextControl, {
                        label: __('Secondary label', 'balefire'),
                        value: attributes.secondaryLabel || '',
                        onChange: (value) => setAttributes({ secondaryLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('Secondary URL', 'balefire'),
                        type: 'url',
                        value: attributes.secondaryUrl || '',
                        onChange: (value) => setAttributes({ secondaryUrl: value }),
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
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        type: 'url',
                        help: __('When set, the row becomes a link with a trailing arrow (split layout) or the title links out (stack layout).', 'balefire'),
                        value: item.url || '',
                        onChange: (value) => updateItem(index, { url: value }),
                    }),
                    el(SelectControl, {
                        label: __('Marker', 'balefire'),
                        help: __('Bundled icon shown beside the point. Leave on Number to keep the counted 01, 02, 03 in the stack layout.', 'balefire'),
                        value: item.icon || '',
                        options: Object.keys(ICON_CHOICES).map((slug) => ({
                            label: ICON_CHOICES[slug],
                            value: slug,
                        })),
                        onChange: (value) => updateItem(index, { icon: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Custom icon (inline SVG)', 'balefire'),
                        help: __('Paste <svg>…</svg> markup. Overrides the bundled marker. Only svg, path, circle, rect, g, line, polyline and polygon elements are kept.', 'balefire'),
                        rows: 4,
                        value: item.iconSvg || '',
                        onChange: (value) => updateItem(index, { iconSvg: value }),
                    }),
                    el(TextControl, {
                        label: __('Accent color', 'balefire'),
                        help: __('Any CSS color or var(--token). Colors the number, icon disc and arrow in the split layout. Empty uses the page accent.', 'balefire'),
                        value: item.accentColor || '',
                        onChange: (value) => updateItem(index, { accentColor: value }),
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
                        __('Stack layout only. Revealed in place of the number while the point is hovered, on screens 1024px and up. Square crops work best.', 'balefire')),
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
                el('div', {
                    style: layout === 'split'
                        ? { display: 'grid', gridTemplateColumns: '5fr 7fr', gap: '32px', alignItems: 'start' }
                        : { display: 'flex', flexDirection: 'column', gap: '16px' },
                },
                    el('div', null,
                        el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                            (attributes.eyebrowVariant === 'bar' ? '— ' : '') + (attributes.eyebrow || '')),
                        el('h2', { style: { margin: '0 0 16px', textTransform: layout === 'split' ? 'none' : 'uppercase', fontSize: '32px', lineHeight: 1.1 } },
                            attributes.title || __('Numbered Features', 'balefire'),
                            layout === 'split' && attributes.titleAccent
                                ? el('em', { style: { color: '#d72b27', fontWeight: 500 } }, ' ' + attributes.titleAccent)
                                : null),
                        layout === 'split' && attributes.content
                            ? el('p', { style: { margin: '0 0 16px', opacity: 0.8 } }, attributes.content)
                            : null,
                        (primaryLabel || attributes.secondaryLabel)
                            ? el('div', { style: { display: 'flex', gap: '8px', flexWrap: 'wrap' } },
                                primaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '999px', background: '#d72b27', color: '#fff', fontWeight: 700 } }, primaryLabel) : null,
                                attributes.secondaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '999px', border: '2px solid currentColor', fontWeight: 700 } }, attributes.secondaryLabel) : null)
                            : null
                    ),
                    el('div', {
                        style: layout === 'split'
                            ? { borderTop: '1px solid rgba(128,128,128,.25)' }
                            : { display: 'grid', gridTemplateColumns: 'repeat(2, minmax(0, 1fr))', gap: '8px' },
                    },
                        items.length === 0
                            ? el('em', { style: { color: '#747474' } }, __('No points yet — add one from the block sidebar.', 'balefire'))
                            : items.map(previewRow)
                    )
                )
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
