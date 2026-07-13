import metadata from '../../../blocks/simple-card/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps, MediaUpload } = wp.blockEditor;
const { PanelBody, ToggleControl, SelectControl, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-simple-card',
        });

        const innerBlocksProps = useInnerBlocksProps(blockProps, {
            renderAppender: InnerBlocks.ButtonBlockAppender,
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Image', 'balefire'), initialOpen: true },
                    el('div', { className: 'components-base-control' },
                        el('label', { className: 'components-base-control__label' }, __('Card Image', 'balefire')),
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
                    ),
                    el(TextControl, {
                        label: __('Image CSS Classes', 'balefire'),
                        help: __('Applied directly to the <img> tag. Separate multiple classes with spaces.', 'balefire'),
                        value: attributes.imageClass,
                        onChange: (value) => setAttributes({ imageClass: value }),
                    })
                ),
                el(PanelBody, { title: __('Card Settings', 'balefire') },
                    el(ToggleControl, {
                        label: __('Show border', 'balefire'),
                        checked: attributes.showBorder,
                        onChange: (value) => setAttributes({ showBorder: value }),
                    }),
                    el(SelectControl, {
                        label: __('Border radius', 'balefire'),
                        value: attributes.borderRadius,
                        options: [
                            { label: __('None', 'balefire'), value: 'rounded-none' },
                            { label: __('SM', 'balefire'), value: 'rounded-sm' },
                            { label: __('Default', 'balefire'), value: 'rounded' },
                            { label: __('MD', 'balefire'), value: 'rounded-md' },
                            { label: __('LG', 'balefire'), value: 'rounded-lg' },
                            { label: __('XL', 'balefire'), value: 'rounded-xl' },
                            { label: __('2XL', 'balefire'), value: 'rounded-2xl' },
                            { label: __('Full', 'balefire'), value: 'rounded-full' },
                        ],
                        onChange: (value) => setAttributes({ borderRadius: value }),
                    }),
                    el(SelectControl, {
                        label: __('Padding', 'balefire'),
                        value: attributes.paddingSize,
                        options: [
                            { label: __('None', 'balefire'), value: 'none' },
                            { label: __('Small', 'balefire'), value: 'sm' },
                            { label: __('Medium', 'balefire'), value: 'md' },
                            { label: __('Large', 'balefire'), value: 'lg' },
                        ],
                        onChange: (value) => setAttributes({ paddingSize: value }),
                    })
                )
            ),
            el('div', blockProps,
                attributes.imageUrl
                    ? el('img', {
                        src: attributes.imageUrl,
                        alt: attributes.imageAlt,
                        className: 'w-full h-auto object-cover' + (attributes.imageClass ? ' ' + attributes.imageClass : ''),
                        style: { marginBottom: '12px' },
                    })
                    : null,
                el('div', innerBlocksProps)
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});
