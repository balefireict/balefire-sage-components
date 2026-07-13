import metadata from '../../../blocks/faq-items/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { createElement: el, Fragment } = wp.element;

const ALLOWED_BLOCKS = ['balefire/faq-no-borders'];
const TEMPLATE = [
    ['balefire/faq-no-borders'],
    ['balefire/faq-no-borders'],
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: () => {
        const blockProps = useBlockProps({
            className: 'faq-section-items bma-editor-preview bma-editor-preview-faq-items',
        });

        const innerBlocksProps = useInnerBlocksProps(
            {},
            {
                allowedBlocks: ALLOWED_BLOCKS,
                template: TEMPLATE,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el('div', blockProps,
                el('div', innerBlocksProps)
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});
