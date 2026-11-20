import { usePage } from '@inertiajs/react';
import { Clock, MapPin, Navigation, Phone } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import { track } from '@/lib/analytics';
import * as stores from '@/routes/stores';
import type { SharedProps, Store } from '@/types';

type StoresContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    location_label: string;
    city_label: string;
    all_states: string;
    all_cities: string;
    find_near_me: string;
    store_count_one: string;
    store_count_many: string;
    directions: string;
    call: string;
    view_map: string;
    empty: string;
    clear: string;
};

export default function StoreLocator({
    stores: items,
    states,
    cities,
    filters,
}: {
    stores: Store[];
    states: string[];
    cities: string[];
    filters: { state?: string | null; city?: string | null };
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.stores as StoresContent;

    function applyFilter(next: { state?: string; city?: string }) {
        const query: Record<string, string> = {};
        const state = 'state' in next ? next.state : filters.state;
        const city = 'city' in next ? next.city : filters.city;

        if (state) {
            query.state = state;
        }

        if (city) {
            query.city = city;
        }

        window.location.href = stores.index.url({ query });
    }

    const hasLocationApi =
        typeof navigator !== 'undefined' && 'geolocation' in navigator;

    function findNearMe() {
        navigator.geolocation.getCurrentPosition((position) => {
            const { latitude, longitude } = position.coords;
            const withCoords = items
                .filter(
                    (store) =>
                        store.latitude !== null && store.longitude !== null,
                )
                .map((store) => ({
                    store,
                    distance:
                        Math.pow((store.latitude as number) - latitude, 2) +
                        Math.pow((store.longitude as number) - longitude, 2),
                }))
                .sort((a, b) => a.distance - b.distance);

            if (withCoords[0]) {
                const el = document.getElementById(
                    `store-${withCoords[0].store.id}`,
                );
                el?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

    const countLabel =
        items.length === 1
            ? page.store_count_one
            : page.store_count_many.replace('{count}', String(items.length));

    return (
        <SiteLayout>
            <Seo title={page.seo.title} description={page.seo.description} />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                    <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                        {page.eyebrow}
                    </p>
                    <h1 className="mt-1 text-4xl font-extrabold text-brand-ink">
                        {page.heading}
                    </h1>

                    <div className="mt-6 flex flex-wrap items-end gap-3">
                        <label className="flex flex-col gap-1 text-sm font-medium">
                            {page.location_label}
                            <select
                                value={filters.state ?? ''}
                                onChange={(e) =>
                                    applyFilter({
                                        state: e.target.value,
                                        city: '',
                                    })
                                }
                                className="h-10 min-w-48 rounded-md border border-input bg-background px-3 text-base text-foreground"
                            >
                                <option value="">{page.all_states}</option>
                                {states.map((state) => (
                                    <option key={state} value={state}>
                                        {state}
                                    </option>
                                ))}
                            </select>
                        </label>
                        <label className="flex flex-col gap-1 text-sm font-medium">
                            {page.city_label}
                            <select
                                value={filters.city ?? ''}
                                onChange={(e) =>
                                    applyFilter({ city: e.target.value })
                                }
                                className="min-40 h-10 rounded-md border border-input bg-background px-3 text-base text-foreground"
                                disabled={!filters.state && cities.length === 0}
                            >
                                <option value="">{page.all_cities}</option>
                                {cities.map((city) => (
                                    <option key={city} value={city}>
                                        {city}
                                    </option>
                                ))}
                            </select>
                        </label>
                        {hasLocationApi ? (
                            <Button
                                type="button"
                                variant="outline"
                                onClick={findNearMe}
                                className="rounded-full"
                            >
                                <Navigation className="size-4" />
                                {page.find_near_me}
                            </Button>
                        ) : null}
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-6 flex items-center justify-between">
                    <h2 className="text-xl font-bold">{countLabel}</h2>
                </div>

                {items.length > 0 ? (
                    <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        {items.map((store) => (
                            <Card
                                key={store.id}
                                id={`store-${store.id}`}
                                className="gap-3 p-5"
                            >
                                <CardContent className="p-0">
                                    <p className="font-bold text-brand-ink">
                                        {store.name}
                                    </p>
                                    <p className="mt-1 flex gap-2 text-sm text-muted-foreground">
                                        <MapPin className="mt-0.5 size-4 shrink-0 text-brand-green" />
                                        {store.address}, {store.city},{' '}
                                        {store.state}
                                    </p>
                                    {store.opening_hours ? (
                                        <p className="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                                            <Clock className="size-4 shrink-0" />
                                            {store.opening_hours}
                                        </p>
                                    ) : null}
                                    {store.phone ? (
                                        <p className="mt-2 flex items-center gap-2 text-sm text-muted-foreground">
                                            <Phone className="size-4 shrink-0" />
                                            {store.phone}
                                        </p>
                                    ) : null}

                                    <div className="mt-4 flex flex-wrap gap-2">
                                        <a
                                            href={store.directions_url}
                                            target="_blank"
                                            rel="noreferrer"
                                            onClick={() =>
                                                track('store_locator_click', {
                                                    store_name: store.name,
                                                    action: 'directions',
                                                })
                                            }
                                        >
                                            <Button
                                                size="sm"
                                                className="rounded-full font-semibold"
                                            >
                                                {page.directions}
                                            </Button>
                                        </a>
                                        {store.phone ? (
                                            <a href={`tel:${store.phone}`}>
                                                <Button
                                                    size="sm"
                                                    variant="outline"
                                                    className="rounded-full font-semibold"
                                                >
                                                    {page.call}
                                                </Button>
                                            </a>
                                        ) : null}
                                        <a
                                            href={`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
                                                `${store.name} ${store.address} ${store.city}`,
                                            )}`}
                                            target="_blank"
                                            rel="noreferrer"
                                            onClick={() =>
                                                track('store_locator_click', {
                                                    store_name: store.name,
                                                    action: 'view_map',
                                                })
                                            }
                                        >
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                className="rounded-full font-semibold"
                                            >
                                                {page.view_map}
                                            </Button>
                                        </a>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <div className="rounded-2xl border bg-card p-12 text-center text-muted-foreground">
                        {page.empty}{' '}
                        <button
                            type="button"
                            className="text-brand-red underline"
                            onClick={() => applyFilter({ state: '', city: '' })}
                        >
                            {page.clear}
                        </button>
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
