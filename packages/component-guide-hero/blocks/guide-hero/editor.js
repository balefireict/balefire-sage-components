(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/guide-hero",
    "title": "Guide Hero",
    "category": "balefire",
    "icon": "cover-image",
    "description": "Dark split hero for guide/hub pages: breadcrumb trail, eyebrow, title, intro copy, dual buttons, and a right-column image.",
    "keywords": ["hero", "guide", "header", "breadcrumb", "balefire"],
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
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "secondaryLabel": { "type": "string", "default": "" },
        "secondaryUrl": { "type": "string", "default": "" },
        "imageId": { "type": "number", "default": 0 },
        "imageUrl": { "type": "string", "default": "" },
        "imageAlt": { "type": "string", "default": "" },
        "imageRatio": { "type": "string", "default": "fill" },
        "imageFrame": { "type": "string", "default": "card" },
        "imageFit": { "type": "string", "default": "cover" },
        "showBreadcrumb": { "type": "boolean", "default": true }
    },
    "editorScript": "balefire-guide-hero-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, ToggleControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-guide-hero',
            style: {
                position: 'relative',
                overflow: 'hidden',
                background: '#171717',
                borderLeft: '6px solid #d72b27',
                padding: '48px 40px',
                display: 'grid',
                gridTemplateColumns: attributes.imageUrl ? '1.1fr 0.9fr' : '1fr',
                gap: '32px',
                alignItems: 'center',
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
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        help: __('Leave empty to use the page title.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro Copy', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show breadcrumb trail', 'balefire'),
                        checked: !!attributes.showBreadcrumb,
                        onChange: (value) => setAttributes({ showBreadcrumb: value }),
                    })
                ),
                el(PanelBody, { title: __('Image', 'balefire'), initialOpen: false },
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
                    }, __('Remove Image', 'balefire')) : null,
                    el(SelectControl, {
                        label: __('Image ratio', 'balefire'),
                        help: __('Fill matches the text column height on desktop (5:4 on mobile).', 'balefire'),
                        value: attributes.imageRatio || 'fill',
                        options: [
                            { label: __('Fill (match text height)', 'balefire'), value: 'fill' },
                            { label: '5:4', value: '5/4' },
                            { label: '4:3', value: '4/3' },
                            { label: __('Square (1:1)', 'balefire'), value: '1/1' },
                            { label: '16:9', value: '16/9' },
                        ],
                        onChange: (value) => setAttributes({ imageRatio: value }),
                    }),
                    el(SelectControl, {
                        label: __('Image frame', 'balefire'),
                        help: __('None drops the rounded corners and hairline border — use it for logos and cut-out graphics.', 'balefire'),
                        value: attributes.imageFrame || 'card',
                        options: [
                            { label: __('Card (rounded, bordered)', 'balefire'), value: 'card' },
                            { label: __('None', 'balefire'), value: 'none' },
                        ],
                        onChange: (value) => setAttributes({ imageFrame: value }),
                    }),
                    el(SelectControl, {
                        label: __('Image fit', 'balefire'),
                        help: __('Cover fills the frame and may crop. Contain shows the whole image.', 'balefire'),
                        value: attributes.imageFit || 'cover',
                        options: [
                            { label: __('Cover (fill, may crop)', 'balefire'), value: 'cover' },
                            { label: __('Contain (show all)', 'balefire'), value: 'contain' },
                        ],
                        onChange: (value) => setAttributes({ imageFit: value }),
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
                el('div', null,
                    attributes.eyebrow ? el('p', {
                        style: { color: '#d72b27', textTransform: 'uppercase', fontSize: '12px', fontWeight: 700, letterSpacing: '0.16em', margin: '0 0 12px' },
                    }, attributes.eyebrow) : null,
                    el('h1', {
                        style: { color: '#fff', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.05, margin: 0 },
                    }, attributes.title || __('Guide Hero (page title)', 'balefire')),
                    attributes.content ? el('p', {
                        style: { color: '#8b8b8b', marginTop: '16px', fontSize: '14px' },
                    }, attributes.content) : null,
                    (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                        style: { marginTop: '20px', display: 'flex', gap: '12px', flexWrap: 'wrap' },
                    },
                        attributes.primaryLabel ? el('span', {
                            style: { background: '#d72b27', color: '#fff', padding: '10px 20px', borderRadius: '8px', textTransform: 'uppercase', fontSize: '13px', fontWeight: 700 },
                        }, attributes.primaryLabel) : null,
                        attributes.secondaryLabel ? el('span', {
                            style: { border: '1px solid rgba(255,255,255,0.3)', color: '#fff', padding: '10px 20px', borderRadius: '8px', textTransform: 'uppercase', fontSize: '13px', fontWeight: 700 },
                        }, attributes.secondaryLabel) : null
                    ) : null
                ),
                attributes.imageUrl ? el('img', {
                    src: attributes.imageUrl,
                    alt: attributes.imageAlt || '',
                    style: {
                        width: '100%',
                        height: attributes.imageRatio === 'fill' ? '100%' : undefined,
                        aspectRatio: attributes.imageRatio === 'fill' ? undefined : (attributes.imageRatio || '5/4').replace('/', ' / '),
                        objectFit: attributes.imageFit === 'contain' ? 'contain' : 'cover',
                        borderRadius: attributes.imageFrame === 'none' ? undefined : '8px',
                        boxShadow: attributes.imageFrame === 'none' ? undefined : 'inset 0 0 0 1px rgba(255,255,255,0.1)',
                    },
                }) : null
            )
        );
    },
    save: () => null,
});

})();
