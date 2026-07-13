// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/faq-items",
    "title": "FAQ Items",
    "category": "balefire",
    "icon": "editor-help",
    "description": "Wrapper block that groups FAQ accordion items together.",
    "keywords": [
        "faq",
        "accordion",
        "items",
        "group",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-faq-items-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "allowedBlocks": [
        "balefire/faq-no-borders"
    ],
    "attributes": {},
    "version": "1.0.0"
};

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
