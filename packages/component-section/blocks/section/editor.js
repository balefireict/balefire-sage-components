import metadata from '../../../blocks/section/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const TEMPLATE = [['balefire/container']];

const BG_OPTIONS = [
    { label: __('None', 'balefire'), value: 'none' },
    { label: __('White', 'balefire'), value: 'white' },
    { label: __('Light', 'balefire'), value: 'light' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-section',
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                template: TEMPLATE,
                renderAppender: false,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Background color', 'balefire'),
                        value: attributes.backgroundColor,
                        options: BG_OPTIONS,
                        onChange: (value) => setAttributes({ backgroundColor: value }),
                    }),
                    el(TextControl, {
                        label: __('Section ID', 'balefire'),
                        value: attributes.htmlId,
                        placeholder: 'e.g. solutions, hero, contact',
                        onChange: (value) => setAttributes({ htmlId: value.replace(/[^a-zA-Z0-9_-]/g, '') }),
                    })
                )
            ),
            el('section', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});
