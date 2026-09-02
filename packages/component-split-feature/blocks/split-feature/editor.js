(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/split-feature",
    "title": "Split Feature",
    "category": "balefire",
    "icon": "align-pull-right",
    "description": "Two-column guide-page section: eyebrow/title/copy/checklist/buttons beside an image, stat card, soft panel, or nested content (tables).",
    "keywords": [
        "split",
        "feature",
        "stat",
        "table",
        "checklist",
        "panel",
        "balefire"
    ],
    "textdomain": "balefire",
    "version": "1.1.0",
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
        "items": {
            "type": "array",
            "default": [],
            "items": {
                "type": "string"
            }
        },
        "listColumns": {
            "type": "number",
            "default": 1
        },
        "ratio": {
            "type": "string",
            "default": "even"
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
        },
        "panelIcon": {
            "type": "string",
            "default": ""
        },
        "panelTitle": {
            "type": "string",
            "default": ""
        },
        "panelText": {
            "type": "string",
            "default": ""
        },
        "panelCtaLabel": {
            "type": "string",
            "default": ""
        },
        "panelCtaUrl": {
            "type": "string",
            "default": ""
        }
    },
    "editorScript": "balefire-split-feature-editor",
    "style": "balefire-split-feature"
};

const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Same list as SectionStyles::tones() in component-support, plus the legacy
// "grey" accu-shot value so existing blocks keep a readable selection.
const TONE_OPTIONS = [
    { label: 'White', value: 'white' },
    { label: 'Light', value: 'light' },
    { label: 'Surface', value: 'surface' },
    { label: 'Surface (muted)', value: 'surface-muted' },
    { label: 'Primary', value: 'primary' },
    { label: 'Secondary', value: 'secondary' },
    { label: 'Accent', value: 'accent' },
    { label: 'Dark', value: 'dark' },
    { label: 'Grey (legacy)', value: 'grey' },
];

const DARK_TONES = ['primary', 'dark', 'accent'];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];
        const updateItem = (index, value) => setAttributes({ items: items.map((it, i) => (i === index ? value : it)) });
        const removeItem = (index) => setAttributes({ items: items.filter((it, i) => i !== index) });
        const addItem = () => setAttributes({ items: [...items, ''] });
        const moveItem = (index, delta) => {
            const t = index + delta;
            if (t < 0 || t >= items.length) return;
            const next = [...items];
            [next[index], next[t]] = [next[t], next[index]];
            setAttributes({ items: next });
        };

        const tone = attributes.tone || 'white';
        const isDark = DARK_TONES.indexOf(tone) !== -1;
        const mediaType = attributes.mediaType || 'content';

        // Hooks run unconditionally; only the rendering is conditional.
        const innerProps = useInnerBlocksProps({ style: { marginTop: '12px', background: '#fff', color: '#171717', border: '1px dashed #bbb', borderRadius: '6px', padding: '12px' } }, { renderAppender: InnerBlocks.ButtonBlockAppender });

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-split-feature',
            style: {
                background: isDark ? '#2b2b2b' : (tone === 'white' ? '#ffffff' : '#f4f4f4'),
                color: isDark ? '#ffffff' : '#171717',
                padding: '32px',
                border: '1px solid #e0e0e0',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: 'Split Feature', initialOpen: true },
                    el(SelectControl, { label: 'Tone', value: tone, options: TONE_OPTIONS, onChange: (v) => setAttributes({ tone: v }) }),
                    el(SelectControl, { label: 'Column ratio (left : right)', value: attributes.ratio || 'even', options: [{ label: 'Even', value: 'even' }, { label: '60 : 40', value: '60-40' }, { label: '40 : 60', value: '40-60' }], onChange: (v) => setAttributes({ ratio: v }) }),
                    el(TextControl, { label: 'Eyebrow', value: attributes.eyebrow || '', onChange: (v) => setAttributes({ eyebrow: v }) }),
                    el(TextControl, { label: 'Title', help: 'Wrap the accent clause in <em>…</em>.', value: attributes.title || '', onChange: (v) => setAttributes({ title: v }) }),
                    el(TextareaControl, { label: 'Content', value: attributes.content || '', onChange: (v) => setAttributes({ content: v }) }),
                    el(TextControl, { label: 'PrimaryLabel', value: attributes.primaryLabel || '', onChange: (v) => setAttributes({ primaryLabel: v }) }),
                    el(TextControl, { label: 'PrimaryUrl', value: attributes.primaryUrl || '', onChange: (v) => setAttributes({ primaryUrl: v }) }),
                    el(TextControl, { label: 'SecondaryLabel', value: attributes.secondaryLabel || '', onChange: (v) => setAttributes({ secondaryLabel: v }) }),
                    el(TextControl, { label: 'SecondaryUrl', value: attributes.secondaryUrl || '', onChange: (v) => setAttributes({ secondaryUrl: v }) })
                ),
                el(PanelBody, { title: 'Checklist', initialOpen: false },
                    el(SelectControl, { label: 'Columns', value: String(attributes.listColumns || 1), options: [{ label: '1', value: '1' }, { label: '2', value: '2' }], onChange: (v) => setAttributes({ listColumns: parseInt(v, 10) === 2 ? 2 : 1 }) }),
                    ...items.map((item, index) => el('div', { key: index, style: { border: '1px solid #ddd', borderRadius: '4px', padding: '8px', marginBottom: '8px' } },
                        el(TextareaControl, { label: 'Item ' + (index + 1), rows: 2, value: item || '', onChange: (v) => updateItem(index, v) }),
                        el('div', { style: { display: 'flex', gap: '4px' } },
                            el(Button, { variant: 'secondary', size: 'small', onClick: () => moveItem(index, -1), disabled: index === 0 }, 'Up'),
                            el(Button, { variant: 'secondary', size: 'small', onClick: () => moveItem(index, 1), disabled: index === items.length - 1 }, 'Down'),
                            el(Button, { variant: 'secondary', size: 'small', isDestructive: true, onClick: () => removeItem(index) }, 'Remove')
                        )
                    )),
                    el(Button, { variant: 'primary', onClick: addItem }, 'Add item')
                ),
                el(PanelBody, { title: 'Media', initialOpen: false },
                    el(SelectControl, { label: 'MediaType', value: mediaType, options: [{ label: 'content', value: 'content' }, { label: 'image', value: 'image' }, { label: 'stat', value: 'stat' }, { label: 'panel', value: 'panel' }], onChange: (v) => setAttributes({ mediaType: v }) }),
                    el(SelectControl, { label: 'MediaSide', value: attributes.mediaSide || 'right', options: [{ label: 'right', value: 'right' }, { label: 'left', value: 'left' }], onChange: (v) => setAttributes({ mediaSide: v }) }),
                    mediaType === 'image' ? el(MediaUpload, {
                        onSelect: (media) => setAttributes({ imageId: media.id || 0, imageUrl: media.url || '', imageAlt: media.alt || '' }),
                        allowedTypes: ['image'],
                        value: attributes.imageId,
                        render: ({ open }) => el('button', { className: 'components-button is-secondary', onClick: open }, attributes.imageUrl ? 'Change Image' : 'Select Image'),
                    }) : null,
                    mediaType === 'stat' ? el(Fragment, null,
                        el(TextControl, { label: 'StatValue', value: attributes.statValue || '', onChange: (v) => setAttributes({ statValue: v }) }),
                        el(TextControl, { label: 'StatLabel', value: attributes.statLabel || '', onChange: (v) => setAttributes({ statLabel: v }) }),
                        el(TextareaControl, { label: 'StatNote', value: attributes.statNote || '', onChange: (v) => setAttributes({ statNote: v }) })
                    ) : null,
                    mediaType === 'panel' ? el(Fragment, null,
                        el(TextareaControl, { label: 'Panel icon (inline SVG)', rows: 3, value: attributes.panelIcon || '', onChange: (v) => setAttributes({ panelIcon: v }) }),
                        el(TextControl, { label: 'Panel title', value: attributes.panelTitle || '', onChange: (v) => setAttributes({ panelTitle: v }) }),
                        el(TextareaControl, { label: 'Panel text', value: attributes.panelText || '', onChange: (v) => setAttributes({ panelText: v }) }),
                        el(TextControl, { label: 'Panel CTA label', value: attributes.panelCtaLabel || '', onChange: (v) => setAttributes({ panelCtaLabel: v }) }),
                        el(TextControl, { label: 'Panel CTA URL', value: attributes.panelCtaUrl || '', onChange: (v) => setAttributes({ panelCtaUrl: v }) })
                    ) : null
                )
            ),
            el('div', blockProps,
                el('p', { style: { margin: 0, fontFamily: 'monospace', fontSize: '11px', textTransform: 'uppercase', color: isDark ? '#ffffff' : '#d72b27', fontWeight: 700 } }, attributes.eyebrow || 'Split Feature'),
                el('h2', { style: { margin: '6px 0 0', fontSize: '24px', color: 'inherit' } }, (attributes.title || '(untitled)').replace(/<[^>]+>/g, '')),
                attributes.content ? el('p', { style: { margin: '10px 0 0', fontSize: '14px', opacity: 0.85 } }, attributes.content) : null,
                items.length ? el('ul', { style: { margin: '12px 0 0', paddingLeft: '18px', fontSize: '14px', columns: attributes.listColumns === 2 ? 2 : 1 } },
                    ...items.map((item, i) => el('li', { key: i }, item || '…'))
                ) : null,
                mediaType === 'panel' ? el('div', { style: { marginTop: '12px', background: 'rgba(0,0,0,0.06)', borderRadius: '6px', padding: '12px' } },
                    el('strong', null, attributes.panelTitle || 'Panel'),
                    attributes.panelText ? el('p', { style: { margin: '6px 0 0', fontSize: '13px' } }, attributes.panelText) : null,
                    attributes.panelCtaLabel ? el('span', { style: { display: 'inline-block', marginTop: '8px', padding: '6px 12px', borderRadius: '6px', background: '#171717', color: '#fff', fontSize: '12px', fontWeight: 700 } }, attributes.panelCtaLabel) : null
                ) : null,
                mediaType === 'content' ? el('div', innerProps) : null
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
