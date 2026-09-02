(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/link-card-grid",
    "title": "Link Card Grid",
    "category": "balefire",
    "icon": "grid-view",
    "description": "Eyebrow, heading, and a responsive grid of cards: arrow links, icon service cards, call cards, plain text cards, or image tiles.",
    "keywords": ["links", "related", "guides", "cards", "services", "icons", "balefire"],
    "textdomain": "balefire",
    "version": "1.1.0",
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
        "content": { "type": "string", "default": "" },
        "ctaLabel": { "type": "string", "default": "Read the guide" },
        "columns": { "type": "number", "default": 3 },
        "cardStyle": { "type": "string", "default": "linked" },
        "textAlign": { "type": "string", "default": "left" },
        "iconStyle": { "type": "string", "default": "disc" },
        "items": { "type": "array", "default": [], "items": { "type": "object" } }
    },
    "editorScript": "balefire-link-card-grid-editor",
    "style": "balefire-link-card-grid"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;

// Same list as BalefireInc\Sage\Support\SectionStyles::tones(), plus the
// original 'grey' tone this block shipped with (kept for existing content).
const TONE_OPTIONS = [
    { label: __('Grey (original)', 'balefire'), value: 'grey' },
    { label: __('White', 'balefire'), value: 'white' },
    { label: __('Light', 'balefire'), value: 'light' },
    { label: __('Surface', 'balefire'), value: 'surface' },
    { label: __('Surface (muted)', 'balefire'), value: 'surface-muted' },
    { label: __('Primary', 'balefire'), value: 'primary' },
    { label: __('Secondary', 'balefire'), value: 'secondary' },
    { label: __('Accent', 'balefire'), value: 'accent' },
    { label: __('Dark', 'balefire'), value: 'dark' },
];
const DARK_TONES = ['primary', 'secondary', 'accent', 'dark'];

// Media-library picker for a per-item attachment id. Reads the thumbnail
// from the REST store so the item only has to carry the id.
const MediaField = ({ label, id, onSelect, onRemove }) => {
    const media = useSelect((select) => (id ? select('core').getMedia(id) : null), [id]);
    const sizes = media && media.media_details && media.media_details.sizes;
    const url = sizes && sizes.thumbnail ? sizes.thumbnail.source_url : (media ? media.source_url : '');

    return el('div', { className: 'components-base-control', style: { marginBottom: '12px' } },
        el('label', { className: 'components-base-control__label', style: { display: 'block', marginBottom: '6px' } }, label),
        el(MediaUploadCheck, null,
            el(MediaUpload, {
                onSelect: (item) => onSelect(item && item.id ? item.id : 0),
                allowedTypes: ['image'],
                value: id || undefined,
                render: ({ open }) => el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                    url ? el('img', {
                        src: url,
                        alt: '',
                        style: { width: '40px', height: '40px', objectFit: 'contain', border: '1px solid #ddd', borderRadius: '4px' },
                    }) : null,
                    el(Button, { size: 'small', variant: 'secondary', onClick: open }, id ? __('Replace', 'balefire') : __('Select', 'balefire')),
                    id ? el(Button, { size: 'small', variant: 'tertiary', isDestructive: true, onClick: onRemove }, __('Remove', 'balefire')) : null
                ),
            })
        )
    );
};

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const cardStyle = attributes.cardStyle === 'plain' ? 'plain' : 'linked';
        const center = attributes.textAlign === 'center';
        const columns = [2, 3, 4].includes(attributes.columns) ? attributes.columns : 3;
        const darkTone = DARK_TONES.includes(attributes.tone);

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
                background: attributes.tone === 'white' ? '#ffffff' : (darkTone ? '#2b2b2b' : '#f4f4f4'),
                color: darkTone ? '#ffffff' : '#171717',
                padding: '40px',
            },
        });

        const itemFields = (item, index) => el('div', {
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
                help: __('Optional. Leave empty for a plain card; use tel:+1... for a call card.', 'balefire'),
                value: item.url || '',
                onChange: (value) => updateItem(index, { url: value }),
            }),
            el(TextareaControl, {
                label: __('Description (optional — switches to rich cards)', 'balefire'),
                value: item.description || '',
                onChange: (value) => updateItem(index, { description: value }),
            }),
            el(TextControl, {
                label: __('Meta line (optional)', 'balefire'),
                help: __('Small muted line under the description, e.g. hours.', 'balefire'),
                value: item.meta || '',
                onChange: (value) => updateItem(index, { meta: value }),
            }),
            el(TextControl, {
                label: __('Link label (optional)', 'balefire'),
                help: __('Overrides the section CTA label for this card. For tel: links it defaults to the number.', 'balefire'),
                value: item.linkLabel || '',
                onChange: (value) => updateItem(index, { linkLabel: value }),
            }),
            el(TextareaControl, {
                label: __('Icon SVG (optional)', 'balefire'),
                help: __('Paste an <svg>...</svg> element; use fill="currentColor". Takes precedence over the icon image.', 'balefire'),
                value: item.iconSvg || '',
                rows: 4,
                onChange: (value) => updateItem(index, { iconSvg: value }),
            }),
            el(MediaField, {
                label: __('Icon image (optional)', 'balefire'),
                id: item.iconId || 0,
                onSelect: (id) => updateItem(index, { iconId: id }),
                onRemove: () => updateItem(index, { iconId: 0 }),
            }),
            el(MediaField, {
                label: __('Image (optional)', 'balefire'),
                id: item.imageId || 0,
                onSelect: (id) => updateItem(index, { imageId: id }),
                onRemove: () => updateItem(index, { imageId: 0 }),
            }),
            el(TextControl, {
                label: __('Accent color (optional)', 'balefire'),
                help: __('Any CSS color, e.g. #1a73e8 or var(--color-secondary). Defaults to the theme accent.', 'balefire'),
                value: item.accentColor || '',
                onChange: (value) => updateItem(index, { accentColor: value }),
            }),
            el('div', { style: { display: 'flex', gap: '8px' } },
                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, -1) }, '↑'),
                el(Button, { size: 'small', variant: 'secondary', onClick: () => moveItem(index, 1) }, '↓'),
                el(Button, { size: 'small', variant: 'secondary', isDestructive: true, onClick: () => removeItem(index) }, __('Remove', 'balefire'))
            )
        );

        const previewCard = (item, index) => {
            const hasIcon = !!(item.iconSvg || item.iconId);
            const accent = item.accentColor || '#d72b27';
            const showCta = cardStyle === 'linked' && !!item.url;
            return el('div', {
                key: index,
                style: {
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: center ? 'center' : 'flex-start',
                    textAlign: center ? 'center' : 'left',
                    gap: '8px',
                    background: '#fff',
                    color: '#171717',
                    border: '1px solid #e8e8e8',
                    borderRadius: '8px',
                    padding: '20px',
                },
            },
                hasIcon ? el('span', {
                    style: attributes.iconStyle === 'plain'
                        ? { width: '28px', height: '28px', borderRadius: '4px', background: accent, opacity: 0.6 }
                        : { width: '40px', height: '40px', borderRadius: '999px', background: accent },
                }) : null,
                el('span', {
                    style: { fontWeight: 700, fontSize: '15px', color: '#2e2e2e' },
                }, item.label || (item.imageId ? __('(image)', 'balefire') : __('(untitled)', 'balefire'))),
                item.description ? el('span', { style: { fontSize: '13px', color: '#555' } }, item.description) : null,
                item.meta ? el('span', { style: { fontSize: '12px', color: '#888' } }, item.meta) : null,
                showCta ? el('span', { style: { color: accent, fontWeight: 700, fontSize: '13px' } },
                    (item.linkLabel || attributes.ctaLabel || __('Read more', 'balefire')) + ' →'
                ) : null
            );
        };

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
                        help: __('Wrap the accent clause in <em>…</em>.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(TextControl, {
                        label: __('Card CTA label', 'balefire'),
                        help: __('Shown on rich cards (links with a description). Each card can override it.', 'balefire'),
                        value: attributes.ctaLabel || '',
                        onChange: (value) => setAttributes({ ctaLabel: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'grey',
                        options: TONE_OPTIONS,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Columns (desktop)', 'balefire'),
                        value: String(columns),
                        options: [
                            { label: '2', value: '2' },
                            { label: '3', value: '3' },
                            { label: '4', value: '4' },
                        ],
                        onChange: (value) => setAttributes({ columns: parseInt(value, 10) || 3 }),
                    })
                ),
                el(PanelBody, { title: __('Card Style', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Card style', 'balefire'),
                        help: __('Plain hides the link line; a card with a URL is still clickable.', 'balefire'),
                        value: cardStyle,
                        options: [
                            { label: __('Linked (arrow link line)', 'balefire'), value: 'linked' },
                            { label: __('Plain (no link line)', 'balefire'), value: 'plain' },
                        ],
                        onChange: (value) => setAttributes({ cardStyle: value }),
                    }),
                    el(SelectControl, {
                        label: __('Text alignment', 'balefire'),
                        value: center ? 'center' : 'left',
                        options: [
                            { label: __('Left', 'balefire'), value: 'left' },
                            { label: __('Center', 'balefire'), value: 'center' },
                        ],
                        onChange: (value) => setAttributes({ textAlign: value }),
                    }),
                    el(SelectControl, {
                        label: __('Icon style', 'balefire'),
                        value: attributes.iconStyle === 'plain' ? 'plain' : 'disc',
                        options: [
                            { label: __('Disc (accent circle, white glyph)', 'balefire'), value: 'disc' },
                            { label: __('Plain (accent glyph, no disc)', 'balefire'), value: 'plain' },
                        ],
                        onChange: (value) => setAttributes({ iconStyle: value }),
                    })
                ),
                el(PanelBody, { title: __('Cards', 'balefire'), initialOpen: true },
                    ...items.map(itemFields),
                    el(Button, {
                        variant: 'primary',
                        onClick: () => setAttributes({ items: [...items, { label: '', url: '' }] }),
                    }, __('Add Card', 'balefire'))
                )
            ),
            el('div', blockProps,
                attributes.eyebrow ? el('p', {
                    style: { color: darkTone ? '#ffffff' : '#d72b27', textTransform: 'uppercase', fontSize: '12px', fontWeight: 700, letterSpacing: '0.16em', margin: '0 0 12px', textAlign: center ? 'center' : 'left' },
                }, attributes.eyebrow) : null,
                attributes.title ? el('h2', {
                    style: { fontSize: '28px', lineHeight: 1.05, margin: '0 0 20px', color: 'inherit', textAlign: center ? 'center' : 'left' },
                }, (attributes.title || '').replace(/<\/?[^>]+>/g, '')) : null,
                el('div', {
                    style: {
                        display: 'grid',
                        gridTemplateColumns: 'repeat(' + columns + ', 1fr)',
                        gap: '16px',
                    },
                },
                    (items.length ? items : [{ label: __('Add cards in the sidebar →', 'balefire') }]).map(previewCard)
                )
            )
        );
    },
    save: () => null,
});

})();
