import { Head, usePage } from '@inertiajs/react';
import type { SharedProps } from '@/types';

type SeoProps = {
    title?: string;
    description?: string;
    image?: string;
    type?: 'website' | 'article' | 'product';
    noindex?: boolean;
};

export function Seo({
    title,
    description,
    image,
    type = 'website',
    noindex = false,
}: SeoProps) {
    const page = usePage<SharedProps>();
    const brand = page.props.content.global.brand;
    const siteName = brand.site_name;
    const origin =
        typeof window !== 'undefined'
            ? window.location.origin
            : (page.props.siteUrl ?? '');
    const url = page.url ? `${origin}${page.url}` : undefined;
    const fullTitle = title ? `${title} | ${siteName}` : siteName;
    const effectiveDescription = description ?? brand.default_description;
    const effectiveImage = image ?? brand.default_og_image;
    const imageUrl = effectiveImage.startsWith('http')
        ? effectiveImage
        : `${origin}${effectiveImage}`;

    return (
        <Head>
            <title>{fullTitle}</title>
            <meta name="description" content={effectiveDescription} />
            <meta property="og:type" content={type} />
            <meta property="og:site_name" content={siteName} />
            <meta property="og:title" content={fullTitle} />
            <meta property="og:description" content={effectiveDescription} />
            {url ? <meta property="og:url" content={url} /> : null}
            <meta property="og:image" content={imageUrl} />
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" content={fullTitle} />
            <meta name="twitter:description" content={effectiveDescription} />
            <meta name="twitter:image" content={imageUrl} />
            {url ? <link rel="canonical" href={url} /> : null}
            {noindex ? (
                <meta name="robots" content="noindex, nofollow" />
            ) : null}
        </Head>
    );
}
