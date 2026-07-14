(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/eyebrow",
    "title": "Eyebrow",
    "category": "balefire",
    "icon": "minus",
    "description": "Brand eyebrow lockup: flanking marks around a short uppercase label. Inherits the surrounding text color.",
    "keywords": ["eyebrow", "preheader", "kicker", "label", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "text": { "type": "string", "default": "" },
        "showLeftMark": { "type": "boolean", "default": true },
        "showRightMark": { "type": "boolean", "default": true }
    },
    "editorScript": "balefire-eyebrow-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-eyebrow',
            style: {
                display: 'flex',
                alignItems: 'center',
                gap: '8px',
                color: '#d72b27',
                fontWeight: 700,
                textTransform: 'uppercase',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Marks', 'balefire'), initialOpen: true },
                    el(ToggleControl, {
                        label: __('Show left mark', 'balefire'),
                        checked: !!attributes.showLeftMark,
                        onChange: (value) => setAttributes({ showLeftMark: value }),
                    }),
                    el(ToggleControl, {
                        label: __('Show right mark', 'balefire'),
                        checked: !!attributes.showRightMark,
                        onChange: (value) => setAttributes({ showRightMark: value }),
                    })
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('p', blockProps,
                attributes.showLeftMark && el('span', { 'aria-hidden': true }, '≡'),
                el(RichText, {
                    tagName: 'span',
                    value: attributes.text || '',
                    allowedFormats: [],
                    onChange: (value) => setAttributes({ text: value }),
                    placeholder: __('Eyebrow text…', 'balefire'),
                }),
                attributes.showRightMark && el('span', { 'aria-hidden': true }, '➤')
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
