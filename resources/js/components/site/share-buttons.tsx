import { usePage } from '@inertiajs/react';
import { Check, Facebook, Link2, MessageCircle } from 'lucide-react';
import { useState } from 'react';
import type { SharedProps } from '@/types';

const shareButtonClass =
    'inline-flex items-center gap-2 rounded-full border border-brand-green/40 bg-background px-4 py-2 text-sm font-medium text-brand-ink hover:border-brand-green hover:bg-brand-green/5';

export function ShareButtons({
    title,
    text,
}: {
    title: string;
    text?: string | null;
}) {
    const { content } = usePage<SharedProps>().props;
    const labels = content.global.share;
    const [copied, setCopied] = useState(false);

    const currentUrl = () =>
        typeof window === 'undefined' ? '' : window.location.href;
    const shareText = text ? `${title}: ${text}` : title;

    const shareLinks = [
        {
            label: 'WhatsApp',
            icon: MessageCircle,
            href: `https://wa.me/?text=${encodeURIComponent(`${shareText} ${currentUrl()}`)}`,
        },
        {
            label: 'Facebook',
            icon: Facebook,
            href: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl())}`,
        },
        {
            label: 'X',
            icon: null,
            href: `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl())}&text=${encodeURIComponent(shareText)}`,
        },
    ];

    const copyLink = async () => {
        try {
            await navigator.clipboard.writeText(currentUrl());
            setCopied(true);
            window.setTimeout(() => setCopied(false), 2000);
        } catch {
            setCopied(false);
        }
    };

    return (
        <div className="flex flex-wrap items-center gap-3">
            <span className="text-sm font-semibold text-brand-green">
                {labels.label}
            </span>
            {shareLinks.map((share) => (
                <a
                    key={share.label}
                    href={share.href}
                    target="_blank"
                    rel="noreferrer"
                    className={shareButtonClass}
                >
                    {share.label === 'X' ? (
                        <span className="text-xs font-bold">𝕏</span>
                    ) : share.icon ? (
                        <share.icon className="size-4" />
                    ) : null}
                    {share.label}
                </a>
            ))}
            <button
                type="button"
                onClick={copyLink}
                className={shareButtonClass}
            >
                {copied ? (
                    <Check className="size-4 text-brand-green" />
                ) : (
                    <Link2 className="size-4" />
                )}
                {copied ? labels.copied : labels.copy}
            </button>
        </div>
    );
}
