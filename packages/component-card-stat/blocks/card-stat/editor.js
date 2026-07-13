import metadata from '../../../blocks/card-stat/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, RadioControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-card-stat',
        });

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
                help: __('Paste the <svg>...</svg> element. Use currentColor for fill/stroke to inherit theme colors.', 'balefire'),
                value,
                onChange,
                rows: 6,
            });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Card Settings', 'balefire') },
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(RadioControl, {
                        label: __('Icon', 'balefire'),
                        selected: attributes.iconType,
                        options: [
                            { label: __('SVG Code', 'balefire'), value: 'svg' },
                            { label: __('Image Upload', 'balefire'), value: 'image' },
                            { label: __('None', 'balefire'), value: 'none' },
                        ],
                        onChange: (value) => setAttributes({ iconType: value }),
                    }),
                    attributes.iconType === 'image'
                        ? mediaUpload(__('Icon Image', 'balefire'), (media) => setAttributes({
                            iconId: media.id,
                            iconUrl: media.url,
                            iconAlt: media.alt || '',
                        }), { id: attributes.iconId, url: attributes.iconUrl, alt: attributes.iconAlt })
                        : attributes.iconType === 'svg'
                            ? svgInput(__('Icon SVG Code', 'balefire'), attributes.iconSvgCode, (value) => setAttributes({ iconSvgCode: value }))
                            : null,
                ),
                el(PanelBody, { title: __('Left Stat', 'balefire') },
                    el(TextControl, {
                        label: __('Value', 'balefire'),
                        value: attributes.statLeftValue,
                        onChange: (value) => setAttributes({ statLeftValue: value }),
                        placeholder: 'e.g. 30%',
                    }),
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.statLeftLabel,
                        onChange: (value) => setAttributes({ statLeftLabel: value }),
                        placeholder: 'e.g. Avg. Eligibility',
                    }),
                ),
                el(PanelBody, { title: __('Right Stat', 'balefire') },
                    el(TextControl, {
                        label: __('Value', 'balefire'),
                        value: attributes.statRightValue,
                        onChange: (value) => setAttributes({ statRightValue: value }),
                        placeholder: 'e.g. $1,750',
                    }),
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.statRightLabel,
                        onChange: (value) => setAttributes({ statRightLabel: value }),
                        placeholder: 'e.g. Avg. Credit / Hire',
                    }),
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, { block: metadata.name, attributes, httpMethod: 'POST' })
            )
        );
    },
    save: () => null,
});
