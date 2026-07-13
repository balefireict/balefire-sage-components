// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/section",
    "title": "Section",
    "category": "balefire",
    "icon": "layout",
    "description": "Full-width layout section with background color.",
    "keywords": [
        "section",
        "layout",
        "background",
        "wrapper",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-section-editor",
    "render": "file:./render.php",
    "supports": {
        "className": true,
        "align": [
            "none",
            "wide",
            "full"
        ]
    },
    "providesContext": {
        "balefire/sectionTone": "backgroundColor"
    },
    "attributes": {
        "backgroundColor": {
            "type": "string",
            "default": "transparent"
        },
        "htmlId": {
            "type": "string",
            "default": ""
        },
        "align": {
            "type": "string",
            "default": "wide"
        }
    },
    "version": "1.0.0"
};

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
