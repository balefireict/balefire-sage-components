(() => {
// Mirrors block.json — both must stay in sync; edit block.json first.
const metadata = {
    "$schema": "https://schemas.wp.org/trunk/block.json",
    "apiVersion": 3,
    "name": "balefire/mission-cards",
    "title": "Mission Cards",
    "category": "balefire",
    "icon": "grid-view",
    "description": "Find-your-fit section: cards generated from Mission taxonomy terms.",
    "keywords": ["mission", "cards", "use case", "category", "balefire"],
    "textdomain": "balefire",
    "version": "1.0.0",
    "render": "file:./render.php",
    "supports": {
        "anchor": true,
        "className": true,
        "align": ["full", "wide"]
    },
    "attributes": {
        "eyebrow": { "type": "string", "default": "Find Your Fit" },
        "title": { "type": "string", "default": "Find the right support for your rifle" },
        "content": { "type": "string", "default": "Start with how you shoot, or jump straight to a model family. Either path takes you to the right setup in a click or two." },
        "termIds": { "type": "array", "default": [] },
        "limit": { "type": "number", "default": 3 },
        "align": { "type": "string", "default": "full" }
    },
    "editorScript": "balefire-mission-cards-editor"
};

const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const { InspectorControls, useBlockProps } = wp.blockEditor;
const { PanelBody, TextControl, TextareaControl, RangeControl, CheckboxControl, Notice } = wp.components;
const { createElement: el, Fragment } = wp.element;

// Injected by src/bootstrap.php. Empty until the Mission taxonomy is imported.
const MISSION_TERMS = Array.isArray(window.balefireMissionTerms) ? window.balefireMissionTerms : [];

registerBlockType(metadata.name, {
    ...metadata,
    edit: ({ attributes, setAttributes }) => {
        const selected = Array.isArray(attributes.termIds) ? attributes.termIds : [];

        const toggleTerm = (id, checked) => {
            setAttributes({
                termIds: checked
                    ? [...selected, id]
                    : selected.filter((t) => t !== id),
            });
        };

        const shown = selected.length
            ? MISSION_TERMS.filter((t) => selected.includes(t.id))
            : MISSION_TERMS.slice(0, attributes.limit || 3);

        const blockProps = useBlockProps({
            className: 'bma-editor-preview bma-mission-cards',
            style: { background: '#f4f4f4', padding: '32px' },
        });

        return el(Fragment, null,
            el(InspectorControls, null,
                el(PanelBody, { title: __('Content', 'balefire'), initialOpen: true },
                    el(TextControl, {
                        label: __('Eyebrow', 'balefire'),
                        value: attributes.eyebrow || '',
                        onChange: (value) => setAttributes({ eyebrow: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Title', 'balefire'),
                        value: attributes.title || '',
                        onChange: (value) => setAttributes({ title: value }),
                    }),
                    el(TextareaControl, {
                        label: __('Description', 'balefire'),
                        value: attributes.content || '',
                        onChange: (value) => setAttributes({ content: value }),
                    })
                ),

                el(PanelBody, { title: __('Missions', 'balefire'), initialOpen: true },
                    MISSION_TERMS.length === 0
                        ? el(Notice, { status: 'warning', isDismissible: false },
                            __('No Mission terms yet. Import acf-exports/mission-taxonomy.json via ACF > Tools, then add terms under Posts > Missions.', 'balefire'))
                        : el(Fragment, null,
                            el('p', { style: { marginTop: 0 } },
                                __('Pick the missions to show. Leave all unchecked to show the newest, limited below.', 'balefire')),
                            MISSION_TERMS.map((term) => el(CheckboxControl, {
                                key: term.id,
                                label: term.name,
                                checked: selected.includes(term.id),
                                onChange: (checked) => toggleTerm(term.id, checked),
                            })),
                            el(RangeControl, {
                                label: __('Max cards', 'balefire'),
                                value: attributes.limit || 3,
                                min: 1,
                                max: 12,
                                onChange: (value) => setAttributes({ limit: value }),
                            })
                        )
                )
            ),

            // Editor preview placeholder. The frontend is rendered by
            // render.php via Blade; this avoids duplicating markup in React.
            el('div', blockProps,
                el('p', { style: { color: '#d72b27', fontWeight: 700, textTransform: 'uppercase', margin: '0 0 8px' } },
                    attributes.eyebrow || ''),
                el('h2', { style: { margin: '0 0 8px', textTransform: 'uppercase', fontSize: '32px', lineHeight: 1.1 } },
                    attributes.title || __('Mission Cards', 'balefire')),
                attributes.content && el('p', { style: { color: '#747474', margin: '0 0 24px', maxWidth: '600px' } },
                    attributes.content),
                el('div', { style: { display: 'flex', gap: '16px', flexWrap: 'wrap' } },
                    shown.length === 0
                        ? el('em', { style: { color: '#747474' } }, __('No missions to show yet.', 'balefire'))
                        : shown.map((term) => el('div', {
                            key: term.id,
                            style: {
                                flex: '1 1 180px',
                                background: '#fff',
                                borderRadius: '8px',
                                padding: '16px',
                                fontWeight: 700,
                                textTransform: 'uppercase',
                            },
                        }, term.name))
                )
            )
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
})();
