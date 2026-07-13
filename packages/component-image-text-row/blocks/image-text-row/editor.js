(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/image-text-row",
    "title": "Image Text Row",
    "category": "balefire",
    "icon": "align-pull-left",
    "description": "A single image and text row used inside Image Text Rows.",
    "keywords": [
        "image",
        "text",
        "row",
        "media",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-image-text-row-editor",
    "render": "file:./render.php",
    "parent": [
        "balefire/image-text-rows"
    ],
    "supports": {
        "reusable": false,
        "className": true,
        "html": false
    },
    "attributes": {
        "heading": {
            "type": "string",
            "default": ""
        },
        "body": {
            "type": "string",
            "default": ""
        },
        "mediaId": {
            "type": "number",
            "default": 0
        },
        "mediaUrl": {
            "type": "string",
            "default": ""
        },
        "mediaAlt": {
            "type": "string",
            "default": ""
        },
        "layout": {
            "type": "string",
            "default": "inherit"
        },
        "preheader": {
            "type": "string",
            "default": ""
        },
        "subhead": {
            "type": "string",
            "default": ""
        },
        "showArrow": {
            "type": "boolean",
            "default": false
        },
        "imageCrop": {
            "type": "string",
            "default": "default"
        },
        "imageAspectRatio": {
            "type": "string",
            "default": "default"
        },
        "imageRounded": {
            "type": "boolean",
            "default": false
        },
        "imagePosition": {
            "type": "string",
            "default": "object-center"
        },
        "columnGap": {
            "type": "string",
            "default": "gap-4"
        },
        "columnGapCustom": {
            "type": "string",
            "default": ""
        },
        "imageMode": {
            "type": "string",
            "default": "single"
        },
        "images": {
            "type": "array",
            "default": []
        },
        "imageStackGap": {
            "type": "string",
            "default": "gap-4"
        },
        "imageStackGapCustom": {
            "type": "string",
            "default": ""
        }
    },
    "version": "1.0.0",
    "style": "balefire-image-text-row"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const {
    BlockControls,
    InnerBlocks,
    InspectorControls,
    MediaPlaceholder,
    MediaReplaceFlow,
    MediaUpload,
    MediaUploadCheck,
    useBlockProps,
    useInnerBlocksProps,
} = wp.blockEditor;
const { Button, PanelBody, RadioControl, SelectControl, TextControl, ToggleControl, ToolbarButton, ToolbarGroup } = wp.components;
const { createElement: el, Fragment } = wp.element;

const TEXT_TEMPLATE = [
    ['core/heading', { level: 2, placeholder: __('Add heading', 'balefire') }],
    ['core/paragraph', { placeholder: __('Add supporting copy', 'balefire') }],
];

const COLUMN_GAP_OPTIONS = [
    { label: __('None (0)', 'balefire'), value: 'gap-0' },
    { label: __('XS (0.5rem)', 'balefire'), value: 'gap-2' },
    { label: __('Small (1rem)', 'balefire'), value: 'gap-4' },
    { label: __('Medium (1.5rem)', 'balefire'), value: 'gap-6' },
    { label: __('Large (2rem)', 'balefire'), value: 'gap-8' },
    { label: __('XL (2.5rem)', 'balefire'), value: 'gap-10' },
    { label: __('2XL (3rem)', 'balefire'), value: 'gap-12' },
    { label: __('3XL (4rem)', 'balefire'), value: 'gap-16' },
    { label: __('Custom', 'balefire'), value: 'custom' },
];

const CROP_OPTIONS = [
    { label: __('Default (no crop)', 'balefire'), value: 'default' },
    { label: __('Cover (fill, crop overflow)', 'balefire'), value: 'object-cover' },
    { label: __('Contain (fit inside)', 'balefire'), value: 'object-contain' },
    { label: __('Fill (stretch)', 'balefire'), value: 'object-fill' },
    { label: __('None (original size)', 'balefire'), value: 'object-none' },
];

const POSITION_OPTIONS = [
    { label: __('Top Left', 'balefire'), value: 'object-top-left' },
    { label: __('Top', 'balefire'), value: 'object-top' },
    { label: __('Top Right', 'balefire'), value: 'object-top-right' },
    { label: __('Left', 'balefire'), value: 'object-left' },
    { label: __('Center', 'balefire'), value: 'object-center' },
    { label: __('Right', 'balefire'), value: 'object-right' },
    { label: __('Bottom Left', 'balefire'), value: 'object-bottom-left' },
    { label: __('Bottom', 'balefire'), value: 'object-bottom' },
    { label: __('Bottom Right', 'balefire'), value: 'object-bottom-right' },
];

const ASPECT_OPTIONS = [
    { label: __('Default (4/3)', 'balefire'), value: 'default' },
    { label: __('Auto', 'balefire'), value: 'aspect-auto' },
    { label: __('Square (1/1)', 'balefire'), value: 'aspect-square' },
    { label: __('Video (16/9)', 'balefire'), value: 'aspect-video' },
    { label: __('Portrait (3/4)', 'balefire'), value: 'aspect-3/4' },
    { label: __('Standard (4/3)', 'balefire'), value: 'aspect-4/3' },
    { label: __('Widescreen (16/9)', 'balefire'), value: 'aspect-16/9' },
    { label: __('Ultrawide (21/9)', 'balefire'), value: 'aspect-21/9' },
];

const EMPTY_IMAGE = {
    id: 0,
    url: '',
    alt: '',
    crop: 'default',
    aspectRatio: 'default',
    position: 'object-center',
    rounded: false,
};

const resolveGapClass = (attributes) => {
    const { columnGap, columnGapCustom } = attributes;
    if (columnGap === 'custom') {
        return (columnGapCustom || '').trim() || 'gap-4';
    }
    return columnGap || 'gap-4';
};

const resolveImageStackGap = (attributes) => {
    const { imageStackGap, imageStackGapCustom } = attributes;
    if (imageStackGap === 'custom') {
        return (imageStackGapCustom || '').trim() || 'gap-4';
    }
    return imageStackGap || 'gap-4';
};

// Per-image repeater card
function ImageEditor({ image, index, onUpdate, onRemove }) {
    const hasImg = !!image.url;

    return el('div', {
        style: {
            padding: '12px',
            marginBottom: '12px',
            border: '1px solid #e0e0e0',
            borderRadius: '4px',
            background: '#fafafa',
        },
    },
        // Header row
        el('div', {
            style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '8px' },
        },
            el('strong', null, __('Image ', 'balefire'), index + 1),
            el(Button, {
                isDestructive: true,
                isSmall: true,
                variant: 'link',
                onClick: onRemove,
            }, __('Remove', 'balefire'))
        ),
        // Image upload / replace
        hasImg
            ? el('div', { style: { marginBottom: '8px' } },
                el('img', {
                    src: image.url,
                    alt: image.alt || '',
                    style: { maxWidth: '100%', height: 'auto', borderRadius: '4px', marginBottom: '4px' },
                }),
                el(MediaUpload, {
                    value: image.id,
                    onSelect: (media) => onUpdate({ ...image, id: media.id || 0, url: media.url || '', alt: media.alt || '' }),
                    allowedTypes: ['image'],
                    render: ({ open }) => el(Button, { onClick: open, variant: 'secondary', isSmall: true },
                        __('Replace', 'balefire')
                    ),
                })
            )
            : el(MediaUpload, {
                onSelect: (media) => onUpdate({ ...image, id: media.id || 0, url: media.url || '', alt: media.alt || '' }),
                allowedTypes: ['image'],
                render: ({ open }) => el(Button, { onClick: open, variant: 'secondary' },
                    __('Select Image', 'balefire')
                ),
            }),
        // Per-image controls
        el(RadioControl, {
            label: __('Image Fit', 'balefire'),
            selected: image.crop || 'default',
            options: CROP_OPTIONS,
            onChange: (value) => onUpdate({ ...image, crop: value }),
        }),
        (image.crop && image.crop !== 'default') && el(SelectControl, {
            label: __('Crop Position', 'balefire'),
            value: image.position || 'object-center',
            options: POSITION_OPTIONS,
            onChange: (value) => onUpdate({ ...image, position: value }),
        }),
        el(SelectControl, {
            label: __('Aspect Ratio', 'balefire'),
            value: image.aspectRatio || 'default',
            options: ASPECT_OPTIONS,
            onChange: (value) => onUpdate({ ...image, aspectRatio: value }),
        }),
        el(ToggleControl, {
            label: __('Rounded corners', 'balefire'),
            checked: !!image.rounded,
            onChange: (value) => onUpdate({ ...image, rounded: value }),
        })
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const gapClass = resolveGapClass(attributes);
        const imageMode = attributes.imageMode || 'single';
        const images = Array.isArray(attributes.images) ? attributes.images : [];
        const stackGapClass = resolveImageStackGap(attributes);

        const isSingle = imageMode !== 'multi';
        const hasSingleImage = !!attributes.mediaUrl;
        const hasMultiImage = images.some((img) => !!img.url);
        const hasImage = isSingle ? hasSingleImage : hasMultiImage;

        const blockProps = useBlockProps({
            className: [
                'image-text-row',
                'bma-editor-preview',
                'bma-editor-preview-image-text-row',
                hasImage ? 'has-image' : 'missing-image',
                `layout-${attributes.layout || 'inherit'}`,
            ].join(' '),
        });

        const innerBlocksProps = useInnerBlocksProps(
            {
                className: 'row-text',
            },
            {
                template: TEXT_TEMPLATE,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        // Single-mode handlers
        const onSelectImage = (media) => {
            setAttributes({
                mediaId: media?.id || 0,
                mediaUrl: media?.url || '',
                mediaAlt: media?.alt || '',
            });
        };

        const removeImage = () => {
            setAttributes({
                mediaId: 0,
                mediaUrl: '',
                mediaAlt: '',
            });
        };

        // Multi-mode handlers
        const addImage = (media) => {
            const newItem = {
                ...EMPTY_IMAGE,
                id: media?.id || 0,
                url: media?.url || '',
                alt: media?.alt || '',
            };
            setAttributes({ images: [...images, newItem] });
        };

        const updateImage = (index, updated) => {
            setAttributes({ images: images.map((img, i) => (i === index ? updated : img)) });
        };

        const removeImageAt = (index) => {
            setAttributes({ images: images.filter((_, i) => i !== index) });
        };

        // ---- InspectorControls ----
        const inspectorControls = el(InspectorControls, null,
            // Row Settings panel (unchanged)
            el(PanelBody, { title: __('Image Text Row Settings', 'balefire') },
                el(SelectControl, {
                    label: __('Layout', 'balefire'),
                    value: attributes.layout || 'inherit',
                    options: [
                        { label: __('Inherit from parent', 'balefire'), value: 'inherit' },
                        { label: __('Text left, image right', 'balefire'), value: 'text-image' },
                        { label: __('Image left, text right', 'balefire'), value: 'image-text' },
                    ],
                    onChange: (value) => setAttributes({ layout: value }),
                }),
                el(SelectControl, {
                    label: __('Column Gap (desktop)', 'balefire'),
                    help: __('Horizontal space between text and image at lg: and up.', 'balefire'),
                    value: attributes.columnGap || 'gap-4',
                    options: COLUMN_GAP_OPTIONS,
                    onChange: (value) => setAttributes({ columnGap: value }),
                }),
                attributes.columnGap === 'custom' && el(TextControl, {
                    label: __('Custom Gap Class', 'balefire'),
                    help: __('Tailwind gap utility, e.g. gap-20 lg:gap-32', 'balefire'),
                    value: attributes.columnGapCustom || '',
                    onChange: (value) => setAttributes({ columnGapCustom: value }),
                })
            ),
            // Image Settings panel — mode toggle + controls
            el(PanelBody, { title: __('Image Settings', 'balefire') },
                el(RadioControl, {
                    label: __('Image Mode', 'balefire'),
                    selected: imageMode,
                    options: [
                        { label: __('Single Image', 'balefire'), value: 'single' },
                        { label: __('Multiple Images', 'balefire'), value: 'multi' },
                    ],
                    onChange: (value) => setAttributes({ imageMode: value }),
                }),

                // Single mode controls (unchanged)
                isSingle && el(RadioControl, {
                    label: __('Image Fit', 'balefire'),
                    selected: attributes.imageCrop || 'default',
                    options: CROP_OPTIONS,
                    onChange: (value) => setAttributes({ imageCrop: value }),
                }),
                isSingle && (attributes.imageCrop && attributes.imageCrop !== 'default') && el(SelectControl, {
                    label: __('Crop Position', 'balefire'),
                    value: attributes.imagePosition || 'object-center',
                    options: POSITION_OPTIONS,
                    onChange: (value) => setAttributes({ imagePosition: value }),
                }),
                isSingle && el(SelectControl, {
                    label: __('Aspect Ratio', 'balefire'),
                    value: attributes.imageAspectRatio || 'default',
                    options: ASPECT_OPTIONS,
                    onChange: (value) => setAttributes({ imageAspectRatio: value }),
                }),
                isSingle && el(ToggleControl, {
                    label: __('Rounded corners', 'balefire'),
                    checked: !!attributes.imageRounded,
                    onChange: (value) => setAttributes({ imageRounded: value }),
                }),

                // Multi mode controls — repeater
                !isSingle && el(Fragment, null,
                    // Image stack gap
                    el(SelectControl, {
                        label: __('Image Stack Gap', 'balefire'),
                        help: __('Vertical space between stacked images.', 'balefire'),
                        value: attributes.imageStackGap || 'gap-4',
                        options: COLUMN_GAP_OPTIONS,
                        onChange: (value) => setAttributes({ imageStackGap: value }),
                    }),
                    attributes.imageStackGap === 'custom' && el(TextControl, {
                        label: __('Custom Stack Gap Class', 'balefire'),
                        help: __('Tailwind gap utility, e.g. gap-6 lg:gap-8', 'balefire'),
                        value: attributes.imageStackGapCustom || '',
                        onChange: (value) => setAttributes({ imageStackGapCustom: value }),
                    }),

                    // Repeater items
                    images.length === 0 && el('p', {
                        style: { color: '#757575', fontStyle: 'italic', margin: '8px 0' },
                    }, __('No images added yet.', 'balefire')),
                    ...images.map((img, i) =>
                        el(ImageEditor, {
                            key: i,
                            image: img,
                            index: i,
                            onUpdate: (updated) => updateImage(i, updated),
                            onRemove: () => removeImageAt(i),
                        })
                    ),
                    // Add button
                    el(MediaUpload, {
                        onSelect: addImage,
                        allowedTypes: ['image'],
                        multiple: false,
                        render: ({ open }) => el(Button, { onClick: open, variant: 'primary', isSmall: true },
                            __('Add Image', 'balefire')
                        ),
                    })
                )
            )
        );

        // ---- BlockControls (single mode toolbar) ----
        const blockControls = isSingle && hasSingleImage && el(BlockControls, null,
            el(MediaUploadCheck, null,
                el(ToolbarGroup, null,
                    el(MediaReplaceFlow, {
                        mediaId: attributes.mediaId,
                        mediaURL: attributes.mediaUrl,
                        allowedTypes: ['image'],
                        accept: 'image/*',
                        onSelect: onSelectImage,
                        name: __('Replace image', 'balefire'),
                    }),
                    el(ToolbarButton, {
                        icon: 'trash',
                        label: __('Remove image', 'balefire'),
                        onClick: removeImage,
                    })
                )
            )
        );

        // ---- Canvas preview ----
        function buildImgClasses(crop, position, aspectRatio) {
            return [
                'row-image', 'h-full', 'w-full',
                crop && crop !== 'default' ? crop : '',
                crop && crop !== 'default' ? (position || 'object-center') : '',
                aspectRatio && aspectRatio !== 'default' ? aspectRatio : '',
            ].filter(Boolean).join(' ');
        }

        const mediaArea = isSingle
            ? (hasSingleImage
                ? el('figure', {
                    className: ['overflow-hidden', attributes.imageRounded ? 'rounded-[var(--radius-card)]' : ''].filter(Boolean).join(' '),
                },
                    el('img', {
                        src: attributes.mediaUrl,
                        alt: attributes.mediaAlt || '',
                        className: buildImgClasses(attributes.imageCrop, attributes.imagePosition, attributes.imageAspectRatio),
                    })
                )
                : el(MediaPlaceholder, {
                    icon: 'format-image',
                    labels: {
                        title: __('Image', 'balefire'),
                        instructions: __('Select an image for this row.', 'balefire'),
                    },
                    onSelect: onSelectImage,
                    allowedTypes: ['image'],
                    accept: 'image/*',
                })
            )
            : (hasMultiImage
                ? el('div', { className: ['row-media', 'flex', 'flex-col', stackGapClass].join(' ') },
                    ...images.filter((img) => !!img.url).map((img, i) =>
                        el('figure', {
                            key: i,
                            className: ['row-media', 'overflow-hidden', img.rounded ? 'rounded-[var(--radius-card)]' : ''].filter(Boolean).join(' '),
                        },
                            el('img', {
                                src: img.url,
                                alt: img.alt || '',
                                className: buildImgClasses(img.crop, img.position, img.aspectRatio),
                            })
                        )
                    )
                )
                : el(MediaPlaceholder, {
                    icon: 'format-image',
                    labels: {
                        title: __('Images', 'balefire'),
                        instructions: __('Switch to Multi mode and add images via the sidebar.', 'balefire'),
                    },
                    onSelect: () => {},
                    allowedTypes: ['image'],
                    accept: 'image/*',
                })
            );

        return el(Fragment, null,
            inspectorControls,
            blockControls,
            el('div', blockProps,
                el('div', { className: ['row-grid', 'grid', 'grid-cols-1', 'items-center', gapClass, hasImage ? 'lg:grid-cols-2' : ''].filter(Boolean).join(' ') },
                    el('div', innerBlocksProps),
                    el('div', { className: 'row-media' },
                        mediaArea
                    )
                )
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
