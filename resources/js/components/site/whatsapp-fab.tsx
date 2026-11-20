import { usePage } from '@inertiajs/react';
import { MessageCircle } from 'lucide-react';
import { buildWhatsAppUrl } from '@/lib/whatsapp';
import type { SharedProps } from '@/types';

export function WhatsAppFab() {
    const { settings, content } = usePage<SharedProps>().props;
    const whatsapp = settings?.['contact.whatsapp'];
    const whatsappContent = content.global.whatsapp;

    if (!whatsapp) {
        return null;
    }

    return (
        <a
            href={buildWhatsAppUrl(whatsapp, whatsappContent.general_message)}
            target="_blank"
            rel="noreferrer"
            className="fixed right-4 bottom-20 z-50 inline-flex size-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition-transform hover:scale-105 lg:bottom-6"
            aria-label={whatsappContent.fab_label}
        >
            <MessageCircle className="size-7" />
        </a>
    );
}
