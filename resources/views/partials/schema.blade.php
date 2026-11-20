@php
    $settings = \App\Models\Setting::publicMap();
    $siteName = $brand['site_name'] ?? config('app.name', 'Elkris Foods');
    $baseUrl = rtrim(config('app.url', ''), '/');
    $logoPath = $brand['default_og_image'] ?? '/apple-touch-icon.png';
    $logo = str_starts_with($logoPath, 'http') ? $logoPath : $baseUrl.'/'.ltrim($logoPath, '/');
    $contactPoint = array_filter([
        'telephone' => $settings['contact.phone'] ?? null,
        'email' => $settings['contact.email'] ?? null,
    ]);
    $sameAs = array_values(array_filter([
        $settings['social.facebook'] ?? null,
        $settings['social.instagram'] ?? null,
        $settings['social.tiktok'] ?? null,
        $settings['social.youtube'] ?? null,
        $settings['social.x'] ?? null,
    ]));
@endphp
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => $baseUrl,
    'logo' => $logo,
    'contactPoint' => $contactPoint !== [] ? [array_merge(['contactType' => 'customer service'], $contactPoint)] : [],
    'sameAs' => $sameAs,
], JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'url' => $baseUrl,
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => ['@type' => 'EntryPoint', 'urlTemplate' => $baseUrl.'/search?q={search_term_string}'],
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_UNICODE) !!}</script>
@foreach (($page['props']['jsonLd'] ?? []) as $block)
    <script type="application/ld+json">{!! json_encode($block, JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
