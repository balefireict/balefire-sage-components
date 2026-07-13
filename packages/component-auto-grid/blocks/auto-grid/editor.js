// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/auto-grid",
    "title": "Auto Grid",
    "category": "balefire",
    "icon": "grid-view",
    "description": "Flex-wrap grid with centered last row, responsive column counts (desktop/tablet/mobile), gap control, and div/section tag.",
    "keywords": [
        "grid",
        "flex",
        "columns",
        "auto",
        "center",
        "responsive",
        "div",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-auto-grid-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "full",
            "wide"
        ],
        "spacing": {
            "margin": true,
            "padding": true,
            "blockGap": false
        }
    },
    "attributes": {
        "tagName": {
            "type": "string",
            "default": "div"
        },
        "columnsMobile": {
            "type": "string",
            "default": "1"
        },
        "columnsTablet": {
            "type": "string",
            "default": ""
        },
        "columnsDesktop": {
            "type": "string",
            "default": "3"
        },
        "gap": {
            "type": "string",
            "default": "6"
        },
        "verticalAlign": {
            "type": "string",
            "default": "start"
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
const { PanelBody, SelectControl, RangeControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const COLUMN_OPTIONS = [
    { label: __('1 Column', 'balefire'), value: '1' },
    { label: __('2 Columns', 'balefire'), value: '2' },
    { label: __('3 Columns', 'balefire'), value: '3' },
    { label: __('4 Columns', 'balefire'), value: '4' },
    { label: __('5 Columns', 'balefire'), value: '5' },
    { label: __('6 Columns', 'balefire'), value: '6' },
];

const TABLET_DEFAULT = { label: __('Default (same as mobile)', 'balefire'), value: '' };
const DESKTOP_DEFAULT = { label: __('Default (same as tablet)', 'balefire'), value: '' };

const VERTICAL_ALIGN_OPTIONS = [
    { label: __('Top', 'balefire'), value: 'start' },
    { label: __('Center', 'balefire'), value: 'center' },
    { label: __('Bottom', 'balefire'), value: 'end' },
    { label: __('Stretch', 'balefire'), value: 'stretch' },
];

const TAG_OPTIONS = [
    { label: __('div (default)', 'balefire'), value: 'div' },
    { label: __('section', 'balefire'), value: 'section' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: ['bma-editor-preview', 'bma-editor-preview-auto-grid', attributes.className].filter(Boolean).join(' '),
            style: {
                maxWidth: 'var(--wp--style--global--content-size, 768px)',
                marginLeft: 'auto',
                marginRight: 'auto',
            },
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                // Layout panel
                el(PanelBody, { title: __('Grid Settings', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Mobile columns', 'balefire'),
                        value: attributes.columnsMobile,
                        options: COLUMN_OPTIONS,
                        onChange: (value) => setAttributes({ columnsMobile: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tablet columns', 'balefire'),
                        value: attributes.columnsTablet || '',
                        options: [TABLET_DEFAULT, ...COLUMN_OPTIONS],
                        onChange: (value) => setAttributes({ columnsTablet: value }),
                    }),
                    el(SelectControl, {
                        label: __('Desktop columns', 'balefire'),
                        value: attributes.columnsDesktop,
                        options: COLUMN_OPTIONS,
                        onChange: (value) => setAttributes({ columnsDesktop: value }),
                    }),
                    el(RangeControl, {
                        label: __('Gap', 'balefire'),
                        value: parseInt(attributes.gap) || 6,
                        min: 0,
                        max: 16,
                        onChange: (value) => setAttributes({ gap: String(value) }),
                    }),
                    el(SelectControl, {
                        label: __('Vertical alignment', 'balefire'),
                        value: attributes.verticalAlign,
                        options: VERTICAL_ALIGN_OPTIONS,
                        onChange: (value) => setAttributes({ verticalAlign: value }),
                    })
                ),
                // Tag panel
                el(PanelBody, { title: __('HTML Tag', 'balefire') },
                    el(SelectControl, {
                        label: __('Element', 'balefire'),
                        value: attributes.tagName,
                        options: TAG_OPTIONS,
                        onChange: (value) => setAttributes({ tagName: value }),
                    })
                )
            ),
            el('div', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});
