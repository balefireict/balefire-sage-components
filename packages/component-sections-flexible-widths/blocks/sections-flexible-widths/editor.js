// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/sections-flexible-widths",
    "title": "Section (Flexible Width)",
    "category": "balefire",
    "icon": "layout",
    "description": "Full-width section with a Tailwind-powered max-width container inside.",
    "keywords": [
        "section",
        "flexible",
        "width",
        "container",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-sections-flexible-widths-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "wide",
            "full"
        ]
    },
    "attributes": {
        "containerWidth": {
            "type": "string",
            "default": "max-w-7xl"
        },
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
    "version": "1.0.0",
    "style": "balefire-sections-flexible-widths"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const WIDTH_OPTIONS = [
    { label: __('None (no max-width)', 'balefire'), value: 'max-w-none' },
    { label: __('XS (20rem)', 'balefire'), value: 'max-w-xs' },
    { label: __('SM (24rem)', 'balefire'), value: 'max-w-sm' },
    { label: __('MD (28rem)', 'balefire'), value: 'max-w-md' },
    { label: __('LG (32rem)', 'balefire'), value: 'max-w-lg' },
    { label: __('XL (36rem)', 'balefire'), value: 'max-w-xl' },
    { label: __('2XL (42rem)', 'balefire'), value: 'max-w-2xl' },
    { label: __('3XL (48rem)', 'balefire'), value: 'max-w-3xl' },
    { label: __('4XL (56rem)', 'balefire'), value: 'max-w-4xl' },
    { label: __('5XL (64rem)', 'balefire'), value: 'max-w-5xl' },
    { label: __('6XL (72rem)', 'balefire'), value: 'max-w-6xl' },
    { label: __('7XL (80rem)', 'balefire'), value: 'max-w-7xl' },
    { label: __('Screen SM (640px)', 'balefire'), value: 'max-w-screen-sm' },
    { label: __('Screen MD (768px)', 'balefire'), value: 'max-w-screen-md' },
    { label: __('Screen LG (1024px)', 'balefire'), value: 'max-w-screen-lg' },
    { label: __('Screen XL (1280px)', 'balefire'), value: 'max-w-screen-xl' },
    { label: __('Screen 2XL (1536px)', 'balefire'), value: 'max-w-screen-2xl' },
    { label: __('Prose (65ch)', 'balefire'), value: 'max-w-prose' },
];

const BG_OPTIONS = [
    { label: __('Transparent', 'balefire'), value: 'transparent' },
    { label: __('White', 'balefire'), value: 'white' },
    { label: __('Light', 'balefire'), value: 'light' },
    { label: __('Primary', 'balefire'), value: 'primary' },
    { label: __('Secondary', 'balefire'), value: 'secondary' },
    { label: __('Dark', 'balefire'), value: 'dark' },
];

const BG_CLASS_MAP = {
    transparent: 'bg-transparent',
    white: 'bg-white',
    light: 'bg-light',
    primary: 'bg-primary',
    secondary: 'bg-secondary',
    dark: 'bg-dark',
};

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const bgClass = BG_CLASS_MAP[attributes.backgroundColor] || 'bg-transparent';

        const blockProps = useBlockProps({
            className: 'bma-section w-full mx-auto ' + bgClass,
        });

        const innerBlocksProps = useInnerBlocksProps(
            {
                className: (attributes.containerWidth || 'max-w-7xl') + ' mx-auto',
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Background color', 'balefire'),
                        value: attributes.backgroundColor || 'transparent',
                        options: BG_OPTIONS,
                        onChange: (value) => setAttributes({ backgroundColor: value }),
                    }),
                    el(TextControl, {
                        label: __('Section ID', 'balefire'),
                        value: attributes.htmlId,
                        placeholder: 'e.g. solutions, hero, contact',
                        onChange: (value) => setAttributes({ htmlId: value.replace(/[^a-zA-Z0-9_-]/g, '') }),
                    })
                ),
                el(PanelBody, { title: __('Container Width', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Max width', 'balefire'),
                        value: attributes.containerWidth,
                        options: WIDTH_OPTIONS,
                        onChange: (value) => setAttributes({ containerWidth: value }),
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
