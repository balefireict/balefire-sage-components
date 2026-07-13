(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/section-heading",
    "title": "Section Heading",
    "category": "balefire",
    "icon": "heading",
    "description": "A compact heading block with eyebrow, title, and supporting copy.",
    "keywords": [
        "heading",
        "eyebrow",
        "intro",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-section-heading-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "wide",
            "full"
        ],
        "spacing": {
            "margin": true,
            "padding": true
        }
    },
    "attributes": {
        "eyebrow": {
            "type": "string",
            "default": ""
        },
        "title": {
            "type": "string",
            "default": ""
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "contentAlign": {
            "type": "string",
            "default": "left"
        },
        "maxWidth": {
            "type": "string",
            "default": ""
        },
        "backgroundTone": {
            "type": "string",
            "default": "transparent"
        },
        "align": {
            "type": "string",
            "default": "wide"
        }
    },
    "version": "1.0.0",
    "style": "balefire-section-heading"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-section-heading',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section Heading Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Content alignment', 'balefire'),
                        value: attributes.contentAlign,
                        options: [
                            { label: __('Left', 'balefire'), value: 'left' },
                            { label: __('Center', 'balefire'), value: 'center' },
                            { label: __('Right', 'balefire'), value: 'right' },
                        ],
                        onChange: (value) => setAttributes({ contentAlign: value }),
                    }),
                    el(SelectControl, {
                        label: __('Max width', 'balefire'),
                        value: attributes.maxWidth,
                        options: [
                            { label: __('Use plugin default', 'balefire'), value: '' },
                            { label: __('Content', 'balefire'), value: 'content' },
                            { label: __('Wide', 'balefire'), value: 'wide' },
                            { label: __('Full', 'balefire'), value: 'full' },
                        ],
                        onChange: (value) => setAttributes({ maxWidth: value }),
                    }),
                    el(SelectControl, {
                        label: __('Background tone', 'balefire'),
                        value: attributes.backgroundTone,
                        options: [
                            { label: __('Transparent', 'balefire'), value: 'transparent' },
                            { label: __('White', 'balefire'), value: 'white' },
                            { label: __('Light', 'balefire'), value: 'light' },
                            { label: __('Primary', 'balefire'), value: 'primary' },
                            { label: __('Secondary', 'balefire'), value: 'secondary' },
                            { label: __('Dark', 'balefire'), value: 'dark' },
                        ],
                        onChange: (value) => setAttributes({ backgroundTone: value }),
                    })
                )
            ),
            el('div', blockProps,
                el(TextControl, {
                    label: __('Eyebrow', 'balefire'),
                    value: attributes.eyebrow,
                    onChange: (value) => setAttributes({ eyebrow: value }),
                }),
                el(TextControl, {
                    label: __('Title', 'balefire'),
                    value: attributes.title,
                    onChange: (value) => setAttributes({ title: value }),
                }),
                el(TextareaControl, {
                    label: __('Content', 'balefire'),
                    value: attributes.content,
                    onChange: (value) => setAttributes({ content: value }),
                })
            )
        );
    },
    save: () => null,
});

})();
