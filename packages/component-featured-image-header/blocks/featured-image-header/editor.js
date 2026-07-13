import metadata from '../../../blocks/featured-image-header/block.json';

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps, RichText } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;
const ServerSideRender = wp.serverSideRender;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-featured-image-header',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(RichText, {
                        tagName: 'p',
                        label: __('Intro Text', 'balefire'),
                        value: attributes.intro,
                        onChange: (value) => setAttributes({ intro: value }),
                        placeholder: __('Optional intro paragraph below the title...', 'balefire'),
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                    })
                ),
                el(PanelBody, { title: __('Settings', 'balefire') },
                    el(ToggleControl, {
                        label: __('Show on front page', 'balefire'),
                        help: __('By default this block is hidden on the front page. Enable to show it there.', 'balefire'),
                        checked: attributes.showOnFrontPage,
                        onChange: (value) => setAttributes({ showOnFrontPage: value }),
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
