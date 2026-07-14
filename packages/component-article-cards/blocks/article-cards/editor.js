(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/article-cards",
    "title": "Article Cards",
    "category": "balefire",
    "icon": "grid-view",
    "description": "Dark guides section: eyebrow, heading, copy and a CTA above a 3- or 4-up grid of post cards.",
    "keywords": ["blog", "posts", "articles", "guides", "cards", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "Get It Right" },
        "title": { "type": "string", "default": "Mounting & Buying Guides" },
        "content": { "type": "string", "default": "" },
        "ctaLabel": { "type": "string", "default": "Explore Our Guides" },
        "ctaUrl": { "type": "string", "default": "" },
        "source": { "type": "string", "default": "filter" },
        "taxonomy": { "type": "string", "default": "category" },
        "termId": { "type": "number", "default": 0 },
        "postIds": { "type": "array", "default": [] },
        "count": { "type": "number", "default": 4 },
        "columns": { "type": "number", "default": 4 },
        "fallbackImageId": { "type": "number", "default": 0 },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-article-cards-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, SelectControl, RangeControl, CheckboxControl, Button, Notice } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Injected by src/bootstrap.php.
const TERMS = (window.balefireArticleTerms && typeof window.balefireArticleTerms === 'object')
    ? window.balefireArticleTerms
    : {};
const POSTS = Array.isArray(window.balefireArticlePosts) ? window.balefireArticlePosts : [];

const TAXONOMY_OPTIONS = [
    { label: __('Category', 'balefire'), value: 'category' },
    { label: __('Mission', 'balefire'), value: 'mission' },
];

const SOURCE_OPTIONS = [
    { label: __('Newest, filtered by a term', 'balefire'), value: 'filter' },
    { label: __('Hand-picked posts', 'balefire'), value: 'picked' },
];

const COLUMN_OPTIONS = [
    { label: __('3 columns', 'balefire'), value: 3 },
    { label: __('4 columns', 'balefire'), value: 4 },
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const picked = Array.isArray(attributes.postIds) ? attributes.postIds : [];
        const taxonomy = attributes.taxonomy || 'category';
        const termsForTax = TERMS[taxonomy] || [];

        const termOptions = [
            { label: __('— All posts —', 'balefire'), value: 0 },
            ...termsForTax.map((t) => ({ label: t.name, value: t.id })),
        ];

        const togglePost = (id, checked) => {
            setAttributes({
                postIds: checked ? [...picked, id] : picked.filter((p) => p !== id),
            });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-article-cards',
            style: { background: '#2e2e2e', color: '#fff', padding: '32px' },
        });

        const columns = Number(attributes.columns) === 3 ? 3 : 4;

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
                    }),
                    el(TextControl, {
                        label: __('CTA label', 'balefire'),
                        value: attributes.ctaLabel || '',
                        onChange: (value) => setAttributes({ ctaLabel: value }),
                    }),
                    el(TextControl, {
                        label: __('CTA URL', 'balefire'),
                        type: 'url',
                        help: __('A root-relative path like /blog works.', 'balefire'),
                        value: attributes.ctaUrl || '',
                        onChange: (value) => setAttributes({ ctaUrl: value }),
                    })
                ),

                el(PanelBody, { title: __('Posts', 'balefire'), initialOpen: true },
                    el(SelectControl, {
                        label: __('Which posts?', 'balefire'),
                        value: attributes.source || 'filter',
                        options: SOURCE_OPTIONS,
                        onChange: (value) => setAttributes({ source: value }),
                    }),

                    attributes.source !== 'picked' && el(Fragment, null,
                        el(SelectControl, {
                            label: __('Filter by', 'balefire'),
                            value: taxonomy,
                            options: TAXONOMY_OPTIONS,
                            onChange: (value) => setAttributes({ taxonomy: value, termId: 0 }),
                        }),
                        termsForTax.length === 0
                            ? el(Notice, { status: 'warning', isDismissible: false },
                                __('No terms found for that taxonomy.', 'balefire'))
                            : el(SelectControl, {
                                label: __('Term', 'balefire'),
                                value: Number(attributes.termId) || 0,
                                options: termOptions,
                                onChange: (value) => setAttributes({ termId: Number(value) }),
                            }),
                        el(RangeControl, {
                            label: __('How many', 'balefire'),
                            value: Number(attributes.count) || 4,
                            min: 1,
                            max: 12,
                            onChange: (value) => setAttributes({ count: value }),
                        })
                    ),

                    attributes.source === 'picked' && el(Fragment, null,
                        el('p', { style: { marginTop: 0 } },
                            __('Cards render in the order you tick them.', 'balefire')),
                        POSTS.length === 0
                            ? el(Notice, { status: 'warning', isDismissible: false },
                                __('No published posts found.', 'balefire'))
                            : POSTS.map((post) => el(CheckboxControl, {
                                key: post.id,
                                label: post.name,
                                checked: picked.includes(post.id),
                                onChange: (checked) => togglePost(post.id, checked),
                            }))
                    )
                ),

                el(PanelBody, { title: __('Layout', 'balefire'), initialOpen: false },
                    el(SelectControl, {
                        label: __('Columns', 'balefire'),
                        value: columns,
                        options: COLUMN_OPTIONS,
                        onChange: (value) => setAttributes({ columns: Number(value) }),
                    })
                ),

                el(PanelBody, { title: __('Fallback image', 'balefire'), initialOpen: false },
                    el('p', { style: { marginTop: 0 } },
                        __('Used when a post has no featured image, so the grid stays even.', 'balefire')),
                    el(MediaUpload, {
                        onSelect: (media) => setAttributes({ fallbackImageId: media.id || 0 }),
                        allowedTypes: ['image'],
                        value: attributes.fallbackImageId,
                        render: ({ open }) => el(Button, {
                            variant: 'secondary',
                            onClick: open,
                        }, attributes.fallbackImageId
                            ? __('Change fallback image', 'balefire')
                            : __('Select fallback image', 'balefire')),
                    }),
                    attributes.fallbackImageId ? el(Button, {
                        variant: 'link',
                        isDestructive: true,
                        style: { display: 'block', marginTop: '4px' },
                        onClick: () => setAttributes({ fallbackImageId: 0 }),
                    }, __('Remove', 'balefire')) : null
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                    attributes.eyebrow || ''),
                el('h2', { style: { margin: '0 0 8px', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.1, color: '#fff' } },
                    attributes.title || __('Article Cards', 'balefire')),
                attributes.content && el('p', { style: { color: '#747474', margin: '0 0 24px', maxWidth: '640px' } },
                    attributes.content),
                el('div', {
                    style: {
                        display: 'grid',
                        gridTemplateColumns: 'repeat(' + columns + ', minmax(0, 1fr))',
                        gap: '16px',
                    },
                },
                    Array.from({ length: columns }, (_, i) => el('div', {
                        key: i,
                        style: { background: '#fff', borderRadius: '8px', overflow: 'hidden' },
                    },
                        el('div', { style: { height: '80px', background: '#e8e8e8' } }),
                        el('div', { style: { padding: '12px' } },
                            el('span', { style: { color: '#d72b27', fontFamily: 'monospace', fontSize: '11px', fontWeight: 700 } },
                                __('CAT: …', 'balefire')),
                            el('p', { style: { color: '#2e2e2e', fontWeight: 700, textTransform: 'uppercase', margin: '4px 0 0' } },
                                __('Post title', 'balefire'))
                        )
                    ))
                ),
                el('p', { style: { color: '#747474', marginBottom: 0, fontSize: '12px' } },
                    attributes.source === 'picked'
                        ? __('Hand-picked posts render on the front end.', 'balefire')
                        : __('Newest posts render on the front end.', 'balefire'))
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
