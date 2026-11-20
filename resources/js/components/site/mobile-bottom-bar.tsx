import { Link, usePage } from '@inertiajs/react';
import { Home, MapPin, MessageCircle, ShoppingBag, User } from 'lucide-react';
import { buildWhatsAppUrl } from '@/lib/whatsapp';
import { home, login } from '@/routes';
import * as accountRoute from '@/routes/account';
import * as shopRoute from '@/routes/shop';
import * as storesRoute from '@/routes/stores';
import type { SharedProps } from '@/types';

export function MobileBottomBar() {
    const { settings, auth, content } = usePage<SharedProps>().props;
    const mobile = content.global.mobile;
    const whatsapp = settings?.['contact.whatsapp'];

    const items = [
        { label: mobile.home, href: home.url(), icon: Home },
        { label: mobile.shop, href: shopRoute.index.url(), icon: ShoppingBag },
        { label: mobile.stores, href: storesRoute.index.url(), icon: MapPin },
        ...(whatsapp
            ? [
                  {
                      label: mobile.whatsapp,
                      href: buildWhatsAppUrl(
                          whatsapp,
                          content.global.whatsapp.general_message,
                      ),
                      icon: MessageCircle,
                      external: true,
                  },
              ]
            : []),
        {
            label: mobile.account,
            href: auth.user ? accountRoute.index.url() : login.url(),
            icon: User,
        },
    ];

    return (
        <nav
            className="fixed inset-x-0 bottom-0 z-40 border-t bg-background/95 backdrop-blur lg:hidden"
            aria-label={mobile.aria_label}
            style={{ paddingBottom: 'env(safe-area-inset-bottom)' }}
        >
            <div className="mx-auto flex max-w-lg items-stretch justify-around">
                {items.map((item) => {
                    const Icon = item.icon;
                    const content = (
                        <span className="flex flex-col items-center gap-0.5 px-2 py-2 text-[11px] font-medium text-foreground/70">
                            <Icon className="size-5" />
                            {item.label}
                        </span>
                    );

                    return 'external' in item && item.external ? (
                        <a
                            key={item.label}
                            href={item.href}
                            target="_blank"
                            rel="noreferrer"
                        >
                            {content}
                        </a>
                    ) : (
                        <Link key={item.label} href={item.href}>
                            {content}
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
