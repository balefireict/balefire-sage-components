// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/team-member-centered",
    "title": "Team Member (Centered)",
    "category": "balefire",
    "icon": "admin-users",
    "description": "A centered team member card with image, name, and title. Optional link wrapper.",
    "keywords": [
        "team",
        "member",
        "profile",
        "person",
        "card",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-team-member-centered-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
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
        "name": {
            "type": "string",
            "default": ""
        },
        "title": {
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
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, RadioControl, SelectControl, Spinner } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-team-member-centered',
        });

        const pages = useSelect((select) => {
            return select('core').getEntityRecords('postType', 'page', { per_page: 100, orderby: 'title', order: 'asc' }) || [];
        }, []);

        const pageOptions = [
            { label: __('— Select a page —', 'balefire'), value: 0 },
            ...pages.map((page) => ({ label: page.title.rendered, value: page.id })),
        ];

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Team Member Settings', 'balefire') },
                    el('div', { className: 'components-base-control' },
                        el('label', { className: 'components-base-control__label' }, __('Photo', 'balefire')),
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
                    ),
                    el(TextControl, {
                        label: __('Name', 'balefire'),
                        value: attributes.name,
                        onChange: (value) => setAttributes({ name: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
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
