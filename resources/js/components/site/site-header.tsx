import { Link, usePage } from '@inertiajs/react';
import { Menu, Moon, Search, ShoppingBag, Sun, X } from 'lucide-react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { useAppearance } from '@/hooks/use-appearance';
import { useNavOverflow } from '@/hooks/use-nav-overflow';
import { home } from '@/routes';
import * as accountRoute from '@/routes/account';
import * as searchRoute from '@/routes/search';
import * as shopRoute from '@/routes/shop';
import type { SharedProps } from '@/types';

const navLinkClassName =
    'whitespace-nowrap rounded-full px-3 py-2 text-sm font-medium text-foreground/90 transition-colors hover:bg-accent hover:text-foreground';

export function SiteHeader() {
    const { auth, content } = usePage<SharedProps>().props;
    const { brand, nav } = content.global;
    const [open, setOpen] = useState(false);
    const { resolvedAppearance, updateAppearance } = useAppearance();
    const { navRef, measurementRef, visibleItems, hiddenItems } =
        useNavOverflow(nav.items);
    const hasHiddenItems = hiddenItems.length > 0;
    const menuOpen = open && hasHiddenItems;

    const toggleTheme = () => {
        updateAppearance(resolvedAppearance === 'dark' ? 'light' : 'dark');
    };

    return (
        <header className="pointer-events-none fixed inset-x-0 top-0 z-50 px-4 pt-4 sm:px-6 sm:pt-5">
            <div className="pointer-events-auto relative mx-auto flex h-14 max-w-5xl items-center justify-between gap-3 rounded-full border border-border/60 bg-background/90 px-4 shadow-lg backdrop-blur supports-[backdrop-filter]:bg-background/80 sm:h-16 sm:px-5 xl:max-w-6xl 2xl:max-w-7xl">
                <Link
                    href={home.url()}
                    className="flex shrink-0 items-center gap-2"
                >
                    <img
                        src={brand.logo}
                        alt={brand.site_name}
                        className="h-9 w-auto sm:h-10"
                    />
                </Link>

                <nav
                    ref={navRef}
                    className="hidden min-w-0 flex-1 items-center justify-center gap-0.5 overflow-x-clip lg:flex"
                    aria-label="Main"
                >
                    {visibleItems.map((item) => (
                        <Link
                            key={`${item.label}-${item.url}`}
                            href={item.url}
                            className={`shrink-0 ${navLinkClassName}`}
                        >
                            {item.label}
                        </Link>
                    ))}
                </nav>
                <div
                    ref={measurementRef}
                    aria-hidden="true"
                    className="pointer-events-none invisible absolute top-0 left-0 flex w-max gap-0.5"
                >
                    {nav.items.map((item) => (
                        <span
                            key={`${item.label}-${item.url}`}
                            className={navLinkClassName}
                        >
                            {item.label}
                        </span>
                    ))}
                </div>

                <div className="flex shrink-0 items-center gap-2">
                    {auth.user ? (
                        <Link
                            href={accountRoute.index.url()}
                            className="hidden sm:block"
                        >
                            <Button
                                variant="outline"
                                size="sm"
                                className="rounded-full"
                            >
                                {nav.account_label}
                            </Button>
                        </Link>
                    ) : null}
                    <Link
                        href={searchRoute.index.url()}
                        className="inline-flex size-10 items-center justify-center rounded-full transition-colors hover:bg-accent"
                        aria-label={nav.search_label}
                    >
                        <Search className="size-5" />
                    </Link>
                    <Link
                        href={shopRoute.index.url()}
                        className="hidden sm:block"
                    >
                        <Button
                            size="sm"
                            className="rounded-full px-5 font-semibold"
                        >
                            <ShoppingBag className="size-4" />
                            {nav.shop_label}
                        </Button>
                    </Link>
                    <button
                        type="button"
                        onClick={toggleTheme}
                        className="inline-flex size-10 items-center justify-center rounded-full transition-colors hover:bg-accent"
                        aria-label={
                            resolvedAppearance === 'dark'
                                ? nav.theme_light_label
                                : nav.theme_dark_label
                        }
                    >
                        {resolvedAppearance === 'dark' ? (
                            <Sun className="size-5" />
                        ) : (
                            <Moon className="size-5" />
                        )}
                    </button>
                    {hasHiddenItems ? (
                        <button
                            type="button"
                            className="hidden size-10 items-center justify-center rounded-full transition-colors hover:bg-accent lg:inline-flex"
                            onClick={() => setOpen((value) => !value)}
                            aria-label={
                                menuOpen
                                    ? nav.menu_close_label
                                    : nav.menu_open_label
                            }
                            aria-expanded={menuOpen}
                        >
                            {menuOpen ? (
                                <X className="size-5" />
                            ) : (
                                <Menu className="size-5" />
                            )}
                        </button>
                    ) : null}
                    <button
                        type="button"
                        className="inline-flex size-10 items-center justify-center rounded-full transition-colors hover:bg-accent lg:hidden"
                        onClick={() => setOpen((value) => !value)}
                        aria-label={
                            menuOpen
                                ? nav.menu_close_label
                                : nav.menu_open_label
                        }
                        aria-expanded={menuOpen}
                    >
                        {menuOpen ? (
                            <X className="size-5" />
                        ) : (
                            <Menu className="size-5" />
                        )}
                    </button>
                </div>
            </div>

            {menuOpen ? (
                <nav
                    className="pointer-events-auto mx-auto mt-2 max-w-5xl rounded-3xl border border-border/60 bg-background/95 p-3 shadow-lg backdrop-blur xl:max-w-6xl 2xl:max-w-7xl"
                    aria-label="Menu"
                >
                    <div className="flex flex-col gap-0.5">
                        {hiddenItems.map((item) => (
                            <Link
                                key={`${item.label}-${item.url}`}
                                href={item.url}
                                onClick={() => setOpen(false)}
                                className="rounded-2xl px-4 py-3 text-sm font-medium whitespace-nowrap text-foreground/90 hover:bg-accent hover:text-foreground"
                            >
                                {item.label}
                            </Link>
                        ))}
                        <Link
                            href={shopRoute.index.url()}
                            onClick={() => setOpen(false)}
                            className="mt-1"
                        >
                            <Button className="w-full rounded-full font-semibold">
                                <ShoppingBag className="size-4" />
                                {nav.shop_label}
                            </Button>
                        </Link>
                    </div>
                </nav>
            ) : null}
        </header>
    );
}
