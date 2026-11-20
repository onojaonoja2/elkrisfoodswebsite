@php($brand = \App\Models\Setting::content('global')['brand'] ?? [])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'light') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(0.985 0.008 85);
            }

            html.dark {
                background-color: oklch(0.18 0.015 50);
            }
        </style>

        <title inertia>{{ $brand['site_name'] ?? config('app.name', 'Elkris Foods') }}</title>

        @include('partials.analytics')
        @include('partials.schema')

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="{{ $brand['favicon'] ?? '/favicon.svg' }}" type="image/svg+xml">
        <link rel="apple-touch-icon" href="{{ $brand['apple_touch_icon'] ?? '/apple-touch-icon.png' }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />

        @viteReactRefresh
        @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
