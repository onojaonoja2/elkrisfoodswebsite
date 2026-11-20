import { Link, usePage } from '@inertiajs/react';
import {
    HeartPulse,
    MapPin,
    Package,
    Search,
    UtensilsCrossed,
} from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import SiteLayout from '@/layouts/site-layout';
import { index as searchIndex } from '@/routes/search';
import type { SearchResult, SearchType, SharedProps } from '@/types';

type SearchContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    placeholder: string;
    submit: string;
    min_chars: string;
    result_one: string;
    result_many: string;
    no_results: string;
    type_product: string;
    type_recipe: string;
    type_article: string;
    type_store: string;
};

export default function SearchIndex({
    q,
    results,
    total,
}: {
    q: string;
    results: SearchResult[];
    total: number;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.search as SearchContent;
    const hasQuery = q.length >= 2;

    const typeMeta: Record<
        SearchType,
        { label: string; icon: React.ComponentType<{ className?: string }> }
    > = {
        product: { label: page.type_product, icon: Package },
        recipe: { label: page.type_recipe, icon: UtensilsCrossed },
        article: { label: page.type_article, icon: HeartPulse },
        store: { label: page.type_store, icon: MapPin },
    };

    const countLabel =
        total === 1
            ? page.result_one
            : page.result_many.replace('{count}', String(total));

    return (
        <SiteLayout>
            <Seo
                title={q ? `Search "${q}"` : page.seo.title}
                description={page.seo.description}
                noindex
            />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                    <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                        {page.eyebrow}
                    </p>
                    <h1 className="mt-1 text-4xl font-extrabold text-brand-ink">
                        {page.heading}
                    </h1>

                    <form
                        action={searchIndex.form.get().action}
                        method="get"
                        className="mt-6 flex gap-2"
                    >
                        <Input
                            type="search"
                            name="q"
                            defaultValue={q}
                            placeholder={page.placeholder}
                            className="h-12 flex-1 rounded-full border-brand-green/40 bg-background px-5"
                            autoComplete="off"
                        />
                        <Button
                            type="submit"
                            className="h-12 rounded-full bg-brand-green px-6 hover:bg-brand-green/90"
                        >
                            <Search className="size-4" /> {page.submit}
                        </Button>
                    </form>
                </div>
            </section>

            <section className="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
                {!hasQuery ? (
                    <div className="rounded-2xl border bg-card p-10 text-center text-muted-foreground">
                        {page.min_chars}
                    </div>
                ) : total > 0 ? (
                    <>
                        <p className="mb-4 text-sm text-muted-foreground">
                            {countLabel} for “{q}”
                        </p>
                        <ul className="space-y-3">
                            {results.map((result, index) => {
                                const meta = typeMeta[result.type];
                                const Icon = meta.icon;

                                return (
                                    <li key={`${result.type}-${index}`}>
                                        <Card className="gap-0 py-0">
                                            <CardContent className="flex items-center gap-4 p-4">
                                                <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-green/10 text-brand-green">
                                                    <Icon className="size-5" />
                                                </span>
                                                <div className="min-w-0 flex-1">
                                                    <Link
                                                        href={result.url}
                                                        className="block truncate font-semibold hover:text-brand-red"
                                                    >
                                                        {result.title}
                                                    </Link>
                                                    {result.subtitle ? (
                                                        <p className="truncate text-sm text-muted-foreground">
                                                            {result.subtitle}
                                                        </p>
                                                    ) : null}
                                                </div>
                                                <Badge
                                                    variant="secondary"
                                                    className="hidden shrink-0 rounded-full bg-brand-red/10 text-brand-red sm:inline-flex"
                                                >
                                                    {meta.label}
                                                </Badge>
                                            </CardContent>
                                        </Card>
                                    </li>
                                );
                            })}
                        </ul>
                    </>
                ) : (
                    <div className="rounded-2xl border bg-card p-10 text-center text-muted-foreground">
                        {page.no_results}
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
