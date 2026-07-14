(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/product-highlight-bar",
    "title": "Product Highlight Bar",
    "category": "balefire",
    "icon": "awards",
    "description": "Row of trust-signal highlights — icon, heading, and a small link. Ships four default icons; each item can take pasted SVG instead.",
    "keywords": ["highlight", "trust", "usp", "icons", "bar", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "items": {
            "type": "array",
            "default": [
                { "icon": "award", "customSvg": "", "heading": "Patented Since 2000", "linkText": "Our story", "linkUrl": "" },
                { "icon": "tools", "customSvg": "", "heading": "Lifetime Warranty", "linkText": "If it breaks, we fix it", "linkUrl": "" },
                { "icon": "police-car", "customSvg": "", "heading": "Trusted by Military & LE", "linkText": "NSN-listed", "linkUrl": "" },
                { "icon": "shield-check", "customSvg": "", "heading": "Authentic Quality", "linkText": "Spot the counterfeits", "linkUrl": "" }
            ]
        },
        "headingLevel": { "type": "string", "default": "h2" },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-product-highlight-bar-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, Button } = wp.components;
const { createElement: el, Fragment } = wp.element;

const ICON_OPTIONS = [
    { label: __('Award', 'balefire'), value: 'award' },
    { label: __('Tools', 'balefire'), value: 'tools' },
    { label: __('Police Car', 'balefire'), value: 'police-car' },
    { label: __('Shield Check', 'balefire'), value: 'shield-check' },
];

const HEADING_LEVEL_OPTIONS = [
    { label: 'H2', value: 'h2' },
    { label: 'H3', value: 'h3' },
    { label: 'H4', value: 'h4' },
    { label: 'H5', value: 'h5' },
    { label: 'H6', value: 'h6' },
    { label: __('Paragraph (no heading)', 'balefire'), value: 'p' },
];

const EMPTY_ITEM = { icon: 'award', customSvg: '', heading: '', linkText: '', linkUrl: '' };

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

        const removeItem = (index) =>
            setAttributes({ items: items.filter((_, i) => i !== index) });

        const moveItem = (index, delta) => {
            const target = index + delta;
            if (target < 0 || target >= items.length) {
                return;
            }
            const next = [...items];
            [next[index], next[target]] = [next[target], next[index]];
            setAttributes({ items: next });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-product-highlight-bar',
            style: {
                background: '#2e2e2e',
                color: '#fff',
                padding: '32px',
                display: 'flex',
                flexWrap: 'wrap',
                gap: '32px',
            },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Heading', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Heading level', 'balefire'),
                        help: __('Each item\'s title uses this tag. Several H2s in one bar can flatten the page outline — drop to H3 if the bar sits under another heading.', 'balefire'),
                        value: attributes.headingLevel || 'h2',
                        options: HEADING_LEVEL_OPTIONS,
                        onChange: (value) => setAttributes({ headingLevel: value }),
                    })
                ),

                items.map((item, index) => el(PanelBody, {
                    key: index,
                    title: (item.heading || __('Untitled', 'balefire')) + ' — ' + __('Item', 'balefire') + ' ' + (index + 1),
                    initialOpen: false,
                },
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: item.heading || '',
                        onChange: (value) => updateItem(index, { heading: value }),
                    }),
                    el(TextControl, {
                        label: __('Link text', 'balefire'),
                        value: item.linkText || '',
                        onChange: (value) => updateItem(index, { linkText: value }),
                    }),
                    el(TextControl, {
                        label: __('Link URL', 'balefire'),
                        type: 'url',
                        help: __('Leave empty to show the text without a link.', 'balefire'),
                        value: item.linkUrl || '',
                        onChange: (value) => updateItem(index, { linkUrl: value }),
                    }),
                    el(SelectControl, {
                        label: __('Icon', 'balefire'),
                        value: item.icon || 'award',
                        options: ICON_OPTIONS,
                        onChange: (value) => updateItem(index, { icon: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Custom SVG', 'balefire'),
                        help: __('Paste SVG markup to override the icon above. Sanitized on save: scripts, event handlers and external references are stripped. Use a 32x32 viewBox and fill="currentColor" so it matches.', 'balefire'),
                        value: item.customSvg || '',
                        onChange: (value) => updateItem(index, { customSvg: value }),
                    }),
                    el('div', { style: { display: 'flex', gap: '8px', marginTop: '8px' } },
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
                            isDestructive: true,
                            variant: 'tertiary',
                            onClick: () => removeItem(index),
                        }, __('Remove', 'balefire'))
                    )
                )),

                el(PanelBody, { title: __('Add item', 'balefire'), initialOpen: false },
                    el(Button, { variant: 'primary', onClick: addItem }, __('Add item', 'balefire'))
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                items.length === 0
                    ? el('p', { style: { opacity: 0.7, margin: 0 } }, __('No items yet — add one from the block sidebar.', 'balefire'))
                    : items.map((item, index) => el('div', {
                        key: index,
                        style: { display: 'flex', gap: '12px', alignItems: 'flex-start', flex: '1 0 180px' },
                    },
                        el('span', {
                            style: {
                                width: '32px',
                                height: '32px',
                                flexShrink: 0,
                                borderRadius: '4px',
                                background: 'rgba(255,255,255,0.12)',
                            },
                        }),
                        el('div', null,
                            el('strong', {
                                style: { display: 'block', textTransform: 'uppercase', fontSize: '16px', lineHeight: '16px' },
                            }, item.heading || __('Heading', 'balefire')),
                            item.linkText && el('span', {
                                style: { display: 'block', marginTop: '5px', fontFamily: 'monospace', fontSize: '12px', opacity: 0.8 },
                            }, item.linkText + (item.linkUrl ? ' →' : ''))
                        )
                    ))
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
