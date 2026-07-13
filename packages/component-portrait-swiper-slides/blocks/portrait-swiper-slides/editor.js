(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/portrait-swiper-slides",
    "title": "Portrait Swiper Slides",
    "category": "balefire",
    "icon": "slides",
    "description": "A Swiper.js slider with portrait-oriented slides featuring images, titles, links, and overlays.",
    "keywords": [
        "swiper",
        "slider",
        "carousel",
        "slides",
        "portrait",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-portrait-swiper-slides-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "none",
            "wide",
            "full"
        ],
        "spacing": {
            "margin": true,
            "padding": true
        }
    },
    "attributes": {
        "slides": {
            "type": "array",
            "default": []
        },
        "slidesPerView": {
            "type": "number",
            "default": 4
        },
        "spaceBetween": {
            "type": "number",
            "default": 16
        },
        "showPagination": {
            "type": "boolean",
            "default": true
        },
        "showNavigation": {
            "type": "boolean",
            "default": true
        },
        "overlayColor": {
            "type": "string",
            "default": ""
        },
        "align": {
            "type": "string",
            "default": "wide"
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const {
    InspectorControls,
    useBlockProps,
    MediaUpload,
} = wp.blockEditor;
const {
    PanelBody,
    SelectControl,
    TextControl,
    ToggleControl,
    Button,
    ColorIndicator,
    Popover,
    Dropdown,
} = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

const EMPTY_SLIDE = { imageUrl: '', imageAlt: '', title: '', url: '' };

const SLIDES_PER_VIEW_OPTIONS = [
    { label: '2', value: 2 },
    { label: '3', value: 3 },
    { label: '4', value: 4 },
    { label: '5', value: 5 },
];

const SPACE_BETWEEN_OPTIONS = [
    { label: 'gap-0 (0px)', value: 0 },
    { label: 'gap-2 (8px)', value: 8 },
    { label: 'gap-4 (16px)', value: 16 },
    { label: 'gap-6 (24px)', value: 24 },
    { label: 'gap-8 (32px)', value: 32 },
];

function SlideEditor({ slide, index, onUpdate, onRemove }) {
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
            el('strong', null, __('Slide ', 'balefire'), index + 1),
            el(Button, {
                isDestructive: true,
                isSmall: true,
                variant: 'link',
                onClick: onRemove,
            }, __('Remove', 'balefire'))
        ),
        el(MediaUpload, {
            onSelect: (media) => onUpdate({
                ...slide,
                imageUrl: media.url,
                imageAlt: media.alt || '',
                imageId: media.id,
            }),
            allowedTypes: ['image'],
            value: slide.imageId || 0,
            render: ({ open }) => el('div', null,
                slide.imageUrl
                    ? el('div', { style: { position: 'relative' } },
                        el('img', {
                            src: slide.imageUrl,
                            alt: slide.imageAlt || '',
                            style: {
                                width: '100%',
                                maxHeight: '120px',
                                objectFit: 'cover',
                                borderRadius: '4px',
                            },
                        }),
                        el('div', {
                            style: {
                                position: 'absolute',
                                top: '4px',
                                right: '4px',
                            },
                        },
                            el(Button, {
                                isSmall: true,
                                variant: 'primary',
                                onClick: open,
                            }, __('Replace', 'balefire'))
                        )
                    )
                    : el(Button, {
                        isPrimary: true,
                        isSmall: true,
                        onClick: open,
                        variant: 'secondary',
                    }, __('Select Image', 'balefire'))
            ),
        }),
        el(TextControl, {
            label: __('Title', 'balefire'),
            value: slide.title || '',
            onChange: (value) => onUpdate({ ...slide, title: value }),
            placeholder: __('Enter slide title', 'balefire'),
        }),
        el(TextControl, {
            label: __('Link URL', 'balefire'),
            type: 'url',
            value: slide.url || '',
            onChange: (value) => onUpdate({ ...slide, url: value }),
            placeholder: 'https://example.com',
        })
    );
}

function OverlayColorPicker({ value, onChange }) {
    return el('div', {
        style: {
            display: 'flex',
            alignItems: 'center',
            gap: '8px',
            marginTop: '8px',
        },
    },
        el(ColorIndicator, { colorValue: value }),
        el('span', { style: { fontSize: '13px' } }, __('Overlay', 'balefire')),
        el(Dropdown, {
            renderToggle: ({ isOpen, onToggle }) => el(Button, {
                variant: 'secondary',
                isSmall: true,
                onClick: onToggle,
                'aria-expanded': isOpen,
            }, __('Custom', 'balefire')),
            renderContent: () => el(Popover, { placement: 'bottom-start' },
                el('div', {
                    style: { padding: '16px' },
                    className: 'bma-overlay-color-popover',
                },
                    el('p', { style: { marginBottom: '8px', fontSize: '12px', color: '#757575' } },
                        __('Enter an rgba or hex color', 'balefire')
                    ),
                    el(TextControl, {
                        value,
                        onChange,
                        placeholder: 'rgba(0, 0, 0, 0.4)',
                    })
                )
            ),
        }),
        el('span', { style: { fontSize: '11px', color: '#999' } }, value)
    );
}

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const slides = Array.isArray(attributes.slides)
            ? attributes.slides : [];

        const addSlide = () => {
            setAttributes({ slides: [...slides, { ...EMPTY_SLIDE }] });
        };

        const updateSlide = (index, updated) => {
            const next = slides.map((s, i) => (i === index ? updated : s));
            setAttributes({ slides: next });
        };

        const removeSlide = (index) => {
            setAttributes({ slides: slides.filter((_, i) => i !== index) });
        };

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-portrait-swiper-slides',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, {
                    title: __('Slider Settings', 'balefire'),
                    initialOpen: true,
                },
                    el(SelectControl, {
                        label: __('Slides Per View (desktop)', 'balefire'),
                        value: attributes.slidesPerView ?? 3,
                        options: SLIDES_PER_VIEW_OPTIONS.map((opt) => ({
                            label: opt.label,
                            value: opt.value,
                        })),
                        onChange: (value) => setAttributes({
                            slidesPerView: Number(value),
                        }),
                    }),
                    el(SelectControl, {
                        label: __('Gap', 'balefire'),
                        value: attributes.spaceBetween ?? 16,
                        options: SPACE_BETWEEN_OPTIONS.map((opt) => ({
                            label: opt.label,
                            value: opt.value,
                        })),
                        onChange: (value) => setAttributes({
                            spaceBetween: Number(value),
                        }),
                    }),
                    el(ToggleControl, {
                        label: __('Show Pagination', 'balefire'),
                        checked: !!attributes.showPagination,
                        onChange: (value) => setAttributes({
                            showPagination: value,
                        }),
                    }),
                    el(ToggleControl, {
                        label: __('Show Navigation Arrows', 'balefire'),
                        checked: !!attributes.showNavigation,
                        onChange: (value) => setAttributes({
                            showNavigation: value,
                        }),
                    }),
                    el(OverlayColorPicker, {
                        value: attributes.overlayColor || 'rgba(0, 0, 0, 0.4)',
                        onChange: (value) => setAttributes({
                            overlayColor: value,
                        }),
                    })
                ),
                el(PanelBody, {
                    title: __('Slides', 'balefire'),
                    initialOpen: true,
                },
                    slides.length > 0
                        ? slides.map((slide, i) => el(SlideEditor, {
                            key: i,
                            slide,
                            index: i,
                            onUpdate: (updated) => updateSlide(i, updated),
                            onRemove: () => removeSlide(i),
                        }))
                        : el('p', {
                            style: {
                                color: '#757575',
                                fontSize: '13px',
                                fontStyle: 'italic',
                            },
                        }, __('No slides yet. Add one below.', 'balefire')),
                    el(Button, {
                        isPrimary: true,
                        variant: 'secondary',
                        onClick: addSlide,
                        style: { marginTop: '8px' },
                    }, __('+ Add Slide', 'balefire'))
                )
            ),
            el('div', blockProps,
                slides.length > 0
                    ? el(ServerSideRender, {
                        block: metadata.name,
                        attributes,
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
                            __('Add slides in the sidebar to see the preview.', 'balefire')
                        )
                    )
            )
        );
    },
    save: () => null,
});

})();
