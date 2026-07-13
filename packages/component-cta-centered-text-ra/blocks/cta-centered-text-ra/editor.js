import metadata from '../../../blocks/cta-centered-text-ra/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
const { PanelBody, TextControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-cta-centered-text-ra',
        });

        const hasButtons = (attributes.primaryLabel && attributes.primaryUrl)
            || (attributes.secondaryLabel && attributes.secondaryUrl);

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Preheader & Title', 'balefire') },
                    el(TextControl, {
                        label: __('Preheader', 'balefire'),
                        value: attributes.preheader,
                        onChange: (value) => setAttributes({ preheader: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextControl, {
                        label: __('CTA Text', 'balefire'),
                        value: attributes.ctaText,
                        onChange: (value) => setAttributes({ ctaText: value }),
                    })
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
                        placeholder: 'Learn More',
                    }),
                    el(TextControl, {
                        label: __('URL', 'balefire'),
                        value: attributes.secondaryUrl,
                        onChange: (value) => setAttributes({ secondaryUrl: value }),
                        type: 'url',
                        placeholder: '/about/',
                    })
                )
            ),
            el('div', blockProps,
                el('div', { style: { textAlign: 'center', maxWidth: '48rem', margin: '0 auto', padding: '24px' } },
                    attributes.preheader ? el('p', {
                        style: { fontSize: '0.875rem', fontWeight: 600, textTransform: 'uppercase', letterSpacing: '0.2em', opacity: 0.8, marginBottom: '16px' },
                    }, attributes.preheader) : null,

                    attributes.title ? el('h2', {
                        style: { fontSize: 'clamp(1.875rem, 2.35rem + 1.4vi, 3rem)', fontWeight: 'inherit', lineHeight: 1.05 },
                    }, attributes.title) : null,

                    attributes.ctaText ? el('p', {
                        style: { marginTop: '16px', fontSize: '1.125rem', fontWeight: 600 },
                    }, attributes.ctaText) : null,

                    el(RichText, {
                        tagName: 'div',
                        value: attributes.content,
                        onChange: (value) => setAttributes({ content: value }),
                        placeholder: __('Body text...', 'balefire'),
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                        style: { maxWidth: '62ch', margin: '16px auto 0', lineHeight: 1.7 },
                    }),

                    hasButtons ? el('div', {
                        className: 'btn-group mt-[var(--spacing-section)] flex flex-col items-center justify-center gap-4 sm:flex-row',
                    },
                        attributes.primaryLabel ? el('a', {
                            className: 'btn btn-white inline-flex items-center justify-center gap-2.5',
                            style: { pointerEvents: 'none' },
                        },
                            attributes.primaryLabel,
                            el('span', null,
                                el('svg', { className: 'bma-hero-btn-arrow size-4 shrink-0', xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 15 20', fill: 'none', 'aria-hidden': 'true' },
                                    el('path', { d: 'M10.652 13.735l3.964-3.967a.476.476 0 000-.672L10.652 5.13a.466.466 0 00-.332-.138.476.476 0 00-.34.809l3.155 3.155H4.817a.475.475 0 000 .951h8.318L9.979 13.063a.477.477 0 00.342.809.467.467 0 00.331-.136Z', fill: 'currentColor' })
                                )
                            )
                        ) : null,
                        attributes.secondaryLabel ? el('span', {
                            className: 'btn-transparent inline-flex items-center justify-center px-[var(--spacing-card)] py-[var(--spacing-card)] text-base font-semibold text-white no-underline transition',
                            style: { pointerEvents: 'none' },
                        }, attributes.secondaryLabel) : null
                    ) : null
                )
            )
        );
    },
    save: () => null,
});
