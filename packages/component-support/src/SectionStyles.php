<?php

declare(strict_types=1);

namespace BalefireInc\Sage\Support;

final class SectionStyles
{
    public static function innerStyle(string $maxWidth): string
    {
        return match ($maxWidth) {
            'xs' => 'max-width: 320px;',
            'sm' => 'max-width: 384px;',
            'md' => 'max-width: 448px;',
            'lg' => 'max-width: 512px;',
            'xl' => 'max-width: 576px;',
            '2xl' => 'max-width: 672px;',
            '3xl' => 'max-width: 768px;',
            '4xl' => 'max-width: 896px;',
            '5xl' => 'max-width: 1024px;',
            '6xl' => 'max-width: 1152px;',
            '7xl' => 'max-width: 1280px;',
            'content' => 'max-width: var(--wp--style--global--content-size, 768px);',
            'wide' => 'max-width: var(--wp--style--global--wide-size, 1440px);',
            'full' => 'max-width: none;',
            default => 'max-width: var(--wp--style--global--wide-size, 1440px);',
        };
    }

    /**
     * Map a maxWidth token to a Tailwind max-w class.
     */
    public static function containerClass(string $maxWidth): string
    {
        return match ($maxWidth) {
            'narrow' => 'max-w-narrow',
            'content' => 'max-w-contentSize',
            'medium' => 'max-w-medium',
            'large' => 'max-w-large',
            'wide' => 'max-w-wideSize',
            'full' => 'max-w-dvw',
            default => 'max-w-wideSize',
        };
    }

    /**
     * Map a padding token to a Tailwind px class for inline padding.
     */
    public static function paddingInline(string $padding): string
    {
        return match ($padding) {
            'none' => 'px-0',
            'sm' => 'px-4',
            'md' => 'px-6',
            'lg' => 'px-8',
            default => 'px-6',
        };
    }

    public static function surface(string $tone): array
    {
        $darkMuted = 'text-[color-mix(in srgb,var(--color-dark)_65%,transparent)]';
        $darkSoft = 'text-[color-mix(in srgb,var(--color-dark)_70%,transparent)]';
        $darkSubtle = 'text-[color-mix(in srgb,var(--color-dark)_60%,transparent)]';
        $lightMuted = 'text-[color-mix(in srgb,var(--color-light)_78%,transparent)]';
        $lightSoft = 'text-[color-mix(in srgb,var(--color-light)_72%,transparent)]';
        $lightSubtle = 'text-[color-mix(in srgb,var(--color-light)_60%,transparent)]';

        return match ($tone) {
            'white' => [
                'section' => 'bg-white',
                'heading' => 'text-dark',
                'body' => $darkMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-dark)_80%,transparent)]',
                'meta' => $darkSubtle,
                'metaSoft' => $darkSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-primary)_10%,transparent)]',
                'accent' => 'text-primary',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-dark)_6%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-dark)_75%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-dark)_15%,transparent)]',
                'eyebrow' => 'text-primary',
            ],
            'light' => [
                'section' => 'bg-light',
                'heading' => 'text-dark',
                'body' => $darkMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-dark)_80%,transparent)]',
                'meta' => $darkSubtle,
                'metaSoft' => $darkSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-primary)_10%,transparent)]',
                'accent' => 'text-primary',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-dark)_6%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-dark)_75%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-dark)_15%,transparent)]',
                'eyebrow' => 'text-primary',
            ],
            'primary' => [
                'section' => 'bg-primary',
                'heading' => 'text-white',
                'body' => $lightMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-light)_88%,transparent)]',
                'meta' => $lightSubtle,
                'metaSoft' => $lightSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-light)_12%,transparent)]',
                'accent' => 'text-white',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-light)_12%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-light)_18%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-light)_92%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-light)_15%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-light)_18%,transparent)]',
                'eyebrow' => 'text-[color-mix(in srgb,var(--color-light)_88%,transparent)]',
            ],
            'secondary' => [
                'section' => 'bg-secondary',
                'heading' => 'text-dark',
                'body' => $darkMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-dark)_80%,transparent)]',
                'meta' => $darkSubtle,
                'metaSoft' => $darkSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'accent' => 'text-dark',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-dark)_8%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-dark)_12%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-dark)_78%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-dark)_12%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-dark)_15%,transparent)]',
                'eyebrow' => 'text-dark',
            ],
            'dark' => [
                'section' => 'bg-dark',
                'heading' => 'text-white',
                'body' => $lightMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-light)_88%,transparent)]',
                'meta' => $lightSubtle,
                'metaSoft' => $lightSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-primary)_18%,transparent)]',
                'accent' => 'text-primary',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-light)_12%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-light)_18%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-light)_92%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-light)_15%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-light)_18%,transparent)]',
                'eyebrow' => 'text-[color-mix(in srgb,var(--color-light)_88%,transparent)]',
            ],
            default => [
                'section' => 'bg-transparent',
                'heading' => 'text-dark',
                'body' => $darkMuted,
                'bodyStrong' => 'text-[color-mix(in srgb,var(--color-dark)_80%,transparent)]',
                'meta' => $darkSubtle,
                'metaSoft' => $darkSoft,
                'badge' => 'bg-[color-mix(in srgb,var(--color-primary)_10%,transparent)]',
                'accent' => 'text-primary',
                'chipBg' => 'bg-[color-mix(in srgb,var(--color-dark)_6%,transparent)]',
                'chipBgHover' => 'hover:bg-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'chipText' => 'text-[color-mix(in srgb,var(--color-dark)_75%,transparent)]',
                'ring' => 'ring-[color-mix(in srgb,var(--color-dark)_10%,transparent)]',
                'border' => 'border-[color-mix(in srgb,var(--color-dark)_15%,transparent)]',
                'eyebrow' => 'text-primary',
            ],
        };
    }
}
