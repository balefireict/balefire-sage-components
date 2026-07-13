// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/features-section",
    "title": "Features Section",
    "category": "balefire",
    "icon": "grid-view",
    "description": "A light-theme business features section with a responsive grid.",
    "keywords": [
        "features",
        "services",
        "business",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-features-section-editor",
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
            "default": "wide"
        },
        "heading": {
            "type": "string",
            "default": ""
        },
        "intro": {
            "type": "string",
            "default": ""
        },
        "maxWidth": {
            "type": "string",
            "default": "wide"
        },
        "backgroundTone": {
            "type": "string",
            "default": "white"
        }
    },
    "version": "1.0.0",
    "style": "balefire-features-section"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-features-section',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Features Section Settings', 'balefire') },
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: attributes.heading,
                        onChange: (value) => setAttributes({ heading: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro text', 'balefire'),
                        value: attributes.intro,
                        onChange: (value) => setAttributes({ intro: value }),
                    }),
                    el(SelectControl, {
                        label: __('Max width', 'balefire'),
                        value: attributes.maxWidth,
                        options: [
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
