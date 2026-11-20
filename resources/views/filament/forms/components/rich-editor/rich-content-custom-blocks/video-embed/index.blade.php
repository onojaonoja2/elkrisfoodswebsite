<figure class="article-video">
    <div class="article-video-frame">
        @if ($embedUrl)
            <iframe
                src="{{ $embedUrl }}"
                title="{{ $title }}"
                loading="lazy"
                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
            ></iframe>
        @else
            <video src="{{ $videoUrl }}" controls playsinline preload="metadata"></video>
        @endif
    </div>

    @if ($caption !== '')
        <figcaption>{{ $caption }}</figcaption>
    @endif
</figure>
