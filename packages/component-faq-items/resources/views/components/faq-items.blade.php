@props([
    // Inner-blocks HTML (source = inner). Not the intro copy — that is $intro.
    'content' => '',
    'tabs' => [],
    'eyebrow' => '',
    'eyebrowVariant' => '',
    'title' => '',
    'intro' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
    'layout' => 'stack',
    'tone' => '',
    'source' => 'inner',
    // Query-mode items: [ ['question' => '', 'answerHtml' => ''], ... ]
    'items' => [],
    'exclusive' => false,
    'emitSchema' => false,
    // Question/answer pairs for the FAQPage JSON-LD (either source).
    'schemaItems' => [],
])

@php
use BalefireInc\Sage\FaqItems\Items;
use BalefireInc\Sage\Support\SectionStyles;

$tabs = array_values(array_filter(array_map('trim', (array) $tabs)));
$layout = $layout === 'split' ? 'split' : 'stack';
$source = $source === 'query' ? 'query' : 'inner';
$items = is_array($items) ? $items : [];
$exclusive = filter_var($exclusive, FILTER_VALIDATE_BOOL);
$emitSchema = filter_var($emitSchema, FILTER_VALIDATE_BOOL);

// '' = the theme's own background and its own item styling (what accu-shot
// ships today). Any real tone opts into the package section + accordion skin.
$tone = sanitize_key((string) $tone);
$tone = array_key_exists($tone, SectionStyles::tones()) ? $tone : '';
$styled = $tone !== '';
$surface = SectionStyles::surface($tone);

$eyebrow = trim((string) $eyebrow);
$eyebrowVariant = sanitize_key((string) $eyebrowVariant);
$title = trim((string) $title);
$intro = trim((string) $intro);
$ctaLabel = trim((string) $ctaLabel);
$ctaUrl = trim((string) $ctaUrl);
// home_url(), not site_url(): Bedrock puts core in /wp.
$ctaUrl = $ctaUrl !== ''
    ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : $ctaUrl)
    : '';
$hasCta = $ctaLabel !== '' && $ctaUrl !== '';

// With nothing new set, the markup is exactly what the block always produced:
// centered "Frequently Asked Questions", optional tab bar, the list.
$legacy = !$styled && $layout === 'stack' && $eyebrow === '' && $title === '' && $intro === '' && !$hasCta;

// One id per instance so two FAQ blocks on a page cannot cross their
// aria-controls wiring, and so exclusive mode groups only its own items.
$uid = 'bma-faq-' . wp_unique_id();

// Exclusive: every <details> shares one name, so the browser closes the
// others when one opens. Inner items render themselves, so the name is
// stamped onto the finished HTML rather than passed down.
if ($exclusive && $source === 'inner' && $content !== '' && class_exists(\WP_HTML_Tag_Processor::class)) {
    $processor = new \WP_HTML_Tag_Processor($content);
    while ($processor->next_tag('details')) {
        $processor->set_attribute('name', $uid);
    }
    $content = $processor->get_updated_html();
}

$headingText = $title !== '' ? $title : __('Frequently Asked Questions', 'balefire');
// The <em> is the accent clause (see .bma-heading in component-support).
$headingAllowed = ['em' => [], 'strong' => [], 'br' => [], 'span' => ['class' => []]];

$schemaJson = $emitSchema ? Items::schema((array) $schemaItems) : '';
@endphp

@if ($source === 'query' && $items === [])
    <!-- balefire/faq-items: query returned no published items -->
@else
    <div
        {{ $attributes->class([
            'faq-section-items',
            'bma-faq-items',
            'bma-faq-items--' . $layout => !$legacy,
            $surface['section'] => $styled,
        ]) }}
        @if ($styled) data-bma-tone="{{ $tone }}" @endif
        @if ($tabs !== []) data-faq-root @endif
    >
        @if ($legacy)
            <h2 class="wp-block-heading has-text-align-center" id="faq-heading">Frequently Asked Questions</h2>
        @else
            <div class="bma-faq-items__inner">
                <div class="bma-faq-items__head">
                    @if ($eyebrow !== '')
                        <div class="bma-faq-items__eyebrow {{ $surface['eyebrow'] }}">
                            @if ($eyebrowVariant !== '')
                                <x-bma::eyebrow :text="$eyebrow" :variant="$eyebrowVariant" />
                            @else
                                <x-bma::eyebrow :text="$eyebrow" />
                            @endif
                        </div>
                    @endif

                    <h2 class="bma-heading bma-faq-items__title {{ $surface['heading'] }}" id="{{ $uid }}-heading">{!! wp_kses($headingText, $headingAllowed) !!}</h2>

                    @if ($intro !== '')
                        <div class="bma-faq-items__intro {{ $surface['body'] }}">
                            {!! wp_kses_post(wpautop($intro)) !!}
                        </div>
                    @endif

                    @if ($hasCta)
                        <p class="bma-faq-items__cta">
                            <a href="{{ $ctaUrl }}" class="bma-btn bma-btn--primary">{{ $ctaLabel }}</a>
                        </p>
                    @endif
                </div>

                <div class="bma-faq-items__body">
        @endif

        @if ($tabs !== [])
            {{-- "All" is always first and always the resting state, so the page
                 still shows every question with JavaScript unavailable — the
                 filter only ever hides things once it runs. --}}
            <div class="faq-section-tabs" role="tablist" aria-label="{{ __('Filter questions by topic', 'balefire') }}">
                <button
                    type="button"
                    role="tab"
                    class="faq-section-tab"
                    id="{{ $uid }}-tab-all"
                    aria-selected="true"
                    data-faq-tab="">{{ __('All', 'balefire') }}</button>

                @foreach ($tabs as $i => $tab)
                    <button
                        type="button"
                        role="tab"
                        class="faq-section-tab"
                        id="{{ $uid }}-tab-{{ $i }}"
                        aria-selected="false"
                        data-faq-tab="{{ $tab }}">{{ $tab }}</button>
                @endforeach
            </div>

            {{-- Announced when a filter changes; the questions themselves are not
                 a live region, so a screen reader would otherwise get no feedback
                 from pressing a tab. --}}
            <p class="faq-section-count" role="status" aria-live="polite" data-faq-count></p>
        @endif

        <div class="faq-section-list" data-faq-list>
            @if ($source === 'query')
                {{-- Same <details>/<summary> markup as a hand-authored item: the
                     single-item component renders both, so the two sources
                     cannot drift apart. `name` lands in its attribute bag. --}}
                @foreach ($items as $item)
                    @if ($exclusive)
                        <x-bma::faq-no-borders
                            :question="(string) ($item['question'] ?? '')"
                            :answer="(string) ($item['answerHtml'] ?? '')"
                            :name="$uid"
                        />
                    @else
                        <x-bma::faq-no-borders
                            :question="(string) ($item['question'] ?? '')"
                            :answer="(string) ($item['answerHtml'] ?? '')"
                        />
                    @endif
                @endforeach
            @else
                {!! $content !!}
            @endif
        </div>

        @if (!$legacy)
                </div>
            </div>
        @endif

        @if ($schemaJson !== '')
            <script type="application/ld+json">{!! $schemaJson !!}</script>
        @endif
    </div>
@endif
