(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/prose-section",
    "title": "Prose Section",
    "category": "balefire",
    "icon": "text-page",
    "description": "Alternating white/grey page section with eyebrow, uppercase heading, and a constrained prose body (paragraphs, lists, tables, nested blocks).",
    "keywords": ["prose", "section", "text", "copy", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full"]
    },
    "attributes": {
        "tone": { "type": "string", "default": "white" },
        "align": { "type": "string", "default": "full" },
        "contentAlign": { "type": "string", "default": "left" },
        "eyebrow": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-prose-section-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, InnerBlocks, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, TextControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const TEMPLATE = [['core/paragraph']];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const isGrey = attributes.tone === 'grey';
        const isCentered = attributes.contentAlign === 'center';

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-prose-section',
            style: {
                background: isGrey ? '#f4f4f4' : '#ffffff',
                padding: '40px',
            },
        });

        const innerBlocksProps = useInnerBlocksProps(
            {
                style: {
                    maxWidth: '48rem',
                    margin: isCentered ? '0 auto' : undefined,
                    textAlign: isCentered ? 'center' : undefined,
                },
            },
            {
                template: TEMPLATE,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Section Settings', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        value: attributes.tone || 'white',
                        options: [
                            { label: __('White', 'balefire'), value: 'white' },
                            { label: __('Grey', 'balefire'), value: 'grey' },
                        ],
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(SelectControl, {
                        label: __('Content alignment', 'balefire'),
                        value: attributes.contentAlign || 'left',
                        options: [
                            { label: __('Left', 'balefire'), value: 'left' },
                            { label: __('Center', 'balefire'), value: 'center' },
                        ],
                        onChange: (value) => setAttributes({ contentAlign: value }),
                    }),
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    })
                )
            ),
            el('section', blockProps,
                el('div', { style: { maxWidth: '48rem', margin: isCentered ? '0 auto' : undefined, textAlign: isCentered ? 'center' : undefined } },
                    attributes.eyebrow ? el('p', {
                        style: { color: '#d72b27', textTransform: 'uppercase', fontSize: '12px', fontWeight: 700, letterSpacing: '0.16em', margin: '0 0 12px' },
                    }, attributes.eyebrow) : null,
                    attributes.title ? el('h2', {
                        style: { textTransform: 'uppercase', fontSize: '28px', lineHeight: 1.05, margin: '0 0 16px', color: '#171717' },
                    }, attributes.title) : null
                ),
                el('div', innerBlocksProps)
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
