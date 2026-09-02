(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/timeline",
    "title": "Timeline",
    "category": "balefire",
    "icon": "backup",
    "description": "Eyebrow, heading and intro above a vertical list of labelled entries on an accent rail with dots; optionally beside an image in a 60/40 split.",
    "keywords": ["timeline", "history", "milestones", "years", "story", "balefire"],
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
        "eyebrowVariant": { "type": "string", "default": "marks" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "imageId": { "type": "number", "default": 0 },
        "imageSide": { "type": "string", "default": "right" }
    },
    "editorScript": "balefire-timeline-editor",
    "style": "balefire-timeline"
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
const EYEBROW_VARIANTS = [
    { label: __('Marks', 'balefire'), value: 'marks' },
    { label: __('Bar', 'balefire'), value: 'bar' },
    { label: __('Plain', 'balefire'), value: 'plain' },
];
const DARK_TONES = ['primary', 'dark', 'accent'];

const EMPTY_ITEM = { label: '', text: '' };

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const isDark = DARK_TONES.includes(attributes.tone);
        const hasImage = Number(attributes.imageId) > 0;
        const imageLeft = attributes.imageSide === 'left';

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
            className: 'bma-editor-preview bma-timeline',
            style: {
                background: isDark ? '#1f2328' : '#fff',
                color: isDark ? '#fff' : '#171717',
                padding: '32px',
                border: '1px solid #e0e0e0',
            },
        });

        const list = el('div', { style: { display: 'flex', flexDirection: 'column', gap: '14px', borderLeft: '2px solid rgba(128,128,128,0.35)', paddingLeft: '18px' } },
            items.length === 0
                ? el('em', { style: { opacity: 0.7 } }, __('No entries yet — add one from the block sidebar.', 'balefire'))
                : items.map((item, i) => el('div', { key: i, style: { position: 'relative' } },
                    el('span', { style: { position: 'absolute', left: '-25px', top: '5px', width: '10px', height: '10px', borderRadius: '999px', background: 'currentColor', opacity: 0.6 } }),
                    el('strong', { style: { display: 'block' } }, item.label || __('Label', 'balefire')),
                    item.text ? el('span', { style: { fontSize: '12px', opacity: 0.75 } }, item.text) : null
                ))
        );

        const media = hasImage
            ? el('div', { style: { background: 'rgba(128,128,128,0.2)', borderRadius: '12px', minHeight: '160px', display: 'grid', placeItems: 'center', fontSize: '12px', opacity: 0.8 } },
                __('Image #', 'balefire') + attributes.imageId)
            : null;

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'white',
                        options: TONES,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
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
                el(PanelBody, { title: __('Image', 'balefire'), initialOpen: false },
                    el(MediaUpload, {
                        onSelect: (m) => setAttributes({ imageId: m.id || 0 }),
                        allowedTypes: ['image'],
                        value: attributes.imageId,
                        render: ({ open }) => el(Button, {
                            variant: 'secondary',
                            onClick: open,
                        }, hasImage ? __('Change image', 'balefire') : __('Set image', 'balefire')),
                    }),
                    hasImage ? el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        onClick: () => setAttributes({ imageId: 0 }),
                    }, __('Remove image', 'balefire')) : null,
                    el('p', { style: { fontSize: '12px', color: '#757575', marginTop: '4px' } },
                        __('With an image the timeline sits in a 60/40 split; without one it runs single-column.', 'balefire')),
                    el(SelectControl, {
                        label: __('Image side', 'balefire'),
                        value: attributes.imageSide || 'right',
                        options: [
                            { label: __('Right', 'balefire'), value: 'right' },
                            { label: __('Left', 'balefire'), value: 'left' },
                        ],
                        onChange: (value) => setAttributes({ imageSide: value }),
                    })
                ),

                items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: (index + 1) + ' — ' + (item.label || __('Untitled', 'balefire')),
                    initialOpen: false,
                },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        help: __('A year, date or short heading.', 'balefire'),
                        value: item.label || '',
                        onChange: (value) => updateItem(index, { label: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Text', 'balefire'),
                        rows: 4,
                        value: item.text || '',
                        onChange: (value) => updateItem(index, { text: value }),
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

                el(PanelBody, { title: __('Add entry', 'balefire'), initialOpen: items.length === 0 },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add entry', 'balefire'))
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                attributes.eyebrow ? el('p', { style: { margin: '0 0 6px', fontSize: '12px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', opacity: 0.8 } }, attributes.eyebrow) : null,
                el('h2', { style: { margin: '0 0 8px', fontSize: '26px', lineHeight: 1.15 } },
                    attributes.title ? attributes.title.replace(/<[^>]+>/g, '') : __('Timeline', 'balefire')),
                attributes.content ? el('p', { style: { margin: '0 0 20px', fontSize: '14px', opacity: 0.8 } }, attributes.content) : null,
                el('div', {
                    style: hasImage
                        ? { display: 'grid', gridTemplateColumns: imageLeft ? '2fr 3fr' : '3fr 2fr', gap: '32px', alignItems: 'start' }
                        : {},
                },
                    hasImage && imageLeft ? media : null,
                    list,
                    hasImage && !imageLeft ? media : null
                )
            )
        );
    },
    save: () => null,
});
})();
