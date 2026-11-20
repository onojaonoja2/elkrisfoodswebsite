import { Link, usePage } from '@inertiajs/react';
import { ArrowRight, Clock } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import { index as healthIndex, show as healthShow } from '@/routes/health';
import { index as recipesIndex } from '@/routes/recipes';
import { index as shopIndex } from '@/routes/shop';
import type { Article, SharedProps } from '@/types';

type HealthContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    intro: string;
    all: string;
    empty: string;
    cta_heading: string;
    cta_body: string;
    shop_cta: string;
    recipes_cta: string;
    fallback_image: string;
};

type ArticleDetailContent = {
    all_articles: string;
    min_read: string;
    more_from: string;
    fallback_image: string;
};

export default function HealthIndex({
    articles,
    categories,
    activeCategory,
}: {
    articles: Article[];
    categories: string[];
    activeCategory?: string | null;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.health as HealthContent;
    const articleCopy = content.article_detail as ArticleDetailContent;

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
                    <p className="mt-3 max-w-2xl text-brand-ink/70">
                        {page.intro}
                    </p>

                    <div className="mt-6 flex flex-wrap gap-2">
                        <Link
                            href={healthIndex.url()}
                            className={`rounded-full border px-4 py-1.5 text-sm font-medium ${
                                !activeCategory
                                    ? 'border-brand-red bg-brand-red text-white'
                                    : 'border-brand-green/40 bg-background hover:border-brand-green'
                            }`}
                        >
                            {page.all}
                        </Link>
                        {categories.map((category) => (
                            <Link
                                key={category}
                                href={healthIndex.url({ query: { category } })}
                                className={`rounded-full border px-4 py-1.5 text-sm font-medium ${
                                    activeCategory === category
                                        ? 'border-brand-red bg-brand-red text-white'
                                        : 'border-brand-green/40 bg-background hover:border-brand-green'
                                }`}
                            >
                                {category}
                            </Link>
                        ))}
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {articles.length > 0 ? (
                    <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        {articles.map((article) => (
                            <Card
                                key={article.id}
                                className="group gap-0 overflow-hidden py-0"
                            >
                                <Link
                                    href={healthShow.url({
                                        article: article.slug,
                                    })}
                                >
                                    <img
                                        src={
                                            article.cover_path ??
                                            page.fallback_image
                                        }
                                        alt={article.title}
                                        className="aspect-video w-full object-cover transition-transform group-hover:scale-105"
                                        loading="lazy"
                                    />
                                </Link>
                                <CardContent className="space-y-2 p-5">
                                    <Badge
                                        variant="secondary"
                                        className="rounded-full bg-brand-red/10 text-brand-red"
                                    >
                                        {article.category}
                                    </Badge>
                                    <Link
                                        href={healthShow.url({
                                            article: article.slug,
                                        })}
                                        className="block font-semibold hover:text-brand-red"
                                    >
                                        {article.title}
                                    </Link>
                                    {article.excerpt ? (
                                        <p className="line-clamp-3 text-sm text-muted-foreground">
                                            {article.excerpt}
                                        </p>
                                    ) : null}
                                    <p className="flex items-center gap-3 text-xs text-muted-foreground">
                                        <span className="flex items-center gap-1">
                                            <Clock className="size-3.5" />{' '}
                                            {article.reading_minutes}{' '}
                                            {articleCopy.min_read}
                                        </span>
                                        {article.published_at ? (
                                            <span>
                                                {new Date(
                                                    article.published_at,
                                                ).toLocaleDateString('en-NG', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric',
                                                })}
                                            </span>
                                        ) : null}
                                    </p>
                                </CardContent>
                            </Card>
                        ))}
                    </div>
                ) : (
                    <div className="rounded-2xl border bg-card p-12 text-center text-muted-foreground">
                        {page.empty}
                    </div>
                )}

                <div className="mt-10 rounded-2xl border border-brand-green/30 bg-brand-green/5 p-6 text-center">
                    <p className="font-semibold text-brand-ink">
                        {page.cta_heading}
                    </p>
                    <p className="mt-1 text-sm text-brand-ink/70">
                        {page.cta_body}
                    </p>
                    <div className="mt-4 flex flex-wrap justify-center gap-3">
                        <Link
                            href={shopIndex.url()}
                            className="inline-flex items-center gap-2 rounded-full bg-brand-green px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-green/90"
                        >
                            {page.shop_cta} <ArrowRight className="size-4" />
                        </Link>
                        <Link
                            href={recipesIndex.url()}
                            className="inline-flex items-center gap-2 rounded-full border border-brand-green/40 px-5 py-2.5 text-sm font-semibold text-brand-green hover:bg-brand-green/10"
                        >
                            {page.recipes_cta}
                        </Link>
                    </div>
                </div>
            </section>
        </SiteLayout>
    );
}
