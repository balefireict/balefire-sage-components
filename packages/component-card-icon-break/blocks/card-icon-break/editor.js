// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/card-icon-break",
    "title": "Card Icon Break",
    "category": "balefire",
    "icon": "format-image",
    "description": "A skeleton card with an absolutely-positioned icon and inner blocks for content. Optional anchor wrapper.",
    "keywords": [
        "card",
        "icon",
        "feature",
        "service",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-card-icon-break-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
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
        "url": {
            "type": "string",
            "default": ""
        },
        "openInNewTab": {
            "type": "boolean",
            "default": false
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, InnerBlocks, useInnerBlocksProps, MediaUpload, URLInput } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Editor-only CSS to fix icon overlap
const editorStyles = `
    .bma-editor-preview-card-icon-break,
    .wp-block-balefire-card-icon-break {
        position: relative !important;
        padding-top: 1rem !important;
    }
    .bma-editor-preview-card-icon-break .bma-card-icon-break__icon,
    .wp-block-balefire-card-icon-break .bma-card-icon-break__icon {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        transform: none !important;
        margin-bottom: 1rem !important;
        margin-inline: auto !important;
    }
    .bma-editor-preview-card-icon-break .bma-card-icon-break__icon-placeholder {
        margin-bottom: 1rem;
    }
    .bma-editor-preview-card-icon-break .bma-card-icon-break__content {
        position: relative;
        z-index: 1;
    }
`;

// Inject editor styles once
if (!document.getElementById('bma-card-icon-break-editor-styles')) {
    const styleEl = document.createElement('style');
    styleEl.id = 'bma-card-icon-break-editor-styles';
    styleEl.textContent = editorStyles;
    document.head.appendChild(styleEl);
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-card-icon-break',
        });

        const innerBlocksProps = useInnerBlocksProps({
            className: 'bma-card-icon-break__content',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Card Settings', 'balefire') },
                    el('div', { className: 'components-base-control' },
                        el('label', { className: 'components-base-control__label' }, __('Icon Image', 'balefire')),
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
                                    }, __('Select Icon', 'balefire'))
                            ),
                        })
                    ),
                    el(URLInput, {
                        label: __('Link URL (optional)', 'balefire'),
                        value: attributes.url,
                        onChange: (value) => setAttributes({ url: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Open in new tab', 'balefire'),
                        checked: attributes.openInNewTab,
                        onChange: (value) => setAttributes({ openInNewTab: value }),
                    })
                )
            ),
            el('div', blockProps,
                attributes.iconUrl
                    ? el('div', { className: 'bma-card-icon-break__icon' },
                        el('div', { className: 'icon-bg rounded-full h-24 w-24 flex items-center justify-center' },
                            el('img', {
                                src: attributes.iconUrl,
                                alt: attributes.iconAlt,
                                className: 'h-16 w-16 object-contain',
                            })
                        )
                    )
                    : el('div', { className: 'bma-card-icon-break__icon-placeholder', style: { textAlign: 'center', padding: '20px', color: '#999' } },
                        __('No icon selected', 'balefire')
                    ),
                el('div', innerBlocksProps)
            )
        );
    },
    save: () => {
        return el(InnerBlocks.Content, null);
    },
});
