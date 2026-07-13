import metadata from '../../../blocks/case-study-compare/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-case-study-compare',
        });

        const renderIconUpload = (side, iconIdAttr, iconUrlAttr, iconAltAttr) => {
            const iconId = attributes[iconIdAttr];
            const iconUrl = attributes[iconUrlAttr];
            const iconAlt = attributes[iconAltAttr];

            return el('div', { className: 'components-base-control', style: { marginBottom: '12px' } },
                el('label', { className: 'components-base-control__label' }, __(side + ' Icon', 'balefire')),
                el(MediaUpload, {
                    onSelect: (media) => setAttributes({
                        [iconIdAttr]: media.id,
                        [iconUrlAttr]: media.url,
                        [iconAltAttr]: media.alt || '',
                    }),
                    allowedTypes: ['image'],
                    value: iconId,
                    render: ({ open }) => el('div', { style: { marginBottom: '8px' } },
                        iconUrl
                            ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                                el('img', {
                                    src: iconUrl,
                                    alt: iconAlt,
                                    style: { maxWidth: '64px', maxHeight: '64px', objectFit: 'contain' },
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
                }),
                iconUrl ? el('button', {
                    className: 'components-button is-link is-destructive',
                    style: { marginTop: '4px' },
                    onClick: () => setAttributes({ [iconIdAttr]: 0, [iconUrlAttr]: '', [iconAltAttr]: '' }),
                }, __('Remove Icon', 'balefire')) : null
            );
        };

        const renderCard = (side, iconUrl, iconAlt, title, body, bodyAttr) => {
            const isLeft = side === 'Left';
            const titleAttr = isLeft ? 'leftTitle' : 'rightTitle';

            return el('div', { className: 'w-full md:w-1/2 flex flex-col items-center border border-gray-300 rounded-lg px-8 pt-12 pb-14' },
                iconUrl
                    ? el('div', { className: '-mt-28 mb-8' },
                        el('div', { className: 'rounded-full h-[100px] w-[100px] flex items-center justify-center bg-gray-100' },
                            el('img', { src: iconUrl, alt: iconAlt, className: 'h-16 w-16 object-contain' })
                        )
                    )
                    : el('div', { className: '-mt-28 mb-8' },
                        el('div', { className: 'rounded-full h-[100px] w-[100px] flex items-center justify-center bg-gray-200' })
                    ),
                title
                    ? el('h3', { className: 'text-3xl font-headline mb-2' }, title)
                    : null,
                el(RichText, {
                    tagName: 'div',
                    className: 'text-base leading-7',
                    value: body,
                    onChange: (value) => setAttributes({ [bodyAttr]: value }),
                    placeholder: __(side + ' card body...', 'balefire'),
                    allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                })
            );
        };

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Left Card', 'balefire'), initialOpen: true },
                    renderIconUpload('Left', 'leftIconId', 'leftIconUrl', 'leftIconAlt'),
                    el(TextControl, {
                        label: __('Card Title', 'balefire'),
                        value: attributes.leftTitle,
                        onChange: (value) => setAttributes({ leftTitle: value }),
                    })
                ),
                el(PanelBody, { title: __('Right Card', 'balefire'), initialOpen: false },
                    renderIconUpload('Right', 'rightIconId', 'rightIconUrl', 'rightIconAlt'),
                    el(TextControl, {
                        label: __('Card Title', 'balefire'),
                        value: attributes.rightTitle,
                        onChange: (value) => setAttributes({ rightTitle: value }),
                    })
                )
            ),
            el('div', blockProps,
                el('div', { className: 'text-center', style: { padding: '16px' } },
                    el('div', { className: 'flex flex-col md:flex-row items-center justify-center gap-4 md:gap-6' },
                        renderCard('Left', attributes.leftIconUrl, attributes.leftIconAlt, attributes.leftTitle, attributes.leftBody, 'leftBody'),

                        // Arrow
                        el('div', { className: 'rotate-90 md:rotate-0 shrink-0' },
                            el('svg', { xmlns: 'http://www.w3.org/2000/svg', width: '37.224', height: '37.225', viewBox: '0 0 37.224 37.225' },
                                el('path', { d: 'M20.627,1.995A18.613,18.613,0,1,0,39.238,20.608,18.622,18.622,0,0,0,20.627,1.995Zm0,2.792a15.82,15.82,0,1,1-15.82,15.82,15.827,15.827,0,0,1,15.82-15.82Zm2.845,8.778s2.8,2.8,6.06,6.067a1.4,1.4,0,0,1,0,1.975c-3.263,3.265-6.058,6.065-6.058,6.065a1.382,1.382,0,0,1-.981.4A1.4,1.4,0,0,1,21.5,25.7l3.682-3.68H12.718a1.4,1.4,0,0,1,0-2.792H25.18L21.5,15.54a1.389,1.389,0,0,1,.011-1.962,1.4,1.4,0,0,1,.989-.413A1.373,1.373,0,0,1,23.471,13.565Z', transform: 'translate(-2.014 -1.995)', fill: '#93a8a4' })
                            )
                        ),

                        renderCard('Right', attributes.rightIconUrl, attributes.rightIconAlt, attributes.rightTitle, attributes.rightBody, 'rightBody')
                    )
                )
            )
        );
    },
    save: () => null,
});
