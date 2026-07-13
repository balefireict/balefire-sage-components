(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/grid-row",
    "title": "Grid Row",
    "category": "balefire",
    "icon": "table-row-before",
    "description": "A 12-column grid row. Add Grid Cell children to build fractional layouts.",
    "keywords": [
        "grid",
        "row",
        "12-column",
        "columns",
        "layout",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-grid-row-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "full",
            "wide"
        ]
    },
    "parent": [
        "balefire/container",
        "balefire/section",
        "balefire/sections-flexible-widths"
    ],
    "attributes": {
        "gap": {
            "type": "string",
            "default": "6"
        },
        "minColumnWidth": {
            "type": "string",
            "default": ""
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl, RangeControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const GAP_OPTIONS = [
    { label: __('0', 'balefire'), value: '0' },
    { label: __('1 (0.25rem)', 'balefire'), value: '1' },
    { label: __('2 (0.5rem)', 'balefire'), value: '2' },
    { label: __('3 (0.75rem)', 'balefire'), value: '3' },
    { label: __('4 (1rem)', 'balefire'), value: '4' },
    { label: __('5 (1.25rem)', 'balefire'), value: '5' },
    { label: __('6 (1.5rem)', 'balefire'), value: '6' },
    { label: __('8 (2rem)', 'balefire'), value: '8' },
    { label: __('10 (2.5rem)', 'balefire'), value: '10' },
    { label: __('12 (3rem)', 'balefire'), value: '12' },
    { label: __('16 (4rem)', 'balefire'), value: '16' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-grid-row',
        });

        const innerBlocksProps = useInnerBlocksProps(
            blockProps,
            {
                allowedBlocks: ['balefire/grid-cell'],
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Grid Row Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Gap', 'balefire'),
                        value: attributes.gap,
                        options: GAP_OPTIONS,
                        onChange: (value) => setAttributes({ gap: value }),
                    })
                )
            ),
            el('div', innerBlocksProps)
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
