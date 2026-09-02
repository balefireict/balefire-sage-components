(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/eyebrow",
    "title": "Eyebrow",
    "category": "balefire",
    "icon": "minus",
    "description": "Brand eyebrow lockup: flanking marks, a short rule, or a bare uppercase label. Inherits the surrounding text color.",
    "keywords": ["eyebrow", "preheader", "kicker", "label", "balefire"],
    "textdomain": "balefire",
    "version": "1.1.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "text": { "type": "string", "default": "" },
        "variant": { "type": "string", "default": "marks" },
        "align": { "type": "string", "default": "left" },
        "showLeftMark": { "type": "boolean", "default": true },
        "showRightMark": { "type": "boolean", "default": true }
    },
    "editorScript": "balefire-eyebrow-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
const { PanelBody, ToggleControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const VARIANTS = [
    { label: __('Marks (flanking brand marks)', 'balefire'), value: 'marks' },
    { label: __('Bar (short rule before the label)', 'balefire'), value: 'bar' },
    { label: __('Plain (label only)', 'balefire'), value: 'plain' },
];

const ALIGNS = [
    { label: __('Left', 'balefire'), value: 'left' },
    { label: __('Center', 'balefire'), value: 'center' },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const variant = attributes.variant || 'marks';
        const align = attributes.align || 'left';

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-eyebrow',
            style: {
                display: 'flex',
                alignItems: 'center',
                justifyContent: align === 'center' ? 'center' : 'flex-start',
                gap: '8px',
                color: '#d72b27',
                fontWeight: 700,
                textTransform: 'uppercase',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Style', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Variant', 'balefire'),
                        value: variant,
                        options: VARIANTS,
                        onChange: (value) => setAttributes({ variant: value }),
                    }),
                    el(SelectControl, {
                        label: __('Alignment', 'balefire'),
                        value: align,
                        options: ALIGNS,
                        onChange: (value) => setAttributes({ align: value }),
                    })
                ),
                variant === 'marks' && el(PanelBody, { title: __('Marks', 'balefire'), initialOpen: true },
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
                variant === 'marks' && attributes.showLeftMark && el('span', { 'aria-hidden': true }, '≡'),
                variant === 'bar' && el('span', {
                    'aria-hidden': true,
                    style: { display: 'inline-block', width: '26px', height: '2px', background: 'currentColor', borderRadius: '2px' },
                }),
                el(RichText, {
                    tagName: 'span',
                    value: attributes.text || '',
                    allowedFormats: [],
                    onChange: (value) => setAttributes({ text: value }),
                    placeholder: __('Eyebrow text…', 'balefire'),
                }),
                variant === 'marks' && attributes.showRightMark && el('span', { 'aria-hidden': true }, '➤')
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
