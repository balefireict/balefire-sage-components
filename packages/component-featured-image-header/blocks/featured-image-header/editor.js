// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/featured-image-header",
    "title": "Featured Image Header",
    "category": "balefire",
    "icon": "format-image",
    "description": "Context-aware page header that uses the featured image as background. Shows contextual titles on archive, search, 404, and singular pages. Designed to replace the PHP page-heading template.",
    "keywords": [
        "header",
        "hero",
        "title",
        "featured image",
        "page",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-featured-image-header-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "wide",
            "full"
        ],
        "spacing": {
            "margin": false,
            "padding": false
        }
    },
    "attributes": {
        "align": {
            "type": "string",
            "default": "full"
        },
        "intro": {
            "type": "string",
            "default": ""
        },
        "showOnFrontPage": {
            "type": "boolean",
            "default": false
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-featured-image-header',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(RichText, {
                        tagName: 'p',
                        label: __('Intro Text', 'balefire'),
                        value: attributes.intro,
                        onChange: (value) => setAttributes({ intro: value }),
                        placeholder: __('Optional intro paragraph below the title...', 'balefire'),
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                    })
                ),
                el(PanelBody, { title: __('Settings', 'balefire') },
                    el(ToggleControl, {
                        label: __('Show on front page', 'balefire'),
                        help: __('By default this block is hidden on the front page. Enable to show it there.', 'balefire'),
                        checked: attributes.showOnFrontPage,
                        onChange: (value) => setAttributes({ showOnFrontPage: value }),
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
