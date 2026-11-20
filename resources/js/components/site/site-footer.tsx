import { Link, usePage } from '@inertiajs/react';
import { NewsletterSignup } from '@/components/site/newsletter-signup';
import type { SharedProps } from '@/types';

export function SiteFooter() {
    const { settings, content } = usePage<SharedProps>().props;
    const { brand, footer } = content.global;
    const socials = [
        { label: 'Facebook', url: settings?.['social.facebook'] },
        { label: 'Instagram', url: settings?.['social.instagram'] },
        { label: 'TikTok', url: settings?.['social.tiktok'] },
        { label: 'YouTube', url: settings?.['social.youtube'] },
        { label: 'X', url: settings?.['social.x'] },
    ].filter((social): social is { label: string; url: string } =>
        Boolean(social.url),
    );

    const copyright = footer.copyright.replace(
        '{year}',
        String(new Date().getFullYear()),
    );

    return (
        <footer className="mt-16 border-t bg-brand-mint text-brand-ink">
            <div className="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-5 lg:px-8">
                <div className="lg:col-span-2">
                    <img
                        src={brand.logo}
                        alt={brand.site_name}
                        className="h-12 w-auto"
                    />
                    <p className="mt-4 max-w-sm text-base text-brand-ink/75">
                        {settings?.['brand.positioning_line'] ??
                            footer.tagline_fallback}
                    </p>
                    <p className="mt-2 text-base font-semibold text-brand-green">
                        {settings?.['brand.positioning_tagline'] ??
                            footer.positioning_fallback}
                    </p>
                    <div className="mt-4 space-y-1 text-base text-brand-ink/75">
                        {settings?.['contact.address'] ? (
                            <p>{settings['contact.address']}</p>
                        ) : null}
                        {settings?.['contact.email'] ? (
                            <p>
                                <a
                                    href={`mailto:${settings['contact.email']}`}
                                    className="hover:text-brand-ink"
                                >
                                    {settings['contact.email']}
                                </a>
                            </p>
                        ) : null}
                        {settings?.['contact.phone'] ? (
                            <p>
                                <a
                                    href={`tel:${settings['contact.phone']}`}
                                    className="hover:text-brand-ink"
                                >
                                    {settings['contact.phone']}
                                </a>
                            </p>
                        ) : null}
                    </div>
                    {socials.length > 0 ? (
                        <div className="mt-4">
                            <p className="text-sm font-semibold tracking-wider text-brand-green uppercase">
                                {footer.follow_heading}
                            </p>
                            <div className="mt-2 flex flex-wrap gap-3">
                                {socials.map((social) => (
                                    <a
                                        key={social.label}
                                        href={social.url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="text-base text-brand-ink/75 underline-offset-2 hover:text-brand-ink hover:underline"
                                    >
                                        {social.label}
                                    </a>
                                ))}
                            </div>
                        </div>
                    ) : null}
                    <div className="mt-6">
                        <NewsletterSignup source="footer" />
                    </div>
                </div>

                {footer.columns.map((column) => (
                    <div key={column.title}>
                        <p className="text-sm font-semibold tracking-wider text-brand-green uppercase">
                            {column.title}
                        </p>
                        <ul className="mt-3 space-y-2">
                            {column.links.map((link) => (
                                <li key={`${link.label}-${link.url}`}>
                                    <Link
                                        href={link.url}
                                        className="text-base text-brand-ink/75 hover:text-brand-ink"
                                    >
                                        {link.label}
                                    </Link>
                                </li>
                            ))}
                        </ul>
                    </div>
                ))}
            </div>
            <div className="border-t border-brand-green/20 py-4 text-center text-sm text-brand-ink/70">
                {copyright}
            </div>
        </footer>
    );
}
