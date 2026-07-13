(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/split-35-65",
    "title": "Split 35/65",
    "category": "balefire",
    "icon": "columns",
    "description": "A 35/65 two-column layout with text + button on the left (vertically centered) and image or video on the right.",
    "keywords": [
        "split",
        "columns",
        "35",
        "65",
        "layout",
        "image",
        "video",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-split-35-65-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "spacing": {
            "margin": true,
            "padding": true
        },
        "align": [
            "wide",
            "full"
        ]
    },
    "attributes": {
        "heading": {
            "type": "string",
            "default": ""
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "buttonLabel": {
            "type": "string",
            "default": ""
        },
        "buttonUrl": {
            "type": "string",
            "default": ""
        },
        "mediaType": {
            "type": "string",
            "default": "image"
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
        "videoUrl": {
            "type": "string",
            "default": ""
        },
        "gap": {
            "type": "string",
            "default": "8"
        },
        "reverse": {
            "type": "boolean",
            "default": false
        },
        "iconId": {
            "type": "number",
            "default": 0
        },
        "iconUrl": {
            "type": "string",
            "default": ""
        },
        "iconAlt": {
            "type": "string",
            "default": ""
        },
        "align": {
            "type": "string",
            "default": "wide"
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, TextControl, RadioControl, RangeControl, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-split-35-65',
        });

        const reverseClass = attributes.reverse ? ' md:flex-row-reverse' : '';

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Left Column — Text', 'balefire') },
                    el('div', { className: 'components-base-control' },
                        el('label', { className: 'components-base-control__label' }, __('Heading Image', 'balefire')),
                        el(MediaUpload, {
                            onSelect: (media) => setAttributes({
                                iconId: media.id,
                                iconUrl: media.url,
                                iconAlt: media.alt || '',
                            }),
                            allowedTypes: ['image'],
                            value: attributes.iconId,
                            render: ({ open }) => el('div', { style: { marginBottom: '8px' } },
                                attributes.iconUrl
                                    ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                        el('img', {
                                            src: attributes.iconUrl,
                                            alt: attributes.iconAlt,
                                            style: { maxWidth: '48px', maxHeight: '48px', objectFit: 'contain' },
                                        }),
                                        el('button', {
                                            type: 'button',
                                            onClick: open,
                                            className: 'components-button is-secondary is-small',
                                        }, __('Replace', 'balefire'))
                                    )
                                    : el('button', {
                                        type: 'button',
                                        onClick: open,
                                        className: 'components-button is-secondary',
                                    }, __('Select Image', 'balefire'))
                            ),
                        }),
                        attributes.iconUrl ? el('button', {
                            className: 'components-button is-link is-destructive',
                            style: { marginTop: '4px' },
                            onClick: () => setAttributes({ iconId: 0, iconUrl: '', iconAlt: '' }),
                        }, __('Remove Image', 'balefire')) : null
                    ),
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: attributes.heading,
                        onChange: (value) => setAttributes({ heading: value }),
                    }),
                    el(TextControl, {
                        label: __('Button Label', 'balefire'),
                        value: attributes.buttonLabel,
                        onChange: (value) => setAttributes({ buttonLabel: value }),
                    }),
                    attributes.buttonLabel
                        ? el(TextControl, {
                            label: __('Button URL', 'balefire'),
                            value: attributes.buttonUrl,
                            onChange: (value) => setAttributes({ buttonUrl: value }),
                            type: 'url',
                            placeholder: '/contact/',
                        })
                        : null
                ),
                el(PanelBody, { title: __('Right Column — Media', 'balefire'), initialOpen: true },
                    el(RadioControl, {
                        label: __('Media Type', 'balefire'),
                        selected: attributes.mediaType,
                        options: [
                            { label: __('Image', 'balefire'), value: 'image' },
                            { label: __('Video', 'balefire'), value: 'video' },
                            { label: __('None', 'balefire'), value: 'none' },
                        ],
                        onChange: (value) => setAttributes({ mediaType: value }),
                    }),
                    attributes.mediaType === 'image'
                        ? el('div', { className: 'components-base-control' },
                            el('label', { className: 'components-base-control__label' }, __('Image', 'balefire')),
                            el(MediaUpload, {
                                onSelect: (media) => setAttributes({
                                    imageId: media.id,
                                    imageUrl: media.url,
                                    imageAlt: media.alt || '',
                                }),
                                allowedTypes: ['image'],
                                value: attributes.imageId,
                                render: ({ open }) => el('div', { style: { marginBottom: '8px' } },
                                    attributes.imageUrl
                                        ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                            el('img', {
                                                src: attributes.imageUrl,
                                                alt: attributes.imageAlt,
                                                style: { maxWidth: '120px', maxHeight: '80px', objectFit: 'contain', borderRadius: '4px' },
                                            }),
                                            el('button', {
                                                type: 'button',
                                                onClick: open,
                                                className: 'components-button is-secondary is-small',
                                            }, __('Replace', 'balefire'))
                                        )
                                        : el('button', {
                                            type: 'button',
                                            onClick: open,
                                            className: 'components-button is-secondary',
                                        }, __('Select Image', 'balefire'))
                                ),
                            }),
                            attributes.imageUrl ? el('button', {
                                className: 'components-button is-link is-destructive',
                                style: { marginTop: '4px' },
                                onClick: () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' }),
                            }, __('Remove Image', 'balefire')) : null
                        )
                        : attributes.mediaType === 'video'
                            ? el(TextControl, {
                                label: __('Video URL', 'balefire'),
                                help: __('YouTube, Vimeo, or direct .mp4 link.', 'balefire'),
                                value: attributes.videoUrl,
                                onChange: (value) => setAttributes({ videoUrl: value }),
                                type: 'url',
                                placeholder: 'https://youtube.com/watch?v=...',
                            })
                            : null
                ),
                el(PanelBody, { title: __('Layout', 'balefire') },
                    el(ToggleControl, {
                        label: __('Reverse columns (text on right)', 'balefire'),
                        checked: attributes.reverse,
                        onChange: (value) => setAttributes({ reverse: value }),
                    }),
                    el(RangeControl, {
                        label: __('Gap', 'balefire'),
                        value: parseInt(attributes.gap) || 8,
                        min: 0,
                        max: 16,
                        onChange: (value) => setAttributes({ gap: String(value) }),
                    })
                )
            ),
            // Inline preview with RichText body
            el('div', blockProps,
                el('div', {
                    className: 'flex flex-col md:flex-row gap-' + (attributes.gap || '8') + reverseClass,
                    style: { padding: '16px' },
                },
                    // Left column preview
                    el('div', { className: 'w-full md:w-[35%] flex items-center' },
                        el('div', { className: 'flex flex-col gap-4' },
                            attributes.iconUrl ? el('img', {
                                src: attributes.iconUrl,
                                alt: attributes.iconAlt,
                                className: 'w-16 h-16 object-contain',
                            }) : null,
                            attributes.heading ? el('h2', {
                                className: 'text-3xl font-headline leading-[1.05]',
                            }, attributes.heading) : null,
                            el(RichText, {
                                tagName: 'div',
                                className: 'max-w-[62ch] text-base leading-7',
                                value: attributes.content,
                                onChange: (value) => setAttributes({ content: value }),
                                placeholder: __('Body text...', 'balefire'),
                                allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                            }),
                            (attributes.buttonLabel && attributes.buttonUrl) ? el('div', { className: 'wp-block-button' },
                                el('span', {
                                    className: 'wp-block-button__link has-text-align-center wp-element-button',
                                    style: { pointerEvents: 'none' },
                                }, attributes.buttonLabel)
                            ) : null
                        )
                    ),
                    // Right column preview
                    el('div', { className: 'w-full md:w-[65%]' },
                        attributes.mediaType === 'image' && attributes.imageUrl
                            ? el('img', {
                                src: attributes.imageUrl,
                                alt: attributes.imageAlt,
                                className: 'w-full h-auto object-cover rounded-lg',
                            })
                            : attributes.mediaType === 'video' && attributes.videoUrl
                                ? el('div', {
                                    className: 'w-full bg-gray-200 rounded-lg flex items-center justify-center',
                                    style: { aspectRatio: '16/9' },
                                },
                                    el('span', { className: 'text-gray-500 text-sm' },
                                        __('Video: ', 'balefire') + attributes.videoUrl
                                    )
                                )
                                : null
                    )
                )
            )
        );
    },
    save: () => null,
});

})();
