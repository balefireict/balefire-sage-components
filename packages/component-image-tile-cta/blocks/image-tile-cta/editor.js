// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/image-tile-cta",
    "title": "Image Tile CTA",
    "category": "balefire",
    "icon": "format-gallery",
    "description": "A two-column section with text content on the left and a four-image tile gallery on the right.",
    "keywords": [
        "image",
        "gallery",
        "cta",
        "tile",
        "feature",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-image-tile-cta-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "heroImageId": {
            "type": "number",
            "default": 0
        },
        "heroImageUrl": {
            "type": "string",
            "default": ""
        },
        "heroImageAlt": {
            "type": "string",
            "default": ""
        },
        "image1Id": {
            "type": "number",
            "default": 0
        },
        "image1Url": {
            "type": "string",
            "default": ""
        },
        "image1Alt": {
            "type": "string",
            "default": ""
        },
        "image2Id": {
            "type": "number",
            "default": 0
        },
        "image2Url": {
            "type": "string",
            "default": ""
        },
        "image2Alt": {
            "type": "string",
            "default": ""
        },
        "image3Id": {
            "type": "number",
            "default": 0
        },
        "image3Url": {
            "type": "string",
            "default": ""
        },
        "image3Alt": {
            "type": "string",
            "default": ""
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const {
    InspectorControls,
    InnerBlocks,
    useBlockProps,
    useInnerBlocksProps,
    MediaUpload,
} = wp.blockEditor;
const { PanelBody } = wp.components;
const { createElement: el, Fragment } = wp.element;

const TEMPLATE = [
    [
        'core/heading',
        {
            level: 2,
            placeholder: __('Section heading', 'balefire'),
        },
    ],
    [
        'core/paragraph',
        {
            placeholder: __('Lead paragraph…', 'balefire'),
        },
    ],
    [
        'core/paragraph',
        {
            placeholder: __('Supporting paragraph…', 'balefire'),
        },
    ],
];

const IMAGE_SLOTS = [
    { key: 'hero',   label: __('Hero (large left/lead)', 'balefire') },
    { key: 'image1', label: __('Image 1 (small)',        'balefire') },
    { key: 'image2', label: __('Image 2 (large)',        'balefire') },
    { key: 'image3', label: __('Image 3 (small)',        'balefire') },
];

function attrKeys(slot) {
    if (slot === 'hero') {
        return { id: 'heroImageId', url: 'heroImageUrl', alt: 'heroImageAlt' };
    }
    // slot is e.g. 'image1' → keys are 'image1Id', 'image1Url', 'image1Alt' (camelCase, matches block.json)
    return { id: slot + 'Id', url: slot + 'Url', alt: slot + 'Alt' };
}

function ImageSlotControl({ slot, label, attributes, setAttributes }) {
    const keys = attrKeys(slot);
    const id   = attributes[keys.id]  || 0;
    const url  = attributes[keys.url] || '';
    const alt  = attributes[keys.alt] || '';

    return el(
        'div',
        { className: 'components-base-control', style: { marginBottom: '16px' } },
        el(
            'label',
            { className: 'components-base-control__label', style: { display: 'block', marginBottom: '4px' } },
            label
        ),
        el(MediaUpload, {
            onSelect: (media) =>
                setAttributes({
                    [keys.id]: media.id,
                    [keys.url]: media.url,
                    [keys.alt]: media.alt || '',
                }),
            allowedTypes: ['image'],
            value: id,
            render: ({ open }) =>
                el(
                    'div',
                    { style: { display: 'flex', alignItems: 'center', gap: '8px', flexWrap: 'wrap' } },
                    url
                        ? el('img', {
                              src: url,
                              alt: alt,
                              style: {
                                  maxWidth: '120px',
                                  maxHeight: '80px',
                                  objectFit: 'cover',
                                  borderRadius: '4px',
                              },
                          })
                        : null,
                    el(
                        'button',
                        {
                            type: 'button',
                            onClick: open,
                            className: 'components-button is-secondary is-small',
                        },
                        url ? __('Replace', 'balefire') : __('Select Image', 'balefire')
                    ),
                    url
                        ? el(
                              'button',
                              {
                                  type: 'button',
                                  onClick: () =>
                                      setAttributes({
                                          [keys.id]: 0,
                                          [keys.url]: '',
                                          [keys.alt]: '',
                                      }),
                                  className: 'components-button is-link is-destructive is-small',
                              },
                              __('Remove', 'balefire')
                          )
                        : null
                ),
        })
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-image-tile-cta bma-editor-preview bma-editor-preview-image-tile-cta',
        });

        const innerBlocksProps = useInnerBlocksProps(
            { className: 'bma-image-tile-cta__content' },
            {
                template: TEMPLATE,
                templateLock: false,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        const renderImagePreview = (slot) => {
            const keys = attrKeys(slot);
            const url = attributes[keys.url];
            if (!url) {
                return el(
                    'div',
                    {
                        style: {
                            background: '#f0f0f0',
                            border: '1px dashed #ccc',
                            borderRadius: '8px',
                            aspectRatio: slot === 'image1' || slot === 'image3' ? '4/3' : '7/5',
                            width: '100%',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: '#888',
                            fontSize: '12px',
                        },
                    },
                    __('No image', 'balefire')
                );
            }
            return el('img', {
                src: url,
                alt: attributes[keys.alt],
                style: {
                    width: '100%',
                    height: 'auto',
                    aspectRatio: slot === 'image1' || slot === 'image3' ? '4/3' : '7/5',
                    objectFit: 'cover',
                    borderRadius: '8px',
                    display: 'block',
                },
            });
        };

        return el(
            Fragment,
            null,
            el(
                InspectorControls,
                null,
                el(
                    PanelBody,
                    { title: __('Images', 'balefire'), initialOpen: true },
                    ...IMAGE_SLOTS.map((slot) =>
                        el(ImageSlotControl, {
                            key: slot.key,
                            slot: slot.key,
                            label: slot.label,
                            attributes,
                            setAttributes,
                        })
                    )
                )
            ),
            el(
                'div',
                blockProps,
                el(
                    'div',
                    { className: 'bma-image-tile-cta__container mx-auto max-w-screen-2xl px-6 lg:px-8' },
                    // Top row: text (40%) + hero (60%), aligned to bottom
                    el(
                        'div',
                        {
                            className: 'bma-image-tile-cta__top',
                            style: {
                                display: 'grid',
                                gridTemplateColumns: 'minmax(0, 1fr) minmax(0, 1fr)',
                                gap: '3rem',
                                alignItems: 'end',
                            },
                        },
                        el(
                            'div',
                            { className: 'bma-image-tile-cta__content' },
                            el('div', innerBlocksProps)
                        ),
                        el(
                            'div',
                            { className: 'bma-image-tile-cta__hero' },
                            renderImagePreview('hero')
                        )
                    ),
                    // Bottom row: 3 supporting tiles, bottom-aligned
                    el(
                        'div',
                        {
                            className: 'bma-image-tile-cta__gallery',
                            style: {
                                display: 'grid',
                                gridTemplateColumns: '2fr 3fr 2fr',
                                gap: '2rem',
                                marginTop: '2rem',
                                alignItems: 'end',
                            },
                        },
                        el('div', { className: 'bma-image-tile-cta__tile bma-image-tile-cta__tile--1' }, renderImagePreview('image1')),
                        el('div', { className: 'bma-image-tile-cta__tile bma-image-tile-cta__tile--2' }, renderImagePreview('image2')),
                        el('div', { className: 'bma-image-tile-cta__tile bma-image-tile-cta__tile--3', style: { alignSelf: 'start' } }, renderImagePreview('image3'))
                    )
                )
            )
        );
    },
    save: () => el(InnerBlocks.Content),
    deprecated: [
        {
            attributes: metadata.attributes,
            supports: metadata.supports,
            // Old save wrapped inner blocks in <div class="bma-image-tile-cta__content"> via useBlockProps.save.
            save: () => {
                const blockProps = useBlockProps.save({
                    className: 'bma-image-tile-cta__content',
                });
                return el('div', blockProps, el(InnerBlocks.Content, null));
            },
            migrate: (attributes, innerBlocks) => [attributes, innerBlocks || []],
        },
    ],
});
