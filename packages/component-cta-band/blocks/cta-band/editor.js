(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/cta-band",
    "title": "CTA Band",
    "category": "balefire",
    "icon": "megaphone",
    "description": "Full-bleed primary-red closing call to action: centered uppercase heading, supporting copy, dual buttons.",
    "keywords": ["cta", "band", "closing", "conversion", "balefire"],
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
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-cta-band-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-cta-band',
            style: {
                background: '#d72b27',
                padding: '56px 40px',
                textAlign: 'center',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Body Content', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
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
            el('div', blockProps,
                el('h2', {
                    style: { color: '#fff', textTransform: 'uppercase', fontSize: '28px', lineHeight: 1.1, margin: 0 },
                }, attributes.title || __('CTA Band', 'balefire')),
                attributes.content ? el('p', {
                    style: { color: 'rgba(255,255,255,0.9)', marginTop: '12px', fontSize: '14px' },
                }, attributes.content) : null,
                (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                    style: { marginTop: '24px', display: 'flex', gap: '12px', justifyContent: 'center', flexWrap: 'wrap' },
                },
                    attributes.primaryLabel ? el('span', {
                        style: { background: '#171717', color: '#fff', padding: '10px 20px', borderRadius: '8px', textTransform: 'uppercase', fontSize: '13px', fontWeight: 700 },
                    }, attributes.primaryLabel) : null,
                    attributes.secondaryLabel ? el('span', {
                        style: { border: '1px solid rgba(255,255,255,0.3)', color: '#fff', padding: '10px 20px', borderRadius: '8px', textTransform: 'uppercase', fontSize: '13px', fontWeight: 700 },
                    }, attributes.secondaryLabel + ' →') : null
                ) : null
            )
        );
    },
    save: () => null,
});

})();
