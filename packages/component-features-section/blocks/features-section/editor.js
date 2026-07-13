import metadata from '../../../blocks/features-section/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-features-section',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Features Section Settings', 'balefire') },
                    el(TextControl, {
                        label: __('Heading', 'balefire'),
                        value: attributes.heading,
                        onChange: (value) => setAttributes({ heading: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Intro text', 'balefire'),
                        value: attributes.intro,
                        onChange: (value) => setAttributes({ intro: value }),
                    }),
                    el(SelectControl, {
                        label: __('Max width', 'balefire'),
                        value: attributes.maxWidth,
                        options: [
                            { label: __('Content', 'balefire'), value: 'content' },
                            { label: __('Wide', 'balefire'), value: 'wide' },
                            { label: __('Full', 'balefire'), value: 'full' },
                        ],
                        onChange: (value) => setAttributes({ maxWidth: value }),
                    }),
                    el(SelectControl, {
                        label: __('Background tone', 'balefire'),
                        value: attributes.backgroundTone,
                        options: [
                            { label: __('Transparent', 'balefire'), value: 'transparent' },
                            { label: __('White', 'balefire'), value: 'white' },
                            { label: __('Light', 'balefire'), value: 'light' },
                            { label: __('Primary', 'balefire'), value: 'primary' },
                            { label: __('Secondary', 'balefire'), value: 'secondary' },
                            { label: __('Dark', 'balefire'), value: 'dark' },
                        ],
                        onChange: (value) => setAttributes({ backgroundTone: value }),
                    })
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, {
                    block: metadata.name,
                    attributes,
                    httpMethod: 'POST',
                })
            )
        );
    },
    save: () => null,
});
