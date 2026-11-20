@php
    $ga4 = \App\Models\Setting::get('analytics.ga4_id');
    $ads = \App\Models\Setting::get('analytics.google_ads_id');
    $pixel = \App\Models\Setting::get('analytics.meta_pixel_id');
    $verification = \App\Models\Setting::get('analytics.search_console_verification');
@endphp

@if($verification)
    <meta name="google-site-verification" content="{{ $verification }}">
@endif

@if($ga4 || $ads)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 ?: $ads }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        @if($ga4)
            gtag('config', @json($ga4), { send_page_view: false });
            gtag('event', 'page_view', {
                page_location: window.location.href,
                page_title: document.title,
            });
        @endif
        @if($ads)
            gtag('config', @json($ads));
        @endif
    </script>
@endif

@if($pixel)
    <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', @json($pixel));
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixel }}&amp;ev=PageView&amp;noscript=1"></noscript>
@endif
