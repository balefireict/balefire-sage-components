(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/preheader-and-title",
    "title": "Preheader & Title",
    "category": "balefire",
    "icon": "heading",
    "description": "A skeleton h2 with an inline preheader span. Styling left to the theme.",
    "keywords": [
        "preheader",
        "title",
        "heading",
        "subtitle",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-preheader-and-title-editor",
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
        "preheader": {
            "type": "string",
            "default": "Preheader"
        },
        "title": {
            "type": "string",
            "default": "Title"
        },
        "textAlign": {
            "type": "string",
            "default": "center"
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
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-preheader-and-title',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Preheader & Title', 'balefire') },
                    el(SelectControl, {
                        label: __('Text alignment', 'balefire'),
                        value: attributes.textAlign,
                        options: [
                            { label: __('Left', 'balefire'), value: 'left' },
                            { label: __('Center', 'balefire'), value: 'center' },
                            { label: __('Right', 'balefire'), value: 'right' },
                        ],
                        onChange: (value) => setAttributes({ textAlign: value }),
                    }),
                    el(TextControl, {
                        label: __('Preheader', 'balefire'),
                        value: attributes.preheader,
                        onChange: (value) => setAttributes({ preheader: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
                    })
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, { block: metadata.name, attributes, httpMethod: 'POST' })
            )
        );
    },
    save: () => null,
});

})();
