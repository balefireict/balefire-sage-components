import metadata from '../../../blocks/layout-grid/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl, RangeControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const COLUMN_OPTIONS = [
    { label: __('1 Column', 'balefire'), value: '1' },
    { label: __('2 Columns', 'balefire'), value: '2' },
    { label: __('3 Columns', 'balefire'), value: '3' },
    { label: __('4 Columns', 'balefire'), value: '4' },
    { label: __('6 Columns', 'balefire'), value: '6' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-layout-grid',
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Layout Grid Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Desktop columns', 'balefire'),
                        value: attributes.columns,
                        options: COLUMN_OPTIONS,
                        onChange: (value) => setAttributes({ columns: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tablet columns', 'balefire'),
                        value: attributes.columnsTablet || '',
                        options: [
                            { label: __('Default (1 column)', 'balefire'), value: '' },
                            ...COLUMN_OPTIONS,
                        ],
                        onChange: (value) => setAttributes({ columnsTablet: value }),
                    }),
                    el(RangeControl, {
                        label: __('Gap', 'balefire'),
                        value: parseInt(attributes.gap) || 6,
                        min: 0,
                        max: 16,
                        onChange: (value) => setAttributes({ gap: String(value) }),
                    })
                )
            ),
            el('div', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});
