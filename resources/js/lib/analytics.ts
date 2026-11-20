declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: (...args: unknown[]) => void;
        fbq?: (...args: unknown[]) => void;
    }
}

const metaEventMap: Record<string, string> = {
    page_view: 'PageView',
    view_item: 'ViewContent',
    begin_checkout: 'InitiateCheckout',
    WhatsApp_click: 'Contact',
    phone_click: 'Contact',
    email_click: 'Contact',
    store_locator_click: 'Contact',
    distributor_submit: 'Lead',
    newsletter_signup: 'Lead',
};

const metaParamKeys = [
    'content_ids',
    'content_name',
    'content_type',
    'value',
    'currency',
];

export function track(
    event: string,
    params: Record<string, unknown> = {},
): void {
    window.dataLayer = window.dataLayer ?? [];
    window.dataLayer.push({ event, ...params });

    if (typeof window.gtag === 'function') {
        window.gtag('event', event, params);
    }

    const metaName = metaEventMap[event];

    if (metaName && typeof window.fbq === 'function') {
        const metaParams = Object.fromEntries(
            Object.entries(params).filter(([key]) =>
                metaParamKeys.includes(key),
            ),
        );
        window.fbq('track', metaName, metaParams);
    }
}

function isWhatsAppLink(href: string): boolean {
    return (
        href.startsWith('https://wa.me/') ||
        href.startsWith('http://wa.me/') ||
        href.startsWith('https://api.whatsapp.com/') ||
        href.includes('whatsapp://') ||
        href.startsWith('https://web.whatsapp.com/')
    );
}

export function bindClickTracking(): void {
    document.addEventListener('click', (event) => {
        const target = event.target as HTMLElement | null;
        const anchor = target?.closest?.('a[href]') as HTMLAnchorElement | null;

        if (!anchor) {
            return;
        }

        const href = anchor.getAttribute('href') ?? '';

        if (isWhatsAppLink(href)) {
            track('WhatsApp_click', { link_url: anchor.href });

            if (href.includes('text=')) {
                track('begin_checkout', { link_url: anchor.href });
            }
        } else if (href.startsWith('tel:')) {
            track('phone_click', { link_url: href });
        } else if (href.startsWith('mailto:')) {
            track('email_click', { link_url: href });
        }
    });
}
