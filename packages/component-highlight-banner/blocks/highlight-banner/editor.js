(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/highlight-banner",
    "title": "Highlight Banner",
    "category": "balefire",
    "icon": "tag",
    "description": "Icon + title + copy + optional CTA in a tinted or card band, or as a compact inline callout (info or alert).",
    "keywords": [
        "banner",
        "highlight",
        "callout",
        "note",
        "alert",
        "cta",
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
        "variant": {
            "type": "string",
            "default": "tint"
        },
        "intent": {
            "type": "string",
            "default": "info"
        },
        "title": {
            "type": "string",
            "default": ""
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "ctaLabel": {
            "type": "string",
            "default": ""
        },
        "ctaUrl": {
            "type": "string",
            "default": ""
        },
        "iconSvg": {
            "type": "string",
            "default": ""
        },
        "iconId": {
            "type": "number",
            "default": 0
        }
    },
    "editorScript": "balefire-highlight-banner-editor",
    "style": "balefire-highlight-banner"
};

const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
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
        const tone = attributes.tone || 'white';
        const variant = attributes.variant || 'tint';
        const intent = attributes.intent || 'info';
        const isInline = variant === 'inline';
        const isDark = !isInline && DARK_TONES.indexOf(tone) !== -1;
        const accent = intent === 'alert' ? '#c54d4c' : '#d72b27';
        const plainTitle = (attributes.title || '').replace(/<[^>]+>/g, '');

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-highlight-banner',
            style: isInline ? {
                display: 'flex',
                gap: '12px',
                alignItems: 'flex-start',
                padding: '14px 16px',
                borderRadius: '8px',
                border: '1px solid ' + accent + '55',
                borderLeft: '4px solid ' + accent,
                background: accent + '18',
                color: '#171717',
            } : {
                background: isDark ? '#2b2b2b' : (tone === 'white' ? '#ffffff' : '#f4f4f4'),
                color: isDark ? '#ffffff' : '#171717',
                padding: '32px',
                border: '1px solid #e0e0e0',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: 'Highlight Banner', initialOpen: true },
                    el(SelectControl, { label: 'Variant', value: variant, options: [{ label: 'Tint band', value: 'tint' }, { label: 'Card band', value: 'card' }, { label: 'Inline callout', value: 'inline' }], onChange: (v) => setAttributes({ variant: v }) }),
                    el(SelectControl, { label: 'Intent', value: intent, options: [{ label: 'Info', value: 'info' }, { label: 'Alert', value: 'alert' }], onChange: (v) => setAttributes({ intent: v }) }),
                    isInline ? null : el(SelectControl, { label: 'Tone', value: tone, options: TONE_OPTIONS, onChange: (v) => setAttributes({ tone: v }) }),
                    el(TextControl, { label: 'Title', help: isInline ? 'Bold lead, rendered inline before the content.' : 'Wrap the accent clause in <em>…</em>.', value: attributes.title || '', onChange: (v) => setAttributes({ title: v }) }),
                    el(TextareaControl, { label: 'Content', value: attributes.content || '', onChange: (v) => setAttributes({ content: v }) }),
                    el(TextControl, { label: 'CtaLabel', value: attributes.ctaLabel || '', onChange: (v) => setAttributes({ ctaLabel: v }) }),
                    el(TextControl, { label: 'CtaUrl', value: attributes.ctaUrl || '', onChange: (v) => setAttributes({ ctaUrl: v }) })
                ),
                el(PanelBody, { title: 'Icon', initialOpen: false },
                    el(TextareaControl, { label: 'Inline SVG', help: 'Takes precedence over the image below. Leave both empty for the default glyph.', rows: 3, value: attributes.iconSvg || '', onChange: (v) => setAttributes({ iconSvg: v }) }),
                    el(MediaUpload, {
                        onSelect: (media) => setAttributes({ iconId: media.id || 0 }),
                        allowedTypes: ['image'],
                        value: attributes.iconId,
                        render: ({ open }) => el('div', { style: { display: 'flex', gap: '8px' } },
                            el(Button, { variant: 'secondary', onClick: open }, attributes.iconId ? 'Change icon image (#' + attributes.iconId + ')' : 'Select icon image'),
                            attributes.iconId ? el(Button, { variant: 'link', isDestructive: true, onClick: () => setAttributes({ iconId: 0 }) }, 'Clear') : null
                        ),
                    })
                )
            ),
            el('div', blockProps,
                el('span', { style: { flex: 'none', width: isInline ? '40px' : '56px', height: isInline ? '40px' : '56px', borderRadius: isInline ? '999px' : '8px', background: accent, display: 'inline-block', marginBottom: isInline ? 0 : '12px' } }),
                isInline
                    ? el('p', { style: { margin: 0, fontSize: '14px', lineHeight: 1.6 } },
                        plainTitle ? el('b', null, plainTitle + ' ') : null,
                        attributes.content || '(no content)'
                    )
                    : el('div', null,
                        el('p', { style: { margin: 0, fontFamily: 'monospace', fontSize: '11px', textTransform: 'uppercase', color: isDark ? '#ffffff' : accent, fontWeight: 700 } }, 'Highlight Banner · ' + variant + (intent === 'alert' ? ' · alert' : '')),
                        el('h2', { style: { margin: '6px 0 0', fontSize: '24px', color: 'inherit' } }, plainTitle || '(untitled)'),
                        attributes.content ? el('p', { style: { margin: '10px 0 0', fontSize: '14px', opacity: 0.85 } }, attributes.content) : null,
                        attributes.ctaLabel ? el('span', { style: { display: 'inline-block', marginTop: '12px', padding: '8px 14px', borderRadius: '6px', background: accent, color: '#fff', fontSize: '12px', fontWeight: 700 } }, attributes.ctaLabel) : null
                    )
            )
        );
    },
    save: () => null,
});

})();
