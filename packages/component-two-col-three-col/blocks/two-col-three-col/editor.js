// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/two-col-three-col",
    "title": "2-col-3-col Cards",
    "category": "balefire",
    "icon": "grid-view",
    "description": "A card grid with 2 cards on the first row and 3 on the second. Flush-top images, padded text areas.",
    "keywords": [
        "cards",
        "grid",
        "2-col",
        "3-col",
        "industry",
        "bundle",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-two-col-three-col-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "cards": {
            "type": "array",
            "default": []
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, RadioControl, SelectControl, Button, Spinner } = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;
const ServerSideRender = wp.serverSideRender;

const EMPTY_CARD = {
    imageId: 0,
    imageUrl: '',
    imageAlt: '',
    title: '',
    prehead: '',
    text: '',
    linkType: 'none',
    pageId: 0,
    url: '',
    openInNewTab: false,
};

function CardEditor({ card, index, onUpdate, onRemove, onMoveUp, onMoveDown, isFirst, isLast, pages }) {
    const pageOptions = [
        { label: __('— Select a page —', 'balefire'), value: 0 },
        ...pages.map(function (page) {
            return { label: page.title.rendered, value: page.id };
        }),
    ];

    return el('div', {
        style: {
            padding: '12px',
            marginBottom: '12px',
            border: '1px solid #e0e0e0',
            borderRadius: '4px',
            background: '#fafafa',
        },
    },
        el('div', {
            style: {
                display: 'flex',
                justifyContent: 'space-between',
                alignItems: 'center',
                marginBottom: '8px',
            },
        },
            el('strong', null, __('Card ', 'balefire'), index + 1),
            el('div', { style: { display: 'flex', gap: '4px' } },
                el(Button, {
                    isSmall: true,
                    variant: 'secondary',
                    disabled: isFirst,
                    onClick: onMoveUp,
                }, '\u2191'),
                el(Button, {
                    isSmall: true,
                    variant: 'secondary',
                    disabled: isLast,
                    onClick: onMoveDown,
                }, '\u2193'),
                el(Button, {
                    isDestructive: true,
                    isSmall: true,
                    variant: 'link',
                    onClick: onRemove,
                }, __('Remove', 'balefire'))
            )
        ),
        el(MediaUpload, {
            onSelect: function (media) {
                onUpdate({
                    ...card,
                    imageUrl: media.url,
                    imageAlt: media.alt || '',
                    imageId: media.id,
                });
            },
            allowedTypes: ['image'],
            value: card.imageId || 0,
            render: function (_ref) {
                var open = _ref.open;
                return el('div', null,
                    card.imageUrl
                        ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' } },
                            el('img', {
                                src: card.imageUrl,
                                alt: card.imageAlt || '',
                                style: { maxWidth: '80px', maxHeight: '60px', objectFit: 'cover', borderRadius: '4px' },
                            }),
                            el(Button, {
                                isSmall: true,
                                variant: 'primary',
                                onClick: open,
                            }, __('Replace', 'balefire'))
                        )
                        : el(Button, {
                            isSmall: true,
                            variant: 'secondary',
                            onClick: open,
                            style: { marginBottom: '8px' },
                        }, __('Select Image', 'balefire'))
                );
            },
        }),
        el(TextControl, {
            label: __('Title', 'balefire'),
            value: card.title || '',
            onChange: function (value) { onUpdate({ ...card, title: value }); },
        }),
        el(TextControl, {
            label: __('Prehead', 'balefire'),
            value: card.prehead || card.subtitle || '',
            onChange: function (value) { onUpdate({ ...card, prehead: value }); },
        }),
        el(TextareaControl, {
            label: __('Description', 'balefire'),
            value: card.text || '',
            onChange: function (value) { onUpdate({ ...card, text: value }); },
        }),
        el(RadioControl, {
            label: __('Link', 'balefire'),
            selected: card.linkType || 'none',
            options: [
                { label: __('None', 'balefire'), value: 'none' },
                { label: __('WordPress Page', 'balefire'), value: 'page' },
                { label: __('External URL', 'balefire'), value: 'external' },
            ],
            onChange: function (value) { onUpdate({ ...card, linkType: value }); },
        }),
        card.linkType === 'page'
            ? pages.length === 0
                ? el('div', { style: { display: 'flex', alignItems: 'center', gap: '8px', padding: '8px 0' } },
                    el(Spinner),
                    el('span', null, __('Loading pages\u2026', 'balefire'))
                  )
                : el(SelectControl, {
                    label: __('Select Page', 'balefire'),
                    value: card.pageId || 0,
                    options: pageOptions,
                    onChange: function (value) { onUpdate({ ...card, pageId: parseInt(value, 10) || 0 }); },
                  })
            : card.linkType === 'external'
                ? el(TextControl, {
                    label: __('External URL', 'balefire'),
                    type: 'url',
                    value: card.url || '',
                    onChange: function (value) { onUpdate({ ...card, url: value }); },
                    placeholder: 'https://example.com',
                  })
                : null
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: function (_ref) {
        var attributes = _ref.attributes;
        var setAttributes = _ref.setAttributes;

        var cards = Array.isArray(attributes.cards) ? attributes.cards : [];

        var pages = useSelect(function (select) {
            return select('core').getEntityRecords('postType', 'page', { per_page: 100, orderby: 'title', order: 'asc' }) || [];
        }, []);

        var addCard = function () {
            setAttributes({ cards: cards.concat([{ ...EMPTY_CARD }]) });
        };

        var updateCard = function (index, updated) {
            var next = cards.map(function (c, i) { return i === index ? updated : c; });
            setAttributes({ cards: next });
        };

        var removeCard = function (index) {
            setAttributes({ cards: cards.filter(function (_, i) { return i !== index; }) });
        };

        var moveCard = function (index, direction) {
            var targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= cards.length) return;
            var next = cards.slice();
            var temp = next[index];
            next[index] = next[targetIndex];
            next[targetIndex] = temp;
            setAttributes({ cards: next });
        };

        var blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-two-col-three-col',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, {
                    title: __('Cards', 'balefire'),
                    initialOpen: true,
                },
                    cards.length > 0
                        ? cards.map(function (card, i) {
                            return el(CardEditor, {
                                key: i,
                                card: card,
                                index: i,
                                onUpdate: function (updated) { updateCard(i, updated); },
                                onRemove: function () { removeCard(i); },
                                onMoveUp: function () { moveCard(i, -1); },
                                onMoveDown: function () { moveCard(i, 1); },
                                isFirst: i === 0,
                                isLast: i === cards.length - 1,
                                pages: pages,
                            });
                        })
                        : el('p', {
                            style: {
                                color: '#757575',
                                fontSize: '13px',
                                fontStyle: 'italic',
                            },
                        }, __('No cards yet. Add one below.', 'balefire')),
                    el(Button, {
                        variant: 'secondary',
                        onClick: addCard,
                        style: { marginTop: '8px' },
                    }, __('+ Add Card', 'balefire'))
                )
            ),
            el('div', blockProps,
                cards.length > 0
                    ? el(ServerSideRender, {
                        block: metadata.name,
                        attributes: attributes,
                        httpMethod: 'POST',
                    })
                    : el('div', {
                        style: {
                            padding: '40px 20px',
                            textAlign: 'center',
                            color: '#757575',
                            border: '2px dashed #ddd',
                            borderRadius: '4px',
                        },
                    },
                        el('p', { style: { fontSize: '14px' } },
                            __('Add cards in the sidebar to see the preview.', 'balefire')
                        )
                    )
            )
        );
    },
    save: function () {
        return null;
    },
});
