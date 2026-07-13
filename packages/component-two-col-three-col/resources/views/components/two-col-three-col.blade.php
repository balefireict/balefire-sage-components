@props([
    'cards' => [],
])

@php
$cards = is_array($cards) ? $cards : [];
$isEditor = defined('REST_REQUEST') && REST_REQUEST;
@endphp

@if ($cards !== [])
    <div {{ $attributes->class(['bma-two-col-three-col']) }}>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-6">
            @foreach ($cards as $cardIndex => $card)
                @php
                $imageId = absint($card['imageId'] ?? 0);
                $imageUrl = (string) ($card['imageUrl'] ?? '');
                $imageAlt = (string) ($card['imageAlt'] ?? '');
                $title = (string) ($card['title'] ?? '');
                $prehead = (string) ($card['prehead'] ?? '');
                $prehead = $prehead !== '' ? $prehead : (string) ($card['subtitle'] ?? '');
                $text = (string) ($card['text'] ?? '');
                $linkType = (string) ($card['linkType'] ?? 'none');
                $pageId = absint($card['pageId'] ?? 0);
                $url = (string) ($card['url'] ?? '');

                if ($imageId > 0 && $imageUrl === '') {
                    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'full');
                }
                if ($imageAlt === '' && $imageId > 0) {
                    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
                }

                $linkHref = '';
                $isExternal = false;
                if (! $isEditor) {
                    if ($linkType === 'external' && $url !== '') {
                        $linkHref = $url;
                        $isExternal = true;
                    } elseif ($linkType === 'page' && $pageId > 0) {
                        $pageUrl = get_permalink($pageId);
                        if ($pageUrl !== false) {
                            $linkHref = $pageUrl;
                        }
                    }
                }

                $hasLink = $linkHref !== '';
                $tag = $hasLink ? 'a' : 'div';
                $cardClasses = implode(' ', array_filter([
                    $cardIndex < 2 ? 'lg:col-span-3' : 'lg:col-span-2',
                    'flex flex-col overflow-hidden rounded-[var(--radius-card,0.5rem)] border border-[var(--bma-two-col-three-col-card-border,var(--color-14,#e5e7eb))] bg-[var(--bma-two-col-three-col-card-bg,var(--white,#fff))]',
                    $hasLink ? 'text-inherit no-underline transition-[box-shadow,transform] duration-200 ease-in-out hover:-translate-y-0.5 hover:shadow-[var(--shadow-card)]' : '',
                ]));
                @endphp

                <{{ $tag }}
                    class="{{ $cardClasses }}"
                    @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
                    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                >
                    @if ($imageUrl !== '')
                        <div class="leading-none">
                            <img class="block aspect-video w-full object-cover" src="{{ esc_url($imageUrl) }}" alt="{{ $imageAlt }}" loading="lazy" />
                        </div>
                    @endif
                    <div class="p-6">
                        @if ($prehead !== '')
                            <p class="m-0 mb-1 text-[16px] prehead">{{ $prehead }}</p>
                        @endif
                        @if ($title !== '')
                            <h3 class="card-title">{{ $title }}</h3>
                        @endif
                        @if ($text !== '')
                            <div class="mb-2 md:mb-4 md:max-w-md">
                                <p>{{ $text }}</p>
                            </div>
                        @endif
                    </div>
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
@endif
