(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/simple-steps-card",
    "title": "Simple Steps Card",
    "category": "balefire",
    "icon": "index-card",
    "description": "A centered card with a 96\u00d796px icon (SVG or image), white/20 background, 3px white border, and centered text via innerBlocks.",
    "keywords": [
        "card",
        "icon",
        "steps",
        "centered",
        "feature",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-simple-steps-card-editor",
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
    TextareaControl,
} = wp.components;
const { createElement: el, Fragment } = wp.element;

const TEMPLATE = [
    ['core/heading', { level: 3, placeholder: __('Step title', 'balefire'), textAlign: 'center' }],
    ['core/paragraph', { placeholder: __('Step description…', 'balefire'), align: 'center' }],
];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className:
                'simple-steps-card flex flex-col items-center text-center rounded-[var(--radius-card,0.5rem)]',
        });

        const innerBlocksProps = useInnerBlocksProps(
            { className: 'simple-steps-card__content' },
            {
                template: TEMPLATE,
                templateLock: false,
                renderAppender: InnerBlocks.ButtonBlockAppender,
            }
        );

        const { children: innerBlocksChildren, ...innerBlocksRest } = innerBlocksProps;

        // Icon preview: prefer raw SVG, fall back to uploaded image
        const iconPreview = attributes.iconSvg
            ? el('div', {
                  className: 'simple-steps-card__icon mb-4',
                  dangerouslySetInnerHTML: { __html: attributes.iconSvg },
              })
            : attributes.iconUrl
            ? el(
                  'div',
                  { className: 'simple-steps-card__icon mb-4' },
                  el('img', {
                      src: attributes.iconUrl,
                      alt: attributes.iconAlt,
                      className: 'w-24 h-24 object-contain',
                  })
              )
            : el('div', {
                  className: 'simple-steps-card__icon mb-4',
                  style: {
                      width: '96px',
                      height: '96px',
                      background: 'rgba(255,255,255,0.2)',
                      border: '2px dashed rgba(255,255,255,0.5)',
                      borderRadius: '8px',
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
                                                      maxWidth: '96px',
                                                      maxHeight: '96px',
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
                )
            ),
            el('div', blockProps,
                iconPreview,
                el('div', innerBlocksRest, innerBlocksChildren)
            )
        );
    },
    save: () => el(InnerBlocks.Content),
});

})();
