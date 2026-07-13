(() => {
// Mirrors block.json — both are generated together; edit block.json and regenerate rather than hand-editing this literal.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/faq-no-borders",
    "title": "FAQ Item (No Borders)",
    "category": "balefire",
    "icon": "editor-help",
    "description": "A single FAQ accordion item with a question and answer, using pure CSS details/summary.",
    "keywords": [
        "faq",
        "accordion",
        "question",
        "answer",
        "balefire"
    ],
    "textdomain": "balefire",
    "editorScript": "balefire-faq-no-borders-editor",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true
    },
    "attributes": {
        "question": {
            "type": "string",
            "default": ""
        },
        "answer": {
            "type": "string",
            "default": ""
        },
        "openByDefault": {
            "type": "boolean",
            "default": false
        }
    },
    "version": "1.0.0"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, RichText, useBlockProps } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-editor-preview-faq-no-borders faq-section-item',
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('FAQ Item Settings', 'balefire') },
                    el(ToggleControl, {
                        label: __('Open by default', 'balefire'),
                        checked: !!attributes.openByDefault,
                        onChange: (value) => setAttributes({ openByDefault: value }),
                    })
                )
            ),
            el('div', blockProps,
                el('div', {
                    className: 'faq-section-question',
                    style: {
                        cursor: 'pointer',
                    },
                },
                    el(RichText, {
                        tagName: 'h3',
                        className: 'faq-section-question-title',
                        value: attributes.question,
                        onChange: (value) => setAttributes({ question: value }),
                        placeholder: __('Question goes here…', 'balefire'),
                        allowedFormats: [],
                    })
                ),
                el('div', {
                    className: 'faq-section-answer',
                    style: {
                        display: 'block',
                    },
                },
                    el(RichText, {
                        tagName: 'div',
                        value: attributes.answer,
                        onChange: (value) => setAttributes({ answer: value }),
                        placeholder: __('Answer goes here…', 'balefire'),
                        allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                    })
                )
            )
        );
    },
    save: () => null,
});

})();
