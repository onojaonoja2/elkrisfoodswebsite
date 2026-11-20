import { usePage } from '@inertiajs/react';
import { MobileBottomBar } from '@/components/site/mobile-bottom-bar';
import { SiteFooter } from '@/components/site/site-footer';
import { SiteHeader } from '@/components/site/site-header';
import { WhatsAppFab } from '@/components/site/whatsapp-fab';
import type { SharedProps } from '@/types';

export default function SiteLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    const page = usePage<SharedProps>();
    const { flash } = page.props;
    const currentUrl = page.url ?? '';
    const isHome = currentUrl === '/' || currentUrl.startsWith('/?');
    const hasFlash = Boolean(flash?.success);
    const topOffset = isHome ? '' : hasFlash ? 'pt-2' : 'pt-24';

    return (
        <div className="flex min-h-screen flex-col">
            <SiteHeader />
            {hasFlash ? (
                <div
                    className="bg-brand-green px-4 pt-24 pb-3 text-center text-sm font-medium text-white"
                    role="status"
                >
                    {flash?.success}
                </div>
            ) : null}
            <main className={`flex-1 pb-20 lg:pb-0 ${topOffset}`}>
                {children}
            </main>
            <SiteFooter />
            <WhatsAppFab />
            <MobileBottomBar />
        </div>
    );
}
