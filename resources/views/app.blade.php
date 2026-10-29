<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color and prevent FOUC image expansion --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }

            img, svg, video, canvas {
                max-width: 100% !important;
                height: auto;
            }

            /* Swiper critical fallback rules to prevent image pop on F5 refresh */
            .swiper {
                width: 100%;
                overflow: hidden;
                display: block;
            }
            .swiper-wrapper {
                display: flex;
                width: 100%;
            }
            .swiper-slide {
                flex-shrink: 0;
                box-sizing: border-box;
            }

            /* Responsive exact aspect-ratio to kill CLS without cropping */
            #inicio {
                aspect-ratio: 1221 / 2161;
                width: 100%;
            }
            @media (min-width: 640px) {
                #inicio {
                    aspect-ratio: 16 / 9;
                }
            }

            /* Content visibility for below-the-fold sections to slash TBT */
            .content-auto {
                content-visibility: auto;
                contain-intrinsic-size: 1px 700px;
            }
        </style>

        <link rel="icon" href="/favicon.svg?v=3" type="image/svg+xml">
        <link rel="icon" href="/favicon.ico?v=3" type="image/x-icon" sizes="any">
        <link rel="shortcut icon" href="/favicon.ico?v=3">
        <link rel="apple-touch-icon" href="/favicon.png?v=3">

        {{-- LCP Image Preload Scanner --}}
        @php
            $heroPreload = $page['props']['_heroPreload'] ?? null;
        @endphp
        @if(!empty($heroPreload))
            @if(!empty($heroPreload['pc']))
                <link rel="preload" as="image" href="{{ $heroPreload['pc'] }}" media="(min-width: 640px)" fetchpriority="high">
            @endif
            @if(!empty($heroPreload['cel']))
                <link rel="preload" as="image" href="{{ $heroPreload['cel'] }}" media="(max-width: 639.98px)" fetchpriority="high">
            @endif
        @endif

        @fonts

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx', "resources/js/pages/{$page['component']}.jsx"])
        <x-inertia::head>
            @php
                $seoData = $page['props']['_seo'] ?? [];
                $seoTitle       = $seoData['title']       ?? config('app.name', 'Laravel');
                $seoDescription = $seoData['description'] ?? '';
                $seoKeywords    = $seoData['keywords']    ?? '';
            @endphp
            <title>{{ $seoTitle }}</title>
            @if($seoDescription)
                <meta name="description" content="{{ $seoDescription }}">
                <meta property="og:description" content="{{ $seoDescription }}">
            @endif
            @if($seoKeywords)
                <meta name="keywords" content="{{ $seoKeywords }}">
            @endif
            <meta property="og:title" content="{{ $seoTitle }}">
            <meta property="og:type" content="website">
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>