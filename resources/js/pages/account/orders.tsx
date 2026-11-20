import { Link, usePage } from '@inertiajs/react';
import { PackageOpen } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/site-layout';
import * as accountRoute from '@/routes/account';
import * as shopRoute from '@/routes/shop';
import type { Order, SharedProps } from '@/types';

type AccountContent = {
    seo: { title: string; description: string };
    heading: string;
    intro: string;
    empty: string;
    start_shopping: string;
    item_one: string;
    item_many: string;
};

export default function AccountOrders({ orders }: { orders: Order[] }) {
    const { content } = usePage<SharedProps>().props;
    const page = content.account as AccountContent;
    const statusLabels = content.labels.order_statuses;

    return (
        <SiteLayout>
            <Seo
                title={page.seo.title}
                description={page.seo.description}
                noindex
            />

            <div className="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
                <h1 className="text-3xl font-extrabold text-brand-ink">
                    {page.heading}
                </h1>
                <p className="mt-2 text-muted-foreground">{page.intro}</p>

                {orders.length > 0 ? (
                    <ul className="mt-8 space-y-4">
                        {orders.map((order) => {
                            const itemLabel =
                                order.items.length === 1
                                    ? page.item_one
                                    : page.item_many.replace(
                                          '{count}',
                                          String(order.items.length),
                                      );

                            return (
                                <li key={order.id}>
                                    <Link
                                        href={accountRoute.orders.url({
                                            number: order.number,
                                        })}
                                        className="flex flex-wrap items-center justify-between gap-4 rounded-2xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
                                    >
                                        <div>
                                            <p className="font-bold">
                                                {order.number}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {new Date(
                                                    order.created_at,
                                                ).toLocaleDateString('en-NG', {
                                                    year: 'numeric',
                                                    month: 'long',
                                                    day: 'numeric',
                                                })}
                                                {' · '}
                                                {itemLabel}
                                            </p>
                                        </div>
                                        <span className="rounded-full bg-brand-green/10 px-3 py-1 text-xs font-semibold text-brand-green">
                                            {statusLabels[order.status] ??
                                                order.status}
                                        </span>
                                    </Link>
                                </li>
                            );
                        })}
                    </ul>
                ) : (
                    <div className="mt-10 flex flex-col items-center rounded-3xl border border-dashed p-10 text-center">
                        <PackageOpen className="size-10 text-muted-foreground" />
                        <p className="mt-4 text-sm text-muted-foreground">
                            {page.empty}
                        </p>
                        <Link href={shopRoute.index.url()} className="mt-4">
                            <Button className="rounded-full bg-brand-green px-6 font-semibold hover:bg-brand-green/90">
                                {page.start_shopping}
                            </Button>
                        </Link>
                    </div>
                )}
            </div>
        </SiteLayout>
    );
}
