(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/hero-video-header",
    "title": "Hero Video Header",
    "category": "balefire",
    "icon": "video-alt3",
    "description": "Full-width hero with looping background video, gradient overlay, title, subtitle, and button group.",
    "keywords": [
        "hero",
        "video",
        "header",
        "banner",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-hero-video-header-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": [
            "wide",
            "full"
        ],
        "spacing": {
            "margin": false,
            "padding": false
        }
    },
    "attributes": {
        "align": {
            "type": "string",
            "default": "full"
        },
        "videoUrl": {
            "type": "string",
            "default": ""
        },
        "fallbackImage": {
            "type": "string",
            "default": ""
        },
        "subtitle": {
            "type": "string",
            "default": ""
        },
        "primaryLabel": {
            "type": "string",
            "default": ""
        },
        "primaryUrl": {
            "type": "string",
            "default": ""
        },
        "secondaryLabel": {
            "type": "string",
            "default": ""
        },
        "secondaryUrl": {
            "type": "string",
            "default": ""
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const arrowPath = 'M10.652 13.735l3.964-3.967a.476.476 0 000-.672L10.652 5.13a.466.466 0 00-.332-.138.476.476 0 00-.34.809l3.155 3.155H4.817a.475.475 0 000 .951h8.318L9.979 13.063a.477.477 0 00.342.809.467.467 0 00.331-.136Z';

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-hero-video-header isolate overflow-hidden relative',
            style: { minHeight: '420px', background: '#1a1a1a' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Video', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Video URL', 'balefire'),
                        help: __('Direct link to an .mp4 video file.', 'balefire'),
                        value: attributes.videoUrl,
                        onChange: (value) => setAttributes({ videoUrl: value }),
                        type: 'url',
                        placeholder: 'https://example.com/video.mp4',
                    })
                ),
                el(PanelBody, { title: __('Fallback Image', 'balefire') },
                    el(MediaUpload, {
                        onSelect: (media) => setAttributes({ fallbackImage: media.url }),
                        allowedTypes: ['image'],
                        value: attributes.fallbackImage,
                        render: ({ open }) => el('button', {
                            className: 'components-button is-secondary',
                            onClick: open,
                        }, attributes.fallbackImage ? __('Change Fallback Image', 'balefire') : __('Select Fallback Image', 'balefire')),
                    }),
                    attributes.fallbackImage ? el('button', {
                        className: 'components-button is-link is-destructive',
                        style: { marginTop: '8px' },
                        onClick: () => setAttributes({ fallbackImage: '' }),
                    }, __('Remove Image', 'balefire')) : null
                ),
                el(PanelBody, { title: __('Primary Button', 'balefire') },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.primaryLabel,
                        onChange: (value) => setAttributes({ primaryLabel: value }),
                        placeholder: 'Get Started',
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.primaryUrl,
                        onChange: (value) => setAttributes({ primaryUrl: value }),
                        type: 'url',
                        placeholder: '/contact/',
                    })
                ),
                el(PanelBody, { title: __('Secondary Button', 'balefire') },
                    el(TextControl, {
                        label: __('Label', 'balefire'),
                        value: attributes.secondaryLabel,
                        onChange: (value) => setAttributes({ secondaryLabel: value }),
                        placeholder: 'Explore Solutions',
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.secondaryUrl,
                        onChange: (value) => setAttributes({ secondaryUrl: value }),
                        type: 'url',
                        placeholder: '/solutions/',
                    })
                )
            ),
            el('div', blockProps,
                // Overlay preview
                el('div', {
                    'aria-hidden': 'true',
                    className: 'bma-gradient-overlay',
                }),
                // Content
                el('div', { style: { position: 'relative', zIndex: 1, padding: '80px 24px', maxWidth: '64rem', margin: '0 auto', textAlign: 'center' } },
                    // Page title preview
                    el('h1', {
                        style: { fontSize: 'clamp(2.75rem, 2.35rem + 1.4vi, 3.25rem)', fontWeight: 500, color: '#fff', lineHeight: 1.1, letterSpacing: '-0.02em', marginBottom: '16px' },
                    }, 'Page Title'),
                    // Inline RichText for subtitle
                    el(RichText, {
                        tagName: 'p',
                        className: 'bma-hero-subtitle',
                        value: attributes.subtitle,
                        onChange: (value) => setAttributes({ subtitle: value }),
                        placeholder: __('Video hero text...', 'balefire'),
                        style: { fontSize: '1.125rem', color: 'rgba(255,255,255,0.8)', maxWidth: '42rem', margin: '0 auto' },
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                    }),
                    // Button previews
                    (attributes.primaryLabel || attributes.secondaryLabel) ? el('div', {
                        style: { marginTop: '40px', display: 'flex', gap: '16px', justifyContent: 'center', flexWrap: 'wrap' },
                    },
                        attributes.primaryLabel ? el('a', {
                            href: '#',
                            onClick: (e) => e.preventDefault(),
                            className: 'bma-hero-btn-primary btn btn-lg btn-black',
                            style: { display: 'inline-flex', alignItems: 'center', gap: '10px', borderRadius: '5px', background: '#272727', padding: '19px 28px', fontSize: '1rem', fontWeight: 600, color: '#fff', textDecoration: 'none', pointerEvents: 'none' },
                        },
                            attributes.primaryLabel,
                            el('svg', { style: { width: 16, height: 16, opacity: 0.7 }, xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 15 20', fill: 'none', 'aria-hidden': 'true' },
                                el('path', { d: arrowPath, fill: 'currentColor' })
                            )
                        ) : null,
                        attributes.secondaryLabel ? el('a', {
                            href: '#',
                            onClick: (e) => e.preventDefault(),
                            className: 'bma-hero-btn-secondary',
                            style: { display: 'inline-flex', alignItems: 'center', padding: '14px 28px', fontSize: '1rem', fontWeight: 600, color: '#fff', textDecoration: 'none', pointerEvents: 'none', position: 'relative' },
                        }, attributes.secondaryLabel) : null
                    ) : null
                )
            )
        );
    },
    save: () => null,
});

})();
