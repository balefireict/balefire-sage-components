(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/faq-items",
    "title": "FAQ Items",
    "category": "balefire",
    "icon": "editor-help",
    "description": "FAQ section: optional eyebrow, heading, intro and CTA above (or beside) a list of accordion items, hand-authored or pulled from a post type.",
    "keywords": [
        "faq",
        "accordion",
        "items",
        "group",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-faq-items-editor",
    "style": "balefire-faq-items",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "wide",
            "full"
        ]
    },
    "allowedBlocks": [
        "balefire/faq-no-borders"
    ],
    "attributes": {
        "tabs": { "type": "array", "default": [] },
        "eyebrow": { "type": "string", "default": "" },
        "eyebrowVariant": { "type": "string", "default": "" },
        "title": { "type": "string", "default": "" },
        "content": { "type": "string", "default": "" },
        "ctaLabel": { "type": "string", "default": "" },
        "ctaUrl": { "type": "string", "default": "" },
        "layout": { "type": "string", "default": "stack" },
        "tone": { "type": "string", "default": "" },
        "source": { "type": "string", "default": "inner" },
        "postType": { "type": "string", "default": "faq" },
        "taxonomy": { "type": "string", "default": "faq_topic" },
        "termIds": { "type": "array", "default": [] },
        "limit": { "type": "number", "default": 8 },
        "orderBy": { "type": "string", "default": "menu_order" },
        "exclusive": { "type": "boolean", "default": false },
        "emitSchema": { "type": "boolean", "default": false },
        "align": { "type": "string" }
    },
    "version": "1.1.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InnerBlocks, InspectorControls, useBlockProps, useInnerBlocksProps } = wp.blockEditor;
const { PanelBody, RangeControl, SelectControl, TextControl, TextareaControl, ToggleControl } = wp.components;
const { createElement: el, Fragment, useState } = wp.element;

const ALLOWED_BLOCKS = ['balefire/faq-no-borders'];
const TEMPLATE = [
    ['balefire/faq-no-borders'],
    ['balefire/faq-no-borders'],
];

// Mirrors SectionStyles::tones() in component-support. The empty value
// keeps the theme's own background and its own item styling (legacy).
const TONES = [
    { label: __('Theme default (transparent)', 'balefire'), value: '' },
    { label: __('White', 'balefire'), value: 'white' },
    { label: __('Light', 'balefire'), value: 'light' },
    { label: __('Surface', 'balefire'), value: 'surface' },
    { label: __('Surface (muted)', 'balefire'), value: 'surface-muted' },
    { label: __('Primary', 'balefire'), value: 'primary' },
    { label: __('Secondary', 'balefire'), value: 'secondary' },
    { label: __('Accent', 'balefire'), value: 'accent' },
    { label: __('Dark', 'balefire'), value: 'dark' },
];

const LAYOUTS = [
    { label: __('Stack (heading above list)', 'balefire'), value: 'stack' },
    { label: __('Split (heading beside list)', 'balefire'), value: 'split' },
];

const SOURCES = [
    { label: __('Hand-authored items (inner blocks)', 'balefire'), value: 'inner' },
    { label: __('Query a post type', 'balefire'), value: 'query' },
];

const ORDER_BY = [
    { label: __('Menu order', 'balefire'), value: 'menu_order' },
    { label: __('Title (A–Z)', 'balefire'), value: 'title' },
    { label: __('Date (newest first)', 'balefire'), value: 'date' },
];

const EYEBROW_VARIANTS = [
    { label: __('Component default', 'balefire'), value: '' },
    { label: __('Marks', 'balefire'), value: 'marks' },
    { label: __('Bar', 'balefire'), value: 'bar' },
    { label: __('Plain', 'balefire'), value: 'plain' },
];

// "3, 12,9" -> [3, 12, 9]; anything that is not a positive integer is dropped.
const parseIds = (text) => String(text || '')
    .split(',')
    .map((part) => parseInt(part.trim(), 10))
    .filter((n) => Number.isInteger(n) && n > 0);

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const layout = attributes.layout === 'split' ? 'split' : 'stack';
        const isQuery = attributes.source === 'query';

        // Local text so a trailing comma survives while the editor is still
        // typing; the parsed ids are what get saved.
        const [termText, setTermText] = useState((attributes.termIds || []).join(', '));

        const blockProps = useBlockProps({
            className: [
                'faq-section-items',
                'bma-faq-items',
                'bma-faq-items--' + layout,
                'bma-editor-preview',
                'bma-editor-preview-faq-items',
            ].join(' '),
            'data-bma-tone': attributes.tone || undefined,
        });

        // Hooks run unconditionally; the inner-blocks node is simply not
        // rendered in query mode.
        const innerBlocksProps = useInnerBlocksProps(
            { className: 'faq-section-list' },
            {
                allowedBlocks: ALLOWED_BLOCKS,
                template: TEMPLATE,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        const hasHead = !!(attributes.eyebrow || attributes.title || attributes.content || attributes.ctaLabel);

        const head = el('div', { className: 'bma-faq-items__head' },
            attributes.eyebrow ? el('p', {
                className: 'bma-faq-items__eyebrow',
                style: { margin: 0, fontSize: '0.85rem', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em' },
            }, attributes.eyebrow) : null,
            el('h2', {
                className: 'bma-heading bma-faq-items__title',
                style: { margin: attributes.eyebrow ? '0.6rem 0 0' : 0 },
                // Title allows <em> for the accent clause; this is the editor's own input.
                dangerouslySetInnerHTML: { __html: attributes.title || __('Frequently Asked Questions', 'balefire') },
            }),
            attributes.content ? el('p', {
                className: 'bma-faq-items__intro',
                style: { marginTop: '0.75rem' },
            }, attributes.content) : null,
            attributes.ctaLabel ? el('p', { className: 'bma-faq-items__cta', style: { marginTop: '1rem' } },
                el('span', { className: 'bma-btn bma-btn--primary' }, attributes.ctaLabel)
            ) : null
        );

        const queryPlaceholder = el('div', {
            className: 'bma-faq-items__placeholder',
            style: { padding: '1rem 1.25rem', border: '1px dashed currentColor', borderRadius: '8px', opacity: 0.8 },
        },
            el('strong', null, __('FAQ items are pulled from a query on the front end.', 'balefire')),
            el('ul', { style: { margin: '0.5rem 0 0', paddingLeft: '1.2rem', fontSize: '0.9rem' } },
                el('li', null, __('Post type: ', 'balefire'), attributes.postType || 'faq'),
                el('li', null, __('Taxonomy: ', 'balefire'), attributes.taxonomy || 'faq_topic',
                    (attributes.termIds || []).length
                        ? ' (' + __('terms', 'balefire') + ' ' + attributes.termIds.join(', ') + ')'
                        : ' (' + __('all terms', 'balefire') + ')'),
                el('li', null, __('Limit: ', 'balefire'), attributes.limit > 0 ? attributes.limit : __('all', 'balefire')),
                el('li', null, __('Order: ', 'balefire'), (ORDER_BY.find((o) => o.value === attributes.orderBy) || ORDER_BY[0]).label),
                el('li', null, __('One open at a time: ', 'balefire'), attributes.exclusive ? __('yes', 'balefire') : __('no', 'balefire')),
                el('li', null, __('FAQPage schema: ', 'balefire'), attributes.emitSchema ? __('yes', 'balefire') : __('no', 'balefire'))
            )
        );

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(SelectControl, {
                        label: __('Eyebrow style', 'balefire'),
                        value: attributes.eyebrowVariant || '',
                        options: EYEBROW_VARIANTS,
                        onChange: (value) => setAttributes({ eyebrowVariant: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        help: __('Leave empty for "Frequently Asked Questions". Wrap the accent clause in <em>…</em>.', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro copy', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    }),
                    el(TextControl, {
                        label: __('Button label', 'balefire'),
                        value: attributes.ctaLabel || '',
                        onChange: (value) => setAttributes({ ctaLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('Button URL', 'balefire'),
                        value: attributes.ctaUrl || '',
                        onChange: (value) => setAttributes({ ctaUrl: value }),
                    })
                ),
                el(PanelBody, { title: __('Layout', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Layout', 'balefire'),
                        value: layout,
                        options: LAYOUTS,
                        onChange: (value) => setAttributes({ layout: value }),
                    }),
                    el(SelectControl, {
                        label: __('Tone', 'balefire'),
                        help: __('Any tone also applies the packaged accordion styling; the theme default leaves item styling to the theme.', 'balefire'),
                        value: attributes.tone || '',
                        options: TONES,
                        onChange: (value) => setAttributes({ tone: value }),
                    }),
                    el(ToggleControl, {
                        label: __('One open at a time', 'balefire'),
                        help: __('Opening a question closes the others (native, no JavaScript).', 'balefire'),
                        checked: !!attributes.exclusive,
                        onChange: (value) => setAttributes({ exclusive: !!value }),
                    }),
                    el(ToggleControl, {
                        label: __('Emit FAQPage schema', 'balefire'),
                        help: __('Adds JSON-LD structured data for the rendered questions on the front end.', 'balefire'),
                        checked: !!attributes.emitSchema,
                        onChange: (value) => setAttributes({ emitSchema: !!value }),
                    })
                ),
                el(PanelBody, { title: __('Source', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Items come from', 'balefire'),
                        value: isQuery ? 'query' : 'inner',
                        options: SOURCES,
                        onChange: (value) => setAttributes({ source: value === 'query' ? 'query' : 'inner' }),
                    }),
                    isQuery ? el(TextControl, {
                        label: __('Post type', 'balefire'),
                        value: attributes.postType || '',
                        onChange: (value) => setAttributes({ postType: value }),
                    }) : null,
                    isQuery ? el(TextControl, {
                        label: __('Taxonomy', 'balefire'),
                        value: attributes.taxonomy || '',
                        onChange: (value) => setAttributes({ taxonomy: value }),
                    }) : null,
                    isQuery ? el(TextControl, {
                        label: __('Term IDs', 'balefire'),
                        help: __('Comma-separated term IDs in the taxonomy above. Empty means every term.', 'balefire'),
                        value: termText,
                        onChange: (value) => {
                            setTermText(value);
                            setAttributes({ termIds: parseIds(value) });
                        },
                    }) : null,
                    isQuery ? el(RangeControl, {
                        label: __('Limit', 'balefire'),
                        help: __('0 shows every published item.', 'balefire'),
                        value: typeof attributes.limit === 'number' ? attributes.limit : 8,
                        min: 0,
                        max: 50,
                        onChange: (value) => setAttributes({ limit: typeof value === 'number' ? value : 8 }),
                    }) : null,
                    isQuery ? el(SelectControl, {
                        label: __('Order by', 'balefire'),
                        value: attributes.orderBy || 'menu_order',
                        options: ORDER_BY,
                        onChange: (value) => setAttributes({ orderBy: value }),
                    }) : null
                )
            ),
            el('div', blockProps,
                el('div', { className: 'bma-faq-items__inner' },
                    (hasHead || layout === 'split' || attributes.tone) ? head : el('h2', {
                        className: 'wp-block-heading has-text-align-center',
                    }, __('Frequently Asked Questions', 'balefire')),
                    el('div', { className: 'bma-faq-items__body' },
                        isQuery ? queryPlaceholder : el('div', innerBlocksProps)
                    )
                )
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
