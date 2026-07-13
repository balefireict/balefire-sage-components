(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/hero-headline",
    "title": "Hero Headline",
    "category": "balefire",
    "icon": "cover-image",
    "description": "Full-bleed image hero with eyebrow flourish, highlighted headline words, supporting text, and dual buttons.",
    "keywords": ["hero", "headline", "banner", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "imageId": { "type": "number", "default": 0 },
        "imageUrl": { "type": "string", "default": "" },
        "imageAlt": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-hero-headline-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Editor preview of the *word* highlight convention.
const highlightTitle = (title) => {
    const parts = String(title || '').split(/\*([^*]+)\*/);
    return parts.map((part, index) =>
        index % 2 === 1
            ? el('span', { key: index, style: { color: '#d72b27' } }, part)
            : part
    );
};

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-hero-headline',
            style: {
                position: 'relative',
                overflow: 'hidden',
                minHeight: '360px',
                display: 'flex',
                alignItems: 'center',
                background: attributes.imageUrl
                    ? 'linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url(' + attributes.imageUrl + ') center/cover'
                    : '#1a1a1a',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Title', 'balefire'),
                        help: __('Wrap words in *asterisks* to highlight them in the brand color.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Supporting Text', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    })
                ),
                el(PanelBody, { title: __('Background Image', 'balefire'), initialOpen: false },
                    el(MediaUpload, {
                        onSelect: (media) => setAttributes({
                            imageId: media.id || 0,
                            imageUrl: media.url || '',
                            imageAlt: media.alt || '',
                        }),
                        allowedTypes: ['image'],
                        value: attributes.imageId,
                        render: ({ open }) => el('button', {
                            className: 'components-button is-secondary',
                            onClick: open,
                        }, attributes.imageUrl ? __('Change Image', 'balefire') : __('Select Image', 'balefire')),
                    }),
                    attributes.imageUrl ? el('button', {
                        className: 'components-button is-link is-destructive',
                        style: { marginTop: '8px' },
                        onClick: () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' }),
                    }, __('Remove Image', 'balefire')) : null
                ),
                el(PanelBody, { title: __('Primary Button', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.primaryLabel || '',
                        onChange: (value) => setAttributes({ primaryLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        type: 'url',
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
                        type: 'url',
                        value: attributes.secondaryUrl || '',
                        onChange: (value) => setAttributes({ secondaryUrl: value }),
                    })
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('div', { style: { padding: '48px 40px', maxWidth: '720px' } },
                    attributes.eyebrow && el('p', {
                        style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 12px' },
                    }, '→ ' + attributes.eyebrow + ' →'),
                    el('h2', {
                        style: { color: '#fff', fontSize: '40px', lineHeight: 1.05, textTransform: 'uppercase', margin: '0 0 16px' },
                    }, attributes.title ? highlightTitle(attributes.title) : __('Hero Headline', 'balefire')),
                    attributes.content && el('p', {
                        style: { color: 'rgba(255,255,255,0.7)', maxWidth: '500px', margin: '0 0 24px' },
                    }, attributes.content),
                    (attributes.primaryLabel || attributes.secondaryLabel) && el('div', { style: { display: 'flex', gap: '16px', flexWrap: 'wrap' } },
                        attributes.primaryLabel && el('span', {
                            style: { background: '#d72b27', color: '#fff', padding: '14px 16px', borderRadius: '4px', fontFamily: 'monospace', fontWeight: 700, textTransform: 'uppercase' },
                        }, attributes.primaryLabel + ' →'),
                        attributes.secondaryLabel && el('span', {
                            style: { border: '1px solid rgba(255,255,255,0.8)', color: '#fff', padding: '14px 16px', borderRadius: '4px', fontFamily: 'monospace', fontWeight: 700, textTransform: 'uppercase' },
                        }, attributes.secondaryLabel)
                    )
                )
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
