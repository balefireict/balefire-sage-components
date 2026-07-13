import metadata from '../../../blocks/preheader-and-title/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, SelectControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-preheader-and-title',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Preheader & Title', 'balefire') },
                    el(SelectControl, {
                        label: __('Text alignment', 'balefire'),
                        value: attributes.textAlign,
                        options: [
                            { label: __('Left', 'balefire'), value: 'left' },
                            { label: __('Center', 'balefire'), value: 'center' },
                            { label: __('Right', 'balefire'), value: 'right' },
                        ],
                        onChange: (value) => setAttributes({ textAlign: value }),
                    }),
                    el(TextControl, {
                        label: __('Preheader', 'balefire'),
                        value: attributes.preheader,
                        onChange: (value) => setAttributes({ preheader: value }),
                    }),
                    el(TextControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title,
                        onChange: (value) => setAttributes({ title: value }),
                    })
                )
            ),
            el('div', blockProps,
                el(ServerSideRender, { block: metadata.name, attributes, httpMethod: 'POST' })
            )
        );
    },
    save: () => null,
});
