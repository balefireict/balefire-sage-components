(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/process-steps",
    "title": "Process Steps",
    "category": "balefire",
    "icon": "editor-ol",
    "description": "Numbered steps with coloured discs — a vertical rail or a horizontal bubble chain — beside or beneath an eyebrow, heading and intro that accepts inner blocks.",
    "keywords": ["steps", "process", "how it works", "timeline", "numbered", "balefire"],
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
        "layout": { "type": "string", "default": "stack" },
        "direction": { "type": "string", "default": "vertical" },
        "eyebrow": { "type": "string", "default": "" },
        "eyebrowVariant": { "type": "string", "default": "marks" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "palette": { "type": "string", "default": "" },
        "numberStyle": { "type": "string", "default": "disc" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-process-steps-editor",
    "style": "balefire-process-steps"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
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
const EYEBROW_VARIANTS = [
    { label: __('Marks', 'balefire'), value: 'marks' },
    { label: __('Bar', 'balefire'), value: 'bar' },
    { label: __('Plain', 'balefire'), value: 'plain' },
];
const DARK_TONES = ['primary', 'dark', 'accent'];

const EMPTY_ITEM = { title: '', text: '', color: '' };

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const isDark = DARK_TONES.includes(attributes.tone);
        const isSplit = attributes.layout === 'split';
        const isHorizontal = attributes.direction === 'horizontal';

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
            className: 'bma-editor-preview bma-process-steps',
            style: {
                background: isDark ? '#1f2328' : '#fff',
                color: isDark ? '#fff' : '#171717',
                padding: '32px',
                border: '1px solid #e0e0e0',
            },
        });

        // The intro column's InnerBlocks slot (rendered after the intro copy,
        // before the buttons on the frontend).
        const innerBlocksProps = useInnerBlocksProps(
            { className: 'bma-process-steps__inner', style: { marginTop: '12px' } },
            { templateLock: false, renderAppender: InnerBlocks.ButtonBlockAppender }
        );

        const palette = String(attributes.palette || '').split(',').map((c) => c.trim()).filter(Boolean);
        const discColor = (item, i) => item.color || (palette.length ? palette[i % palette.length] : '#747474');

        const stepPreview = (item, i) => el('div', {
            key: i,
            style: isHorizontal
                ? { display: 'flex', flexDirection: 'column', alignItems: 'center', textAlign: 'center', gap: '6px', flex: '1 1 120px' }
                : { display: 'flex', gap: '12px', alignItems: 'flex-start' },
        },
            el('span', {
                style: {
                    display: 'grid', placeItems: 'center', flex: 'none',
                    width: isHorizontal ? '48px' : '36px', height: isHorizontal ? '48px' : '36px',
                    borderRadius: '999px', fontWeight: 700,
                    background: attributes.numberStyle === 'plain' ? 'transparent' : discColor(item, i),
                    color: attributes.numberStyle === 'plain' ? discColor(item, i) : '#fff',
                },
            }, i + 1),
            el('div', null,
                el('strong', { style: { display: 'block' } }, item.title || __('Step title', 'balefire')),
                item.text ? el('span', { style: { fontSize: '12px', opacity: 0.75 } }, item.text) : null
            )
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Layout', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'white',
                        options: TONES,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Layout', 'balefire'),
                        value: attributes.layout || 'stack',
                        options: [
                            { label: __('Stack (intro above, centered)', 'balefire'), value: 'stack' },
                            { label: __('Split (intro left, steps right)', 'balefire'), value: 'split' },
                        ],
                        onChange: (value) => setAttributes({ layout: value }),
                    }),
                    el(SelectControl, {
                        label: __('Direction', 'balefire'),
                        value: attributes.direction || 'vertical',
                        options: [
                            { label: __('Vertical rail', 'balefire'), value: 'vertical' },
                            { label: __('Horizontal chain', 'balefire'), value: 'horizontal' },
                        ],
                        onChange: (value) => setAttributes({ direction: value }),
                    }),
                    el(SelectControl, {
                        label: __('Number style', 'balefire'),
                        value: attributes.numberStyle || 'disc',
                        options: [
                            { label: __('Coloured disc', 'balefire'), value: 'disc' },
                            { label: __('Plain number', 'balefire'), value: 'plain' },
                        ],
                        onChange: (value) => setAttributes({ numberStyle: value }),
                    }),
                    el(TextControl, {
                        label: __('Palette', 'balefire'),
                        help: __('Comma-separated CSS colours cycled over steps that have no colour of their own. Leave blank to use the accent colour for all.', 'balefire'),
                        value: attributes.palette || '',
                        onChange: (value) => setAttributes({ palette: value }),
                    })
                ),
                el(PanelBody, { title: __('Intro', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(SelectControl, {
                        label: __('Eyebrow style', 'balefire'),
                        value: attributes.eyebrowVariant || 'marks',
                        options: EYEBROW_VARIANTS,
                        onChange: (value) => setAttributes({ eyebrowVariant: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Title', 'balefire'),
                        help: __('Wrap the accent clause in <em>…</em>.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro copy', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    })
                ),

                items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: (index + 1) + ' — ' + (item.title || __('Untitled', 'balefire')),
                    initialOpen: false,
                },
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: item.title || '',
                        onChange: (value) => updateItem(index, { title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Text', 'balefire'),
                        rows: 4,
                        value: item.text || '',
                        onChange: (value) => updateItem(index, { text: value }),
                    }),
                    el(TextControl, {
                        label: __('Disc colour', 'balefire'),
                        help: __('Any CSS colour (hex, rgb(), var(--token)). Blank = palette, then accent.', 'balefire'),
                        value: item.color || '',
                        onChange: (value) => updateItem(index, { color: value }),
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

                el(PanelBody, { title: __('Add step', 'balefire'), initialOpen: items.length === 0 },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add step', 'balefire'))
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
                el('div', {
                    style: isSplit
                        ? { display: 'grid', gridTemplateColumns: '5fr 7fr', gap: '32px', alignItems: 'start' }
                        : { display: 'flex', flexDirection: 'column', gap: '24px' },
                },
                    el('div', { style: isSplit ? {} : { textAlign: 'center', maxWidth: '640px', margin: '0 auto', width: '100%' } },
                        attributes.eyebrow ? el('p', { style: { margin: '0 0 6px', fontSize: '12px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', opacity: 0.8 } }, attributes.eyebrow) : null,
                        el('h2', { style: { margin: 0, fontSize: '26px', lineHeight: 1.15 } },
                            attributes.title ? attributes.title.replace(/<[^>]+>/g, '') : __('Process Steps', 'balefire')),
                        attributes.content ? el('p', { style: { margin: '10px 0 0', fontSize: '14px', opacity: 0.8 } }, attributes.content) : null,
                        el('div', innerBlocksProps),
                        (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                            style: { marginTop: '16px', display: 'flex', gap: '8px', flexWrap: 'wrap', justifyContent: isSplit ? 'flex-start' : 'center' },
                        },
                            attributes.primaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '8px', background: '#171717', color: '#fff', fontSize: '13px', fontWeight: 700 } }, attributes.primaryLabel) : null,
                            attributes.secondaryLabel ? el('span', { style: { padding: '8px 16px', borderRadius: '8px', border: '1px solid currentColor', fontSize: '13px', fontWeight: 700 } }, attributes.secondaryLabel) : null
                        ) : null
                    ),
                    el('div', {
                        style: isHorizontal
                            ? { display: 'flex', flexWrap: 'wrap', gap: '16px', justifyContent: 'center' }
                            : { display: 'flex', flexDirection: 'column', gap: '16px', maxWidth: '560px', margin: isSplit ? 0 : '0 auto', width: '100%' },
                    },
                        items.length === 0
                            ? el('em', { style: { opacity: 0.7 } }, __('No steps yet — add one from the block sidebar.', 'balefire'))
                            : items.map(stepPreview)
                    )
                )
            )
        );
    },

    // The intro slot's inner blocks are serialised; PHP renders the frame.
    save: () => el(InnerBlocks.Content),
});
})();
