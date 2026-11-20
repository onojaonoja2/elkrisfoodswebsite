import { usePage } from '@inertiajs/react';
import { MessageCircle, Search } from 'lucide-react';
import { useState } from 'react';
import { ProductCard } from '@/components/site/product-card';
import { Seo } from '@/components/site/seo';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import SiteLayout from '@/layouts/site-layout';
import { buildWhatsAppUrl } from '@/lib/whatsapp';
import * as shop from '@/routes/shop';
import type { Category, Product, SharedProps } from '@/types';

export default function ShopIndex({
    products,
    categories,
    filters,
}: {
    products: Product[];
    categories: Category[];
    filters: {
        q?: string | null;
        category?: string | null;
        pack?: string | null;
        sort?: string | null;
    };
}) {
    const { settings, content } = usePage<SharedProps>().props;
    const [q, setQ] = useState(filters.q ?? '');
    const whatsapp = settings?.['contact.whatsapp'];
    const deliveryInfo = settings?.['delivery.info'];
    const waUrl = whatsapp
        ? buildWhatsAppUrl(whatsapp, content.global.whatsapp.general_message)
        : null;

    function applyQuery(next: Record<string, string | undefined>) {
        const query: Record<string, string> = {};

        if (filters.category) {
            query.category = filters.category;
        }

        if (filters.pack) {
            query.pack = filters.pack;
        }

        if (filters.sort) {
            query.sort = filters.sort;
        }

        if (filters.q) {
            query.q = filters.q;
        }

        Object.assign(
            query,
            Object.fromEntries(Object.entries(next).filter(([, v]) => v)),
        );

        window.location.href = shop.index.url({ query });
    }

    return (
        <SiteLayout>
            <Seo
                title="Shop"
                description="Shop Elkris Foods products - search, filter by size and pack type, then order via WhatsApp."
            />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                        Shop
                    </p>
                    <h1 className="mt-1 text-4xl font-extrabold text-brand-ink">
                        Order Elkris products
                    </h1>

                    {waUrl ? (
                        <div className="mt-6 flex flex-wrap items-center gap-4">
                            <a href={waUrl} target="_blank" rel="noreferrer">
                                <Button
                                    size="lg"
                                    className="rounded-full bg-[#25D366] px-8 font-semibold hover:bg-[#1ebe5b]"
                                >
                                    <MessageCircle className="size-4" />
                                    Order on WhatsApp
                                </Button>
                            </a>
                            {deliveryInfo ? (
                                <p className="max-w-xl text-sm text-muted-foreground">
                                    {deliveryInfo}
                                </p>
                            ) : null}
                        </div>
                    ) : null}

                    <form
                        className="mt-6 flex max-w-xl gap-2"
                        onSubmit={(e) => {
                            e.preventDefault();
                            applyQuery({ q });
                        }}
                    >
                        <div className="relative flex-1">
                            <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            <Input
                                value={q}
                                onChange={(e) => setQ(e.target.value)}
                                placeholder="Search oat flour, plantain, swallow…"
                                className="pl-9"
                                aria-label="Search products"
                            />
                        </div>
                        <Button type="submit" className="rounded-full px-6">
                            Search
                        </Button>
                    </form>

                    <div className="mt-5 flex flex-wrap gap-3">
                        <select
                            aria-label="Filter by category"
                            value={filters.category ?? ''}
                            onChange={(e) =>
                                applyQuery({
                                    category: e.target.value || undefined,
                                })
                            }
                            className="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground"
                        >
                            <option value="">All categories</option>
                            {categories.map((category) => (
                                <option key={category.id} value={category.slug}>
                                    {category.name}
                                </option>
                            ))}
                        </select>
                        <select
                            aria-label="Filter by pack"
                            value={filters.pack ?? ''}
                            onChange={(e) =>
                                applyQuery({
                                    pack: e.target.value || undefined,
                                })
                            }
                            className="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground"
                        >
                            <option value="">Single & carton</option>
                            <option value="single">Single pack</option>
                            <option value="carton">Carton</option>
                        </select>
                        <select
                            aria-label="Sort products"
                            value={filters.sort ?? ''}
                            onChange={(e) =>
                                applyQuery({
                                    sort: e.target.value || undefined,
                                })
                            }
                            className="h-9 rounded-md border border-input bg-background px-3 text-sm text-foreground"
                        >
                            <option value="">Sort: Featured</option>
                            <option value="name">Name A-Z</option>
                        </select>
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {products.length > 0 ? (
                    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        {products.map((product) => (
                            <ProductCard key={product.id} product={product} />
                        ))}
                    </div>
                ) : (
                    <div className="rounded-2xl border bg-card p-12 text-center text-muted-foreground">
                        No products match your search.
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
