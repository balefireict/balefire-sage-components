// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/feature-row",
    "title": "Feature Row",
    "category": "balefire",
    "icon": "align-pull-left",
    "description": "A 30/70 flex row with an image on the left and vertically centered text on the right.",
    "keywords": [
        "feature",
        "image",
        "text",
        "row",
        "split",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-feature-row-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
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
        "body": {
            "type": "string",
            "default": ""
        },
        "mediaId": {
            "type": "number",
            "default": 0
        },
        "mediaUrl": {
            "type": "string",
            "default": ""
        },
        "mediaAlt": {
            "type": "string",
            "default": ""
        },
        "imageClass": {
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
        "url": {
            "type": "string",
            "default": ""
        },
        "linkText": {
            "type": "string",
            "default": ""
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, TextControl, RadioControl, SelectControl, Spinner } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-feature-row',
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

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Image', 'balefire'), initialOpen: true },
                    el('div', { className: 'components-base-control' },
                        el('label', { className: 'components-base-control__label' }, __('Featured Image', 'balefire')),
                        el(MediaUpload, {
                            onSelect: (media) => setAttributes({
                                mediaId: media.id,
                                mediaUrl: media.url,
                                mediaAlt: media.alt || '',
                            }),
                            allowedTypes: ['image'],
                            value: attributes.mediaId,
                            render: ({ open }) => el('div', { style: { marginBottom: '8px' } },
                                attributes.mediaUrl
                                    ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                        el('img', {
                                            src: attributes.mediaUrl,
                                            alt: attributes.mediaAlt,
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
                        attributes.mediaUrl ? el('button', {
                            className: 'components-button is-link is-destructive',
                            style: { marginTop: '4px' },
                            onClick: () => setAttributes({ mediaId: 0, mediaUrl: '', mediaAlt: '' }),
                        }, __('Remove Image', 'balefire')) : null
                    ),
                    el(TextControl, {
                        label: __('Image CSS Classes', 'balefire'),
                        help: __('Applied to the image container div. Separate multiple classes with spaces.', 'balefire'),
                        value: attributes.imageClass,
                        onChange: (value) => setAttributes({ imageClass: value }),
                    })
                ),
                el(PanelBody, { title: __('Text', 'balefire') },
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: attributes.heading,
                        onChange: (value) => setAttributes({ heading: value }),
                    })
                ),
                el(PanelBody, { title: __('Link', 'balefire') },
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
                            : null,
                    el(TextControl, {
                        label: __('Link Text', 'balefire'),
                        value: attributes.linkText,
                        onChange: (value) => setAttributes({ linkText: value }),
                    })
                ),
            ),
            // Inline preview
            el('div', blockProps,
                el('div', {
                    className: 'flex flex-col md:flex-row items-center gap-8',
                    style: { padding: '16px' },
                },
                    // Image column
                    el('div', { className: 'w-full md:w-[30%] shrink-0' },
                        attributes.mediaUrl
                            ? el('img', {
                                src: attributes.mediaUrl,
                                alt: attributes.mediaAlt,
                                className: 'w-full h-auto object-cover' + (attributes.imageClass ? ' ' + attributes.imageClass : ''),
                            })
                            : el('div', {
                                className: 'w-full bg-gray-200 flex items-center justify-center',
                                style: { aspectRatio: '1/1', borderRadius: '4px' },
                            }, el('span', { className: 'text-gray-500 text-sm' }, __('No image selected', 'balefire')))
                    ),
                    // Text column
                    el('div', { className: 'w-full md:w-[70%] flex items-center' },
                        el('div', { className: 'flex flex-col gap-4' },
                            attributes.heading
                                ? el('h3', { className: 'text-2xl font-headline leading-tight' }, attributes.heading)
                                : null,
                            el(RichText, {
                                tagName: 'div',
                                className: 'max-w-[62ch] text-base leading-7',
                                value: attributes.body,
                                onChange: (value) => setAttributes({ body: value }),
                                placeholder: __('Body text...', 'balefire'),
                                allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                            }),
                            attributes.linkType !== 'none' && attributes.linkText
                                ? el('span', {
                                    className: 'my-3 inline-flex items-center gap-2 font-semibold arrow-link',
                                    style: { color: 'var(--color-1, #000)' },
                                },
                                    attributes.linkText,
                                    el('svg', {
                                        className: 'w-[1.375rem] h-[0.75rem] shrink-0',
                                        xmlns: 'http://www.w3.org/2000/svg',
                                        width: '22.62',
                                        height: '12.5',
                                        viewBox: '0 0 22.62 12.5',
                                        ariaHidden: 'true',
                                    },
                                        el('path', {
                                            d: 'M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z',
                                            transform: 'translate(3.955 -4.742)',
                                            fill: 'currentColor',
                                            stroke: 'currentColor',
                                            strokeWidth: '0.5',
                                        })
                                    )
                                )
                                : null
                        )
                    )
                )
            )
        );
    },
    save: () => null,
});
