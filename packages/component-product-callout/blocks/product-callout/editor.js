(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/product-callout",
    "title": "Product Callout",
    "category": "balefire",
    "icon": "products",
    "description": "White panel card with heading, supporting copy, and live product mini-cards (image, price, Add/View) referenced by SKU or ID.",
    "keywords": ["product", "callout", "card", "upsell", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "title": { "type": "string", "default": "" },
        "text": { "type": "string", "default": "" },
        "products": { "type": "string", "default": "" }
    },
    "editorScript": "balefire-product-callout-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const refs = (attributes.products || '')
            .split(',')
            .map((r) => r.trim())
            .filter(Boolean);

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Product Callout', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: attributes.title,
                        onChange: (title) => setAttributes({ title }),
                    }),
                    el(TextareaControl, {
                        label: __('Text', 'balefire'),
                        value: attributes.text,
                        onChange: (text) => setAttributes({ text }),
                    }),
                    el(TextControl, {
                        label: __('Products (SKUs or IDs, comma-separated)', 'balefire'),
                        help: __('e.g. BT01-QK, BT05-QK, BT06-QK — price, image, and stock render live on the front end.', 'balefire'),
                        value: attributes.products,
                        onChange: (products) => setAttributes({ products }),
                    })
                )
            ),
            el('div', { ...useBlockProps({ style: { border: '1px solid #e5e5e5', borderRadius: '12px', padding: '20px', background: '#fff' } }) },
                el('strong', { style: { display: 'block', textTransform: 'uppercase', fontSize: '18px' } },
                    attributes.title || __('Product Callout', 'balefire')),
                attributes.text
                    ? el('p', { style: { color: '#777', margin: '8px 0' } }, attributes.text)
                    : null,
                el('p', { style: { color: '#999', fontSize: '12px', margin: 0 } },
                    refs.length
                        ? __('Products: ', 'balefire') + refs.join(', ')
                        : __('No products selected — set SKUs or IDs in the sidebar.', 'balefire'))
            )
        );
    },
    save: () => null,
});
})();
