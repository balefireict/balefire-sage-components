import metadata from '../../../blocks/image-text-rows/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const ALLOWED_BLOCKS = ['balefire/image-text-row'];
const TEMPLATE = [['balefire/image-text-row']];

const GAP_OPTIONS = [
    { label: __('Gap 4', 'balefire'), value: 'gap-4' },
    { label: __('Gap 6', 'balefire'), value: 'gap-6' },
    { label: __('Gap 8', 'balefire'), value: 'gap-8' },
    { label: __('Gap 10', 'balefire'), value: 'gap-10' },
    { label: __('Gap 12', 'balefire'), value: 'gap-12' },
    { label: __('Custom...', 'balefire'), value: 'custom' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const gapSize = attributes.gapSize || 'gap-4';
        const gapCustom = attributes.gapCustom || '';
        const isCustom = gapSize === 'custom';
        const activeGap = isCustom && gapCustom ? gapCustom : gapSize;

        const blockProps = useBlockProps({
            className: [
                'image-text-rows',
                'bma-editor-preview',
                'bma-editor-preview-image-text-rows',
                `row-gap-${String(isCustom ? 'custom' : gapSize).replace('gap-', '')}`,
                attributes.alternateEvenRows ? 'rows-alternate-even' : '',
            ].filter(Boolean).join(' '),
        });

        const innerBlocksProps = useInnerBlocksProps(
            {
                className: ['rows-list', 'flex', 'flex-col', activeGap].join(' '),
            },
            {
                allowedBlocks: ALLOWED_BLOCKS,
                template: TEMPLATE,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Image Text Rows Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Gap size', 'balefire'),
                        value: gapSize,
                        options: GAP_OPTIONS,
                        onChange: (value) => setAttributes({ gapSize: value }),
                    }),
                    isCustom && el(TextControl, {
                        label: __('Custom gap classes', 'balefire'),
                        help: __('e.g. gap-16 md:gap-24', 'balefire'),
                        value: gapCustom,
                        onChange: (value) => setAttributes({ gapCustom: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Alternate even rows', 'balefire'),
                        checked: !!attributes.alternateEvenRows,
                        onChange: (value) => setAttributes({ alternateEvenRows: value }),
                    })
                )
            ),
            el('section', blockProps,
                el('div', innerBlocksProps)
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});
