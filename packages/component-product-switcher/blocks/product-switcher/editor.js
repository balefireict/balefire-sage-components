(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/product-switcher",
    "title": "Product Switcher",
    "category": "balefire",
    "icon": "images-alt2",
    "description": "Product image with pill tabs that switch between WooCommerce products, alongside a headline, copy and a shared CTA.",
    "keywords": ["product", "switcher", "tabs", "woocommerce", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "Complete Your Setup" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "items": { "type": "array", "default": [] },
        "ctaLabel": { "type": "string", "default": "" },
        "ctaUrl": { "type": "string", "default": "" },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-product-switcher-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button, Notice } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Injected by src/bootstrap.php.
const PRODUCTS = Array.isArray(window.balefireProducts) ? window.balefireProducts : [];
const CATEGORIES = Array.isArray(window.balefireProductCats) ? window.balefireProductCats : [];
const ATTRIBUTES = Array.isArray(window.balefireProductAttrs) ? window.balefireProductAttrs : [];

const PRODUCT_OPTIONS = [
    { label: __('— Select a product —', 'balefire'), value: 0 },
    ...PRODUCTS.map((p) => ({ label: p.name, value: p.id })),
];

const CATEGORY_OPTIONS = [
    { label: __('— Select a category —', 'balefire'), value: 0 },
    ...CATEGORIES.map((c) => ({ label: c.name, value: c.id })),
];

const ATTRIBUTE_OPTIONS = [
    { label: __('— Select an attribute —', 'balefire'), value: '' },
    ...ATTRIBUTES.map((a) => ({ label: a.label, value: a.taxonomy })),
];

const SOURCE_OPTIONS = [
    { label: __('Category + attribute (automatic)', 'balefire'), value: 'attribute' },
    { label: __('Hand-picked products', 'balefire'), value: 'products' },
];

const EMPTY_ITEM = { productId: 0, label: '', imageId: 0, url: '' };

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const items = Array.isArray(attributes.items) ? attributes.items : [];

        const updateItem = (index, changes) => {
            setAttributes({
                items: items.map((item, i) => (i === index ? { ...item, ...changes } : item)),
            });
        };

        const addItem = () => setAttributes({ items: [...items, { ...EMPTY_ITEM }] });
        const removeItem = (index) => setAttributes({ items: items.filter((_, i) => i !== index) });

        const moveItem = (index, delta) => {
            const target = index + delta;
            if (target < 0 || target >= items.length) return;
            const next = [...items];
            [next[index], next[target]] = [next[target], next[index]];
            setAttributes({ items: next });
        };

        const nameOf = (item) => {
            if (item.label) return item.label;
            const p = PRODUCTS.find((x) => x.id === Number(item.productId));
            return p ? p.name : __('Untitled', 'balefire');
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-product-switcher',
            style: { background: '#fff', padding: '32px', border: '1px solid #e8e8e8' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Description', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    })
                ),

                el(PanelBody, { title: __('Call to action', 'balefire'), initialOpen: false },
                    el(TextControl, {
                        label: __('Button label', 'balefire'),
                        value: attributes.ctaLabel || '',
                        onChange: (value) => setAttributes({ ctaLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('Button URL', 'balefire'),
                        type: 'url',
                        help: __('Shared by every tab — it does not change when you switch. A root-relative path like /product-category/monopods works.', 'balefire'),
                        value: attributes.ctaUrl || '',
                        onChange: (value) => setAttributes({ ctaUrl: value }),
                    })
                ),

                el(PanelBody, { title: __('Tabs', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Where do the tabs come from?', 'balefire'),
                        value: attributes.source || 'products',
                        options: SOURCE_OPTIONS,
                        onChange: (value) => setAttributes({ source: value }),
                    }),

                    PRODUCTS.length === 0 && el(Notice, { status: 'warning', isDismissible: false },
                        __('No products found. Is WooCommerce active?', 'balefire')),

                    attributes.source === 'attribute' && el(Fragment, null,
                        el(SelectControl, {
                            label: __('Product category', 'balefire'),
                            value: Number(attributes.categoryId) || 0,
                            options: CATEGORY_OPTIONS,
                            onChange: (value) => setAttributes({ categoryId: Number(value) }),
                        }),
                        el(SelectControl, {
                            label: __('Attribute', 'balefire'),
                            help: __('One tab per attribute term used inside that category. Tag a new product and its tab appears on its own. Set each term\'s image under Products > Attributes > (attribute) > Configure terms.', 'balefire'),
                            value: attributes.attribute || '',
                            options: ATTRIBUTE_OPTIONS,
                            onChange: (value) => setAttributes({ attribute: value }),
                        })
                    ),

                    attributes.source !== 'attribute' && el('p', { style: { marginBottom: 0 } },
                        __('Tabs are the hand-picked products listed below.', 'balefire'))
                ),

                attributes.source !== 'attribute' && items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: nameOf(item) + ' — ' + __('Tab', 'balefire') + ' ' + (index + 1),
                    initialOpen: false,
                },
                    el(SelectControl, {
                        label: __('Product', 'balefire'),
                        help: __('Image, link and label default to this product.', 'balefire'),
                        value: Number(item.productId) || 0,
                        options: PRODUCT_OPTIONS,
                        onChange: (value) => updateItem(index, { productId: Number(value) }),
                    }),
                    el(TextControl, {
                        label: __('Tab label', 'balefire'),
                        help: __('Short — e.g. "Picatinny Rail". Leave empty to use the product name.', 'balefire'),
                        value: item.label || '',
                        onChange: (value) => updateItem(index, { label: value }),
                    }),
                    el(TextControl, {
                        label: __('Link override', 'balefire'),
                        type: 'url',
                        help: __('Leave empty to use the product permalink.', 'balefire'),
                        value: item.url || '',
                        onChange: (value) => updateItem(index, { url: value }),
                    }),
                    el(MediaUpload, {
                        onSelect: (media) => updateItem(index, { imageId: media.id || 0 }),
                        allowedTypes: ['image'],
                        value: item.imageId,
                        render: ({ open }) => el(Button, {
                            variant: 'secondary',
                            onClick: open,
                        }, item.imageId
                            ? __('Change image override', 'balefire')
                            : __('Override image', 'balefire')),
                    }),
                    item.imageId ? el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        style: { display: 'block', marginTop: '4px' },
                        onClick: () => updateItem(index, { imageId: 0 }),
                    }, __('Use product image', 'balefire')) : null,
                    el('div', { style: { display: 'flex', gap: '8px', marginTop: '12px' } },
                        el(Button, {
                            variant: 'secondary',
                            disabled: index === 0,
                            onClick: () => moveItem(index, -1),
                        }, __('Move up', 'balefire')),
                        el(Button, {
                            variant: 'secondary',
                            disabled: index === items.length - 1,
                            onClick: () => moveItem(index, 1),
                        }, __('Move down', 'balefire')),
                        el(Button, {
                            variant: 'tertiary',
                            isDestructive: true,
                            onClick: () => removeItem(index),
                        }, __('Remove', 'balefire'))
                    )
                )),

                attributes.source !== 'attribute' && el(PanelBody, { title: __('Add tab', 'balefire'), initialOpen: items.length === 0 },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add tab', 'balefire'))
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                    attributes.eyebrow || ''),
                el('h2', { style: { margin: '0 0 8px', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.1 } },
                    attributes.title || __('Product Switcher', 'balefire')),
                attributes.content && el('p', { style: { color: '#747474', maxWidth: '520px', margin: '0 0 16px' } },
                    attributes.content),
                el('div', { style: { display: 'flex', gap: '12px', flexWrap: 'wrap', marginBottom: '16px' } },
                    attributes.source === 'attribute'
                        ? el('em', { style: { color: '#747474' } },
                            attributes.categoryId && attributes.attribute
                                ? __('Tabs are generated on the front end from the terms of ', 'balefire')
                                    + (ATTRIBUTES.find((a) => a.taxonomy === attributes.attribute) || {}).label
                                    + __(' used in ', 'balefire')
                                    + ((CATEGORIES.find((c) => c.id === Number(attributes.categoryId)) || {}).name || '')
                                    + '.'
                                : __('Pick a category and an attribute in the sidebar.', 'balefire'))
                    : items.length === 0
                        ? el('em', { style: { color: '#747474' } }, __('No tabs yet — add one from the block sidebar.', 'balefire'))
                        : items.map((item, i) => el('span', {
                            key: i,
                            style: {
                                border: '1px solid ' + (i === 0 ? '#760604' : '#a2a2a2'),
                                background: i === 0 ? '#760604' : 'transparent',
                                color: i === 0 ? '#fff' : '#a2a2a2',
                                borderRadius: '4px',
                                padding: '10px 14px',
                                fontFamily: 'monospace',
                                fontWeight: 700,
                                fontSize: '12px',
                                textTransform: 'uppercase',
                            },
                        }, nameOf(item)))
                ),
                attributes.ctaLabel && el('span', {
                    style: {
                        display: 'inline-block',
                        background: '#d72b27',
                        color: '#fff',
                        padding: '14px 16px',
                        borderRadius: '4px',
                        fontFamily: 'monospace',
                        fontWeight: 700,
                        textTransform: 'uppercase',
                    },
                }, attributes.ctaLabel + ' →')
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
