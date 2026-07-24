(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/image-marquee",
    "title": "Image Marquee",
    "category": "balefire",
    "icon": "images-alt2",
    "description": "Infinitely scrolling image rows with alternating direction, optional header and CTA.",
    "keywords": ["marquee", "scroll", "patches", "logos", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": { "anchor": true, "className": true, "align": ["full"] },
    "attributes": {
        "align": { "type": "string", "default": "full" },
        "tone": { "type": "string", "default": "dark" },
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "primaryLabel": { "type": "string", "default": "" },
        "primaryUrl": { "type": "string", "default": "" },
        "imageIds": { "type": "array", "default": [], "items": { "type": "number" } },
        "rows": { "type": "number", "default": 3 },
        "duration": { "type": "number", "default": 90 }
    },
    "editorScript": "balefire-image-marquee-editor"
};

const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, RangeControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { __ } = wp.i18n;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, setAttributes }) {
        const blockProps = useBlockProps({
            style: {
                padding: '2rem',
                background: attributes.tone === 'dark' ? '#171717' : attributes.tone === 'grey' ? '#f5f4f2' : '#fff',
                color: attributes.tone === 'dark' ? '#fff' : '#1a1a1a',
                borderRadius: '4px',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Marquee', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone,
                        options: [
                            { label: 'Dark', value: 'dark' },
                            { label: 'White', value: 'white' },
                            { label: 'Grey', value: 'grey' },
                        ],
                        onChange: (tone) => setAttributes({ tone }),
                    }),
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow,
                        onChange: (eyebrow) => setAttributes({ eyebrow }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (title) => setAttributes({ title }),
                    }),
                    el(TextareaControl, {
                        label: __('Content', 'balefire'),
                        value: attributes.content,
                        onChange: (content) => setAttributes({ content }),
                    }),
                    el(TextControl, {
                        label: __('CTA label', 'balefire'),
                        value: attributes.primaryLabel,
                        onChange: (primaryLabel) => setAttributes({ primaryLabel }),
                    }),
                    el(TextControl, {
                        label: __('CTA URL', 'balefire'),
                        value: attributes.primaryUrl,
                        onChange: (primaryUrl) => setAttributes({ primaryUrl }),
                    }),
                    el(RangeControl, {
                        label: __('Rows', 'balefire'),
                        min: 1,
                        max: 4,
                        value: attributes.rows,
                        onChange: (rows) => setAttributes({ rows }),
                    }),
                    el(RangeControl, {
                        label: __('Seconds per loop', 'balefire'),
                        min: 20,
                        max: 240,
                        value: attributes.duration,
                        onChange: (duration) => setAttributes({ duration }),
                    })
                )
            ),
            el('div', blockProps,
                el('strong', null, attributes.title || __('Image Marquee', 'balefire')),
                el('p', { style: { margin: '0.5em 0 0', opacity: 0.7 } },
                    (attributes.imageIds || []).length + ' ' + __('images in', 'balefire') + ' ' + attributes.rows + ' ' + __('rows — rendered on the front end', 'balefire'))
            )
        );
    },
    save: () => null,
});
})();
