(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/cta-band",
    "title": "CTA Band",
    "category": "balefire",
    "icon": "megaphone",
    "description": "Full-bleed toned closing call to action: centered heading with accent clause, supporting copy, dual buttons, optional pill link and background motif.",
    "keywords": ["cta", "band", "closing", "conversion", "balefire"],
    "textdomain": "balefire",
    "version": "1.1.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full"]
    },
    "attributes": {
        "align": { "type": "string", "default": "full" },
        "tone": { "type": "string", "default": "primary" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "primaryStyle": { "type": "string", "default": "cta" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" },
        "tertiaryLabel": { "type": "string", "default": "" },
        "tertiaryUrl": { "type": "string", "default": "" },
        "tertiaryIconSvg": { "type": "string", "default": "" },
        "showMotif": { "type": "boolean", "default": false }
    },
    "editorScript": "balefire-cta-band-editor",
    "style": "balefire-cta-band"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Subset of SectionStyles::tones() that suits a closing band.
const TONE_OPTIONS = [
    { label: 'Primary', value: 'primary' },
    { label: 'Dark', value: 'dark' },
    { label: 'Accent', value: 'accent' },
    { label: 'Secondary', value: 'secondary' },
];

const PREVIEW_BG = {
    primary: '#d72b27',
    dark: '#171717',
    accent: '#3a7d8c',
    secondary: '#e9e4dc',
};

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const tone = attributes.tone || 'primary';
        const isLight = tone === 'secondary';
        const fg = isLight ? '#171717' : '#ffffff';
        const plainTitle = (attributes.title || '').replace(/<[^>]+>/g, '');

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-cta-band',
            style: {
                background: PREVIEW_BG[tone] || PREVIEW_BG.primary,
                color: fg,
                padding: '56px 40px',
                textAlign: 'center',
                position: 'relative',
                overflow: 'hidden',
            },
        });

        const primaryBg = attributes.primaryStyle === 'dark' ? '#171717' : (isLight ? '#171717' : 'rgba(255,255,255,0.92)');
        const primaryFg = attributes.primaryStyle === 'dark' || isLight ? '#ffffff' : '#171717';

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: tone,
                        options: TONE_OPTIONS,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        help: __('Wrap the accent clause in <em>…</em>.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Body Content', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show background motif', 'balefire'),
                        checked: !!attributes.showMotif,
                        onChange: (value) => setAttributes({ showMotif: !!value }),
                    })
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
                    }),
                    el(SelectControl, {
                        label: __('Style', 'balefire'),
                        help: __('"CTA" uses the theme CTA colour (falls back to primary). "Dark" reproduces the original grey-900 button.', 'balefire'),
                        value: attributes.primaryStyle || 'cta',
                        options: [{ label: 'CTA (theme)', value: 'cta' }, { label: 'Dark', value: 'dark' }],
                        onChange: (value) => setAttributes({ primaryStyle: value }),
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
                ),
                el(PanelBody, { title: __('Pill Link', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.tertiaryLabel || '',
                        onChange: (value) => setAttributes({ tertiaryLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.tertiaryUrl || '',
                        onChange: (value) => setAttributes({ tertiaryUrl: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Icon (inline SVG, optional)', 'balefire'),
                        rows: 3,
                        value: attributes.tertiaryIconSvg || '',
                        onChange: (value) => setAttributes({ tertiaryIconSvg: value }),
                    })
                )
            ),
            el('div', blockProps,
                attributes.showMotif ? el('span', {
                    'aria-hidden': 'true',
                    style: { position: 'absolute', left: '-40px', top: '-30px', width: '200px', height: '200px', borderRadius: '50%', border: '6px solid ' + fg, opacity: 0.08, pointerEvents: 'none' },
                }) : null,
                el('h2', {
                    style: { color: fg, fontSize: '28px', lineHeight: 1.1, margin: 0, position: 'relative' },
                }, plainTitle || __('CTA Band', 'balefire')),
                attributes.content ? el('p', {
                    style: { color: fg, opacity: 0.9, marginTop: '12px', fontSize: '14px', position: 'relative' },
                }, attributes.content) : null,
                (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                    style: { marginTop: '24px', display: 'flex', gap: '12px', justifyContent: 'center', flexWrap: 'wrap', position: 'relative' },
                },
                    attributes.primaryLabel ? el('span', {
                        style: { background: primaryBg, color: primaryFg, padding: '10px 20px', borderRadius: '8px', fontSize: '13px', fontWeight: 700 },
                    }, attributes.primaryLabel) : null,
                    attributes.secondaryLabel ? el('span', {
                        style: { border: '1px solid ' + (isLight ? 'rgba(0,0,0,0.3)' : 'rgba(255,255,255,0.3)'), color: fg, padding: '10px 20px', borderRadius: '8px', fontSize: '13px', fontWeight: 700 },
                    }, attributes.secondaryLabel + ' →') : null
                ) : null,
                attributes.tertiaryLabel ? el('div', { style: { marginTop: '16px', position: 'relative' } },
                    el('span', {
                        style: { display: 'inline-block', border: '1px solid ' + (isLight ? 'rgba(0,0,0,0.25)' : 'rgba(255,255,255,0.3)'), background: isLight ? 'rgba(0,0,0,0.08)' : 'rgba(255,255,255,0.14)', color: fg, padding: '6px 14px', borderRadius: '999px', fontSize: '12px', fontWeight: 700 },
                    }, attributes.tertiaryLabel)
                ) : null
            )
        );
    },
    save: () => null,
});

})();
