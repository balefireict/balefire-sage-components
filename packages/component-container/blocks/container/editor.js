import metadata from '../../../blocks/container/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const MAX_WIDTH_OPTIONS = [
    { label: __('Narrow (512px)', 'balefire'), value: 'narrow' },
    { label: __('Content (theme)', 'balefire'), value: 'content' },
    { label: __('Medium (1024px)', 'balefire'), value: 'medium' },
    { label: __('Large (1280px)', 'balefire'), value: 'large' },
    { label: __('Wide (theme)', 'balefire'), value: 'wide' },
    { label: __('Full (viewport)', 'balefire'), value: 'full' },
];

const PADDING_OPTIONS = [
    { label: __('None', 'balefire'), value: 'none' },
    { label: __('SM', 'balefire'), value: 'sm' },
    { label: __('MD', 'balefire'), value: 'md' },
    { label: __('LG', 'balefire'), value: 'lg' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-container',
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Container Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Max width', 'balefire'),
                        value: attributes.maxWidth,
                        options: MAX_WIDTH_OPTIONS,
                        onChange: (value) => setAttributes({ maxWidth: value }),
                    }),
                    el(SelectControl, {
                        label: __('Horizontal padding', 'balefire'),
                        value: attributes.paddingInline,
                        options: PADDING_OPTIONS,
                        onChange: (value) => setAttributes({ paddingInline: value }),
                    })
                )
            ),
            el('div', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});
