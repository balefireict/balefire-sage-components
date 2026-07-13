// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/simple-icon-stacked-cards",
    "title": "Simple Icon Stacked Cards",
    "category": "balefire",
    "icon": "columns",
    "description": "A white card with a 40\u00d740px icon (SVG or image) aligned flex-start with an optional link, plus innerBlocks for content.",
    "keywords": [
        "card",
        "icon",
        "stacked",
        "feature",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-simple-icon-stacked-cards-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "iconId": {
            "type": "number",
            "default": 0
        },
        "iconUrl": {
            "type": "string",
            "default": ""
        },
        "iconAlt": {
            "type": "string",
            "default": ""
        },
        "iconSvg": {
            "type": "string",
            "default": ""
        },
        "url": {
            "type": "string",
            "default": ""
        },
        "linkType": {
            "type": "string",
            "default": "none"
        },
        "pageId": {
            "type": "number",
            "default": 0
        },
        "openInNewTab": {
            "type": "boolean",
            "default": false
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
const {
    PanelBody,
    TextControl,
    TextareaControl,
    RadioControl,
    SelectControl,
    Spinner,
    ToggleControl,
} = wp.components;
const { createElement: el, Fragment } = wp.element;
const { useSelect } = wp.data;

const TEMPLATE = [
    ['core/heading', { level: 3, placeholder: __('Card title', 'balefire') }],
    ['core/paragraph', { placeholder: __('Card description…', 'balefire') }],
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className:
                'bma-simple-icon-stacked-card overflow-hidden rounded-[var(--radius-card,0.5rem)] bg-white border border-gray-200 p-[var(--spacing-card,1.5rem)]',
        });

        const innerBlocksProps = useInnerBlocksProps(
            { className: 'bma-simple-icon-stacked-card__content flex flex-col gap-4' },
            {
                template: TEMPLATE,
                templateLock: false,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        const { children: innerBlocksChildren, ...innerBlocksRest } = innerBlocksProps;

        const pages = useSelect((select) => {
            return (
                select('core').getEntityRecords('postType', 'page', {
                    per_page: 100,
                    orderby: 'title',
                    order: 'asc',
                }) || []
            );
        }, []);

        const pageOptions = [
            { label: __('— Select a page —', 'balefire'), value: 0 },
            ...pages.map((page) => ({ label: page.title.rendered, value: page.id })),
        ];

        // Icon preview: prefer raw SVG, fall back to uploaded image
        const iconPreview = attributes.iconSvg
            ? el('div', {
                  className: 'bma-simple-icon-stacked-card__icon',
                  dangerouslySetInnerHTML: { __html: attributes.iconSvg },
              })
            : attributes.iconUrl
            ? el(
                  'div',
                  { className: 'bma-simple-icon-stacked-card__icon' },
                  el('img', {
                      src: attributes.iconUrl,
                      alt: attributes.iconAlt,
                      className: 'w-10 h-10 object-contain',
                  })
              )
            : el('div', {
                  className: 'bma-simple-icon-stacked-card__icon',
                  style: {
                      width: '40px',
                      height: '40px',
                      background: '#f0f0f0',
                      border: '1px dashed #ccc',
                      borderRadius: '4px',
                  },
              });

        return el(
            Fragment,
            null,
            el(
                InspectorControls,
                null,
                el(
                    PanelBody,
                    { title: __('Icon Settings', 'balefire'), initialOpen: true },
                    el(
                        'div',
                        { className: 'components-base-control' },
                        el(
                            'label',
                            { className: 'components-base-control__label' },
                            __('Icon Image', 'balefire')
                        ),
                        el(MediaUpload, {
                            onSelect: (media) =>
                                setAttributes({
                                    iconId: media.id,
                                    iconUrl: media.url,
                                    iconAlt: media.alt || '',
                                }),
                            allowedTypes: ['image'],
                            value: attributes.iconId,
                            render: ({ open }) =>
                                el(
                                    'div',
                                    { style: { marginBottom: '8px' } },
                                    attributes.iconUrl
                                        ? el(
                                              'div',
                                              {
                                                  style: {
                                                      display: 'flex',
                                                      alignItems: 'center',
                                                      gap: '8px',
                                                  },
                                              },
                                              el('img', {
                                                  src: attributes.iconUrl,
                                                  alt: attributes.iconAlt,
                                                  style: {
                                                      maxWidth: '40px',
                                                      maxHeight: '40px',
                                                      objectFit: 'contain',
                                                  },
                                              }),
                                              el(
                                                  'button',
                                                  {
                                                      type: 'button',
                                                      onClick: open,
                                                      className:
                                                          'components-button is-secondary is-small',
                                                  },
                                                  __('Replace', 'balefire')
                                              ),
                                              el(
                                                  'button',
                                                  {
                                                      type: 'button',
                                                      onClick: () =>
                                                          setAttributes({
                                                              iconId: 0,
                                                              iconUrl: '',
                                                              iconAlt: '',
                                                          }),
                                                      className:
                                                          'components-button is-link is-destructive is-small',
                                                  },
                                                  __('Remove', 'balefire')
                                              )
                                          )
                                        : el(
                                              'button',
                                              {
                                                  type: 'button',
                                                  onClick: open,
                                                  className:
                                                      'components-button is-secondary',
                                              },
                                              __('Select Icon Image', 'balefire')
                                          )
                                ),
                        })
                    ),
                    el(TextareaControl, {
                        label: __('Or paste SVG code', 'balefire'),
                        value: attributes.iconSvg,
                        onChange: (value) => setAttributes({ iconSvg: value }),
                        placeholder: '<svg xmlns="http://www.w3.org/2000/svg" ...></svg>',
                        help: __(
                            'SVG takes priority over the uploaded image.',
                            'balefire'
                        ),
                        rows: 6,
                    })
                ),
                el(
                    PanelBody,
                    { title: __('Link Settings', 'balefire'), initialOpen: false },
                    el(RadioControl, {
                        label: __('Link', 'balefire'),
                        selected: attributes.linkType,
                        options: [
                            { label: __('None', 'balefire'), value: 'none' },
                            {
                                label: __('WordPress Page', 'balefire'),
                                value: 'page',
                            },
                            {
                                label: __('External URL', 'balefire'),
                                value: 'external',
                            },
                        ],
                        onChange: (value) => setAttributes({ linkType: value }),
                    }),
                    attributes.linkType === 'page'
                        ? pages.length === 0
                            ? el(
                                  'div',
                                  {
                                      style: {
                                          display: 'flex',
                                          alignItems: 'center',
                                          gap: '8px',
                                          padding: '8px 0',
                                      },
                                  },
                                  el(Spinner),
                                  el('span', null, __('Loading pages…', 'balefire'))
                              )
                            : el(SelectControl, {
                                  label: __('Select Page', 'balefire'),
                                  value: attributes.pageId,
                                  options: pageOptions,
                                  onChange: (value) =>
                                      setAttributes({ pageId: parseInt(value, 10) || 0 }),
                              })
                        : attributes.linkType === 'external'
                        ? el(TextControl, {
                              label: __('External URL', 'balefire'),
                              type: 'url',
                              value: attributes.url,
                              onChange: (value) => setAttributes({ url: value }),
                              placeholder: 'https://example.com',
                          })
                        : null,
                    attributes.linkType !== 'none'
                        ? el(ToggleControl, {
                              label: __('Open in new tab', 'balefire'),
                              checked: attributes.openInNewTab,
                              onChange: (value) => setAttributes({ openInNewTab: value }),
                          })
                        : null
                )
            ),
            el('div', blockProps,
                el('div', innerBlocksRest, iconPreview, innerBlocksChildren)
            )
        );
    },
    save: () => {
        const blockProps = useBlockProps.save({
            className: 'bma-simple-icon-stacked-card',
        });
        return el('div', blockProps, el(InnerBlocks.Content, null));
    },
    deprecated: [
        {
            attributes: metadata.attributes,
            supports: metadata.supports,
            // Old save returned null (dynamic-only block, no inner block markup).
            save: () => null,
            migrate: (attributes, innerBlocks) => [attributes, innerBlocks || []],
        },
    ],
});
