import metadata from '../../../blocks/grid-cell/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const SPAN_OPTIONS = [
    { label: __('1', 'balefire'), value: '1' },
    { label: __('2', 'balefire'), value: '2' },
    { label: __('3', 'balefire'), value: '3' },
    { label: __('4', 'balefire'), value: '4' },
    { label: __('5', 'balefire'), value: '5' },
    { label: __('6', 'balefire'), value: '6' },
    { label: __('7', 'balefire'), value: '7' },
    { label: __('8', 'balefire'), value: '8' },
    { label: __('9', 'balefire'), value: '9' },
    { label: __('10', 'balefire'), value: '10' },
    { label: __('11', 'balefire'), value: '11' },
    { label: __('12', 'balefire'), value: '12' },
];

const VALIGN_OPTIONS = [
    { label: __('Default', 'balefire'), value: '' },
    { label: __('Top', 'balefire'), value: 'start' },
    { label: __('Center', 'balefire'), value: 'center' },
    { label: __('Bottom', 'balefire'), value: 'end' },
    { label: __('Stretch', 'balefire'), value: 'stretch' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-grid-cell',
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Grid Cell Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Mobile column span', 'balefire'),
                        value: attributes.colSpanMobile,
                        options: SPAN_OPTIONS,
                        onChange: (value) => setAttributes({ colSpanMobile: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tablet column span', 'balefire'),
                        value: attributes.colSpanTablet || '',
                        options: [
                            { label: __('Default', 'balefire'), value: '' },
                            ...SPAN_OPTIONS,
                        ],
                        onChange: (value) => setAttributes({ colSpanTablet: value }),
                    }),
                    el(SelectControl, {
                        label: __('Desktop column span', 'balefire'),
                        value: attributes.colSpan,
                        options: SPAN_OPTIONS,
                        onChange: (value) => setAttributes({ colSpan: value }),
                    }),
                    el(SelectControl, {
                        label: __('Row span', 'balefire'),
                        value: attributes.rowSpan || '',
                        options: [
                            { label: __('1 (default)', 'balefire'), value: '' },
                            ...SPAN_OPTIONS,
                        ],
                        onChange: (value) => setAttributes({ rowSpan: value }),
                    }),
                    el(SelectControl, {
                        label: __('Vertical alignment', 'balefire'),
                        value: attributes.vAlign || '',
                        options: VALIGN_OPTIONS,
                        onChange: (value) => setAttributes({ vAlign: value }),
                    })
                )
            ),
            el('div', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});
