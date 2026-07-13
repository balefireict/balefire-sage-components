(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/card-media",
    "title": "Card Media",
    "category": "balefire",
    "icon": "format-image",
    "description": "A skeleton card with an image, logo, title, body text, and learn-more link.",
    "keywords": [
        "card",
        "media",
        "image",
        "feature",
        "service",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-card-media-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "logoType": {
            "type": "string",
            "default": "image"
        },
        "logoSvgCode": {
            "type": "string",
            "default": ""
        },
        "mediaType": {
            "type": "string",
            "default": "image"
        },
        "svgCode": {
            "type": "string",
            "default": ""
        },
        "logoId": {
            "type": "number",
            "default": 0
        },
        "logoUrl": {
            "type": "string",
            "default": ""
        },
        "logoAlt": {
            "type": "string",
            "default": ""
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
        "title": {
            "type": "string",
            "default": ""
        },
        "text": {
            "type": "string",
            "default": ""
        },
        "linkText": {
            "type": "string",
            "default": ""
        },
        "url": {
            "type": "string",
            "default": ""
        },
        "linkType": {
            "type": "string",
            "default": "none"
        },
        "pageId": {
            "type": "number",
            "default": 0
        },
        "openInNewTab": {
            "type": "boolean",
            "default": false
        }
    },
    "version": "1.0.0",
    "style": "balefire-card-media"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, RadioControl, SelectControl, Spinner } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-card-media',
        });

        const pages = useSelect((select) => {
            return select('core').getEntityRecords('postType', 'page', { per_page: 100, orderby: 'title', order: 'asc' }) || [];
        }, []);

        const pageOptions = [
            { label: __('— Select a page —', 'balefire'), value: 0 },
            ...pages.map((page) => ({
                label: page.title.rendered,
                value: page.id,
            })),
        ];

        const mediaUpload = (label, onSelect, preview) =>
            el('div', { className: 'components-base-control' },
                el('label', { className: 'components-base-control__label' }, label),
                el(MediaUpload, {
                    onSelect,
                    allowedTypes: ['image'],
                    value: preview.id,
                    render: ({ open }) => el('div', { style: { marginBottom: '8px' } },
                        preview.url
                            ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                el('img', {
                                    src: preview.url,
                                    alt: preview.alt,
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
                })
            );

        const svgInput = (label, value, onChange) =>
            el(TextareaControl, {
                label,
                help: __('Paste the <svg>...</svg> element. Use currentColor for fill/stroke to animate on hover.', 'balefire'),
                value,
                onChange,
                rows: 6,
            });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Card Settings', 'balefire') },
                    el(RadioControl, {
                        label: __('Top Logo', 'balefire'),
                        selected: attributes.logoType,
                        options: [
                            { label: __('Image Upload', 'balefire'), value: 'image' },
                            { label: __('SVG Code', 'balefire'), value: 'svg' },
                            { label: __('None', 'balefire'), value: 'none' },
                        ],
                        onChange: (value) => setAttributes({ logoType: value }),
                    }),
                    attributes.logoType === 'image'
                        ? mediaUpload(__('Logo Image', 'balefire'), (media) => setAttributes({
                            logoId: media.id,
                            logoUrl: media.url,
                            logoAlt: media.alt || '',
                        }), { id: attributes.logoId, url: attributes.logoUrl, alt: attributes.logoAlt })
                        : attributes.logoType === 'svg'
                            ? svgInput(__('Logo SVG Code', 'balefire'), attributes.logoSvgCode, (value) => setAttributes({ logoSvgCode: value }))
                            : null,
                    el(RadioControl, {
                        label: __('Card Media', 'balefire'),
                        selected: attributes.mediaType,
                        options: [
                            { label: __('Image Upload', 'balefire'), value: 'image' },
                            { label: __('SVG Code', 'balefire'), value: 'svg' },
                        ],
                        onChange: (value) => setAttributes({ mediaType: value }),
                    }),
                    attributes.mediaType === 'image'
                        ? mediaUpload(__('Card Image', 'balefire'), (media) => setAttributes({
                            imageId: media.id,
                            imageUrl: media.url,
                            imageAlt: media.alt || '',
                        }), { id: attributes.imageId, url: attributes.imageUrl, alt: attributes.imageAlt })
                        : svgInput(__('Card SVG Code', 'balefire'), attributes.svgCode, (value) => setAttributes({ svgCode: value })),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Body Text', 'balefire'),
                        value: attributes.text,
                        onChange: (value) => setAttributes({ text: value }),
                    }),
                    el(TextControl, {
                        label: __('Link Text', 'balefire'),
                        value: attributes.linkText,
                        onChange: (value) => setAttributes({ linkText: value }),
                    }),
                    el(RadioControl, {
                        label: __('Link', 'balefire'),
                        selected: attributes.linkType,
                        options: [
                            { label: __('None', 'balefire'), value: 'none' },
                            { label: __('WordPress Page', 'balefire'), value: 'page' },
                            { label: __('External URL (opens in new tab)', 'balefire'), value: 'external' },
                        ],
                        onChange: (value) => setAttributes({ linkType: value }),
                    }),
                    attributes.linkType === 'page'
                        ? pages.length === 0
                            ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px', padding: '8px 0' } },
                                el(Spinner),
                                el('span', null, __('Loading pages…', 'balefire'))
                              )
                            : el(SelectControl, {
                                label: __('Select Page', 'balefire'),
                                value: attributes.pageId,
                                options: pageOptions,
                                onChange: (value) => setAttributes({ pageId: parseInt(value, 10) || 0 }),
                              })
                        : attributes.linkType === 'external'
                            ? el(TextControl, {
                                label: __('External URL', 'balefire'),
                                type: 'url',
                                value: attributes.url,
                                onChange: (value) => setAttributes({ url: value }),
                                placeholder: 'https://example.com',
                              })
                            : null
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, { block: metadata.name, attributes, httpMethod: 'POST' })
            )
        );
    },
    save: () => null,
});

})();
