(() => {
const { __, sprintf } = wp.i18n;
const apiFetch = wp.apiFetch;
const { createElement: el, Fragment, useMemo, useState, createRoot } = wp.element;
const {
    Button,
    Card,
    CardBody,
    Notice,
    SelectControl,
    Spinner,
    TextControl,
    ToggleControl,
} = wp.components;

const boot = window.balefireBlocksAdmin || {};

const TYPE_LABELS = {
    all: __('All Types', 'balefire'),
    headers: __('Headers', 'balefire'),
    cta: __('CTA', 'balefire'),
    text: __('Text', 'balefire'),
    layout: __('Layout', 'balefire'),
    'single-block': __('Single Block', 'balefire'),
    media: __('Media', 'balefire'),
    dynamic: __('Dynamic', 'balefire'),
    content: __('Content', 'balefire'),
    uncategorized: __('Uncategorized', 'balefire'),
};

const clone = (value) => JSON.parse(JSON.stringify(value || {}));

const blockType = (block) => block?.type || 'uncategorized';

const dependenciesMet = (settings, block) => {
    const deps = Array.isArray(block?.dependsOn) ? block.dependsOn : [];
    return deps.every((slug) => settings?.enabledBlocks?.[slug] !== false);
};

function App() {
    const blocks = Array.isArray(boot.blocks) ? boot.blocks : [];
    const [settings, setSettings] = useState(clone(boot.settings));
    const [search, setSearch] = useState('');
    const [type, setType] = useState('all');
    const [saving, setSaving] = useState(false);
    const [notice, setNotice] = useState(null);

    const typeOptions = useMemo(() => {
        const present = ['all', ...new Set(blocks.map(blockType))];
        return present.map((value) => ({
            value,
            label: TYPE_LABELS[value] || value,
        }));
    }, [blocks]);

    const visible = useMemo(() => {
        const query = search.trim().toLowerCase();
        return blocks.filter((block) => {
            if (type !== 'all' && blockType(block) !== type) {
                return false;
            }
            if (query === '') {
                return true;
            }
            const haystack = [
                block.slug,
                block.title,
                block.description,
                blockType(block),
                ...(Array.isArray(block.keywords) ? block.keywords : []),
            ].filter(Boolean).join(' ').toLowerCase();
            return haystack.includes(query);
        });
    }, [blocks, search, type]);

    const setEnabled = (slug, value) => {
        const next = clone(settings);
        next.enabledBlocks = next.enabledBlocks || {};
        next.enabledBlocks[slug] = value;
        // Disabling a dependency drags dependents down with it.
        if (!value) {
            blocks.forEach((block) => {
                const deps = Array.isArray(block.dependsOn) ? block.dependsOn : [];
                if (deps.includes(slug)) {
                    next.enabledBlocks[block.slug] = false;
                }
            });
        }
        setSettings(next);
    };

    const setDefault = (key, value) => {
        const next = clone(settings);
        next.defaults = next.defaults || {};
        next.defaults[key] = value;
        setSettings(next);
    };

    const save = () => {
        setSaving(true);
        setNotice(null);
        apiFetch({
            path: boot.restPath || '/wp/v2/settings',
            method: 'POST',
            data: { [boot.optionKey]: settings },
        }).then((response) => {
            const saved = response?.[boot.optionKey];
            if (saved) {
                setSettings(clone(saved));
            }
            setNotice({ status: 'success', text: __('Settings saved.', 'balefire') });
        }).catch((error) => {
            setNotice({
                status: 'error',
                text: error?.message || __('Saving failed.', 'balefire'),
            });
        }).finally(() => setSaving(false));
    };

    return el(Fragment, null,
        el('h1', { className: 'bma-admin-title' }, __('Balefire Blocks', 'balefire')),

        notice && el(Notice, {
            status: notice.status,
            onRemove: () => setNotice(null),
        }, notice.text),

        el(Card, { className: 'bma-admin-defaults' },
            el(CardBody, null,
                el('h2', null, __('Global Defaults', 'balefire')),
                el('div', { className: 'bma-admin-defaults__grid' },
                    el(SelectControl, {
                        label: __('Section max width', 'balefire'),
                        value: settings?.defaults?.sectionMaxWidth || 'wide',
                        options: [
                            { label: __('Content', 'balefire'), value: 'content' },
                            { label: __('Wide', 'balefire'), value: 'wide' },
                            { label: __('Full', 'balefire'), value: 'full' },
                        ],
                        onChange: (value) => setDefault('sectionMaxWidth', value),
                    }),
                    el(SelectControl, {
                        label: __('Button style', 'balefire'),
                        value: settings?.defaults?.buttonStyle || 'solid',
                        options: [
                            { label: __('Solid', 'balefire'), value: 'solid' },
                            { label: __('Outline', 'balefire'), value: 'outline' },
                        ],
                        onChange: (value) => setDefault('buttonStyle', value),
                    }),
                    el(TextControl, {
                        label: __('Posts per page (Posts Grid)', 'balefire'),
                        type: 'number',
                        min: 1,
                        max: 12,
                        value: String(settings?.defaults?.postsPerPage ?? 3),
                        onChange: (value) => setDefault('postsPerPage', Math.max(1, Math.min(12, parseInt(value, 10) || 3))),
                    })
                )
            )
        ),

        el('div', { className: 'bma-admin-toolbar' },
            el(TextControl, {
                className: 'bma-admin-toolbar__search',
                placeholder: __('Search blocks…', 'balefire'),
                value: search,
                onChange: setSearch,
            }),
            el(SelectControl, {
                className: 'bma-admin-toolbar__type',
                value: type,
                options: typeOptions,
                onChange: setType,
            }),
            el('span', { className: 'bma-admin-toolbar__count' },
                sprintf(
                    /* translators: %d: number of blocks shown. */
                    __('%d blocks', 'balefire'),
                    visible.length
                )
            )
        ),

        el('div', { className: 'bma-admin-grid' },
            visible.map((block) => {
                const enabled = settings?.enabledBlocks?.[block.slug] !== false;
                const depsOk = dependenciesMet(settings, block);
                const deps = Array.isArray(block.dependsOn) ? block.dependsOn : [];

                return el(Card, { key: block.slug, className: 'bma-admin-card' },
                    el(CardBody, null,
                        el('div', { className: 'bma-admin-card__head' },
                            el('h3', null, block.title || block.slug),
                            el('code', null, 'balefire/' + block.slug)
                        ),
                        block.description && el('p', { className: 'bma-admin-card__desc' }, block.description),
                        el('div', { className: 'bma-admin-card__meta' },
                            el('span', { className: 'bma-admin-card__type' }, TYPE_LABELS[blockType(block)] || blockType(block)),
                            deps.length > 0 && el('span', { className: 'bma-admin-card__deps' },
                                sprintf(
                                    /* translators: %s: comma-separated dependency slugs. */
                                    __('needs %s', 'balefire'),
                                    deps.join(', ')
                                )
                            )
                        ),
                        el(ToggleControl, {
                            label: enabled ? __('Enabled', 'balefire') : __('Disabled', 'balefire'),
                            checked: enabled && depsOk,
                            disabled: !depsOk,
                            help: !depsOk ? __('A required block is disabled.', 'balefire') : undefined,
                            onChange: (value) => setEnabled(block.slug, value),
                        })
                    )
                );
            })
        ),

        el('div', { className: 'bma-admin-save' },
            el(Button, {
                variant: 'primary',
                onClick: save,
                disabled: saving,
            }, saving ? el(Spinner) : __('Save Settings', 'balefire'))
        )
    );
}

const mount = document.getElementById('balefire-blocks-admin');
if (mount) {
    createRoot(mount).render(el(App));
}
})();
