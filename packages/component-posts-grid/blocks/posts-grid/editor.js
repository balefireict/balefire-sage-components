(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/posts-grid",
    "title": "Posts Grid",
    "category": "balefire",
    "icon": "screenoptions",
    "description": "A dynamic posts grid that respects Balefire tokens and plugin defaults.",
    "keywords": [
        "posts",
        "grid",
        "query",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-posts-grid-editor",
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
        "intro": {
            "type": "string",
            "default": ""
        },
        "postsPerPage": {
            "type": "number",
            "default": 0
        },
        "columns": {
            "type": "number",
            "default": 3
        },
        "showExcerpt": {
            "type": "boolean",
            "default": true
        },
        "showDate": {
            "type": "boolean",
            "default": true
        },
        "heading": {
            "type": "string",
            "default": ""
        },
        "maxWidth": {
            "type": "string",
            "default": "wide"
        },
        "showAuthor": {
            "type": "boolean",
            "default": true
        },
        "backgroundTone": {
            "type": "string",
            "default": "transparent"
        }
    },
    "version": "1.0.0",
    "style": "balefire-posts-grid"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-posts-grid',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Posts Grid Settings', 'balefire') },
                    el(RangeControl, {
                        label: __('Posts per page', 'balefire'),
                        value: attributes.postsPerPage || 3,
                        min: 1,
                        max: 12,
                        onChange: (value) => setAttributes({ postsPerPage: value }),
                    }),
                    el(RangeControl, {
                        label: __('Columns', 'balefire'),
                        value: attributes.columns || 3,
                        min: 1,
                        max: 3,
                        onChange: (value) => setAttributes({ columns: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show excerpt', 'balefire'),
                        checked: !!attributes.showExcerpt,
                        onChange: (value) => setAttributes({ showExcerpt: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show date', 'balefire'),
                        checked: !!attributes.showDate,
                        onChange: (value) => setAttributes({ showDate: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show author', 'balefire'),
                        checked: !!attributes.showAuthor,
                        onChange: (value) => setAttributes({ showAuthor: value }),
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
                el(TextControl, {
                    label: __('Heading', 'balefire'),
                    value: attributes.heading,
                    onChange: (value) => setAttributes({ heading: value }),
                }),
                el(TextControl, {
                    label: __('Intro text', 'balefire'),
                    value: attributes.intro,
                    onChange: (value) => setAttributes({ intro: value }),
                }),
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

})();
