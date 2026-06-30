import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    InspectorControls,
    useBlockProps,
} from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    TextareaControl,
    SelectControl,
} from '@wordpress/components';

registerBlockType('balefire/cta-banner', {
    edit: ({ attributes, setAttributes }) => {
        const blockProps = useBlockProps();

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('Content', 'balefire')} initialOpen>
                        <TextControl
                            label={__('Eyebrow', 'balefire')}
                            value={attributes.eyebrow || ''}
                            onChange={(val) => setAttributes({ eyebrow: val })}
                        />
                        <TextControl
                            label={__('Title', 'balefire')}
                            value={attributes.title || ''}
                            onChange={(val) => setAttributes({ title: val })}
                        />
                        <TextareaControl
                            label={__('Body Content', 'balefire')}
                            value={attributes.content || ''}
                            onChange={(val) => setAttributes({ content: val })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Appearance', 'balefire')} initialOpen={false}>
                        <SelectControl
                            label={__('Tone', 'balefire')}
                            value={attributes.tone || 'primary'}
                            options={[
                                { label: __('Primary', 'balefire'), value: 'primary' },
                                { label: __('Secondary', 'balefire'), value: 'secondary' },
                                { label: __('Dark', 'balefire'), value: 'dark' },
                                { label: __('Light', 'balefire'), value: 'light' },
                            ]}
                            onChange={(val) => setAttributes({ tone: val })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Primary Button', 'balefire')} initialOpen={false}>
                        <TextControl
                            label={__('Label', 'balefire')}
                            value={attributes.primaryLabel || ''}
                            onChange={(val) => setAttributes({ primaryLabel: val })}
                        />
                        <TextControl
                            label={__('URL', 'balefire')}
                            value={attributes.primaryUrl || ''}
                            onChange={(val) => setAttributes({ primaryUrl: val })}
                        />
                        <SelectControl
                            label={__('Style', 'balefire')}
                            value={attributes.primaryStyle || 'solid'}
                            options={[
                                { label: __('Solid', 'balefire'), value: 'solid' },
                                { label: __('Outline', 'balefire'), value: 'outline' },
                            ]}
                            onChange={(val) => setAttributes({ primaryStyle: val })}
                        />
                    </PanelBody>

                    <PanelBody title={__('Secondary Button', 'balefire')} initialOpen={false}>
                        <TextControl
                            label={__('Label', 'balefire')}
                            value={attributes.secondaryLabel || ''}
                            onChange={(val) => setAttributes({ secondaryLabel: val })}
                        />
                        <TextControl
                            label={__('URL', 'balefire')}
                            value={attributes.secondaryUrl || ''}
                            onChange={(val) => setAttributes({ secondaryUrl: val })}
                        />
                    </PanelBody>
                </InspectorControls>

                {/* Server-side preview placeholder.
                    The actual frontend is rendered by render.php via Blade.
                    This gives the editor something visible without duplicating
                    markup in React. */}
                <div {...blockProps}>
                    <div className="bma-cta-banner rounded-[2rem] px-6 py-8 md:px-10 md:py-12 bg-neutral-100 text-dark">
                        <div className="mx-auto flex max-w-[72rem] flex-col gap-8 md:flex-row md:items-end md:justify-between">
                            <div className="max-w-[44rem] space-y-4">
                                {attributes.eyebrow && (
                                    <p className="text-sm font-semibold uppercase tracking-[0.2em] text-dark/80">
                                        {attributes.eyebrow}
                                    </p>
                                )}
                                {attributes.title && (
                                    <h2 className="text-3xl font-bold leading-[1.05] md:text-5xl">
                                        {attributes.title}
                                    </h2>
                                )}
                                {attributes.content && (
                                    <div className="max-w-[62ch] text-base leading-7 text-dark/85">
                                        {attributes.content}
                                    </div>
                                )}
                            </div>
                            {(attributes.primaryLabel || attributes.secondaryLabel) && (
                                <div className="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end">
                                    {attributes.primaryLabel && (
                                        <span className="inline-flex items-center justify-center rounded-full px-6 py-3 font-semibold bg-white text-dark">
                                            {attributes.primaryLabel}
                                        </span>
                                    )}
                                    {attributes.secondaryLabel && (
                                        <span className="inline-flex items-center justify-center rounded-full border border-current px-6 py-3 font-semibold text-current">
                                            {attributes.secondaryLabel}
                                        </span>
                                    )}
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </>
        );
    },

    // PHP render callback handles the frontend. No React save.
    save: () => null,
});
