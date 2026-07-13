// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/centered-intro-text",
    "title": "Centered Intro Text",
    "category": "balefire",
    "icon": "editor-aligncenter",
    "description": "A centered intro text section with full-width surface support.",
    "keywords": [
        "intro",
        "centered",
        "text",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-centered-intro-text-editor",
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
        "align": {
            "type": "string",
            "default": "full"
        },
        "content": {
            "type": "string",
            "default": ""
        },
        "maxWidth": {
            "type": "string",
            "default": "narrow"
        },
        "backgroundTone": {
            "type": "string",
            "default": "light"
        }
    },
    "version": "1.0.0",
    "style": "balefire-centered-intro-text"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-centered-intro-text',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Centered Intro Text Settings', 'balefire') },
                    el(SelectControl, {
                        label: __('Inner width', 'balefire'),
                        value: attributes.maxWidth,
                        options: [
                            { label: __('Narrow', 'balefire'), value: 'narrow' },
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
                    }),
                    el(TextareaControl, {
                        label: __('Content', 'balefire'),
                        value: attributes.content,
                        onChange: (value) => setAttributes({ content: value }),
                    })
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, {
                    block: metadata.name,
                    attributes,
                    httpMethod: 'POST',
                })
            )
        );
    },
    save: () => null,
});
