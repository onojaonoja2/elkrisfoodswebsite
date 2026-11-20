import { Link, usePage } from '@inertiajs/react';
import { ArrowLeft, Clock, User } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { ShareButtons } from '@/components/site/share-buttons';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import { index as healthIndex, show as healthShow } from '@/routes/health';
import type { Article, SharedProps } from '@/types';

type ArticleDetailContent = {
    all_articles: string;
    min_read: string;
    more_from: string;
    fallback_image: string;
};

export default function HealthShow({
    article,
    related,
}: {
    article: Article;
    related: Article[];
}) {
    const { content } = usePage<SharedProps>().props;
    const copy = content.article_detail as ArticleDetailContent;
    const description =
        article.meta_description ?? article.excerpt ?? undefined;

    return (
        <SiteLayout>
            <Seo
                title={article.meta_title ?? article.title}
                description={description}
                type="article"
                image={article.og_image ?? article.cover_path ?? undefined}
            />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
                    <Link
                        href={healthIndex.url()}
                        className="inline-flex items-center gap-1.5 text-sm font-medium text-brand-green hover:text-brand-red"
                    >
                        <ArrowLeft className="size-4" /> {copy.all_articles}
                    </Link>

                    <div className="mt-5 flex flex-wrap items-center gap-3">
                        <Badge
                            variant="secondary"
                            className="rounded-full bg-brand-red/10 text-brand-red"
                        >
                            {article.category}
                        </Badge>
                        <span className="flex items-center gap-1 text-xs text-muted-foreground">
                            <Clock className="size-3.5" />{' '}
                            {article.reading_minutes} {copy.min_read}
                        </span>
                        {article.author ? (
                            <span className="flex items-center gap-1 text-xs text-muted-foreground">
                                <User className="size-3.5" /> {article.author}
                            </span>
                        ) : null}
                        {article.published_at ? (
                            <span className="text-xs text-muted-foreground">
                                {new Date(
                                    article.published_at,
                                ).toLocaleDateString('en-NG', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric',
                                })}
                            </span>
                        ) : null}
                    </div>

                    <h1 className="mt-3 text-3xl font-extrabold text-brand-ink sm:text-4xl">
                        {article.title}
                    </h1>
                </div>
            </section>

            <section className="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
                <img
                    src={article.cover_path ?? copy.fallback_image}
                    alt={article.title}
                    className="aspect-video w-full rounded-2xl object-cover"
                />

                <article
                    className="article-body mt-8"
                    dangerouslySetInnerHTML={{
                        __html: article.body_html ?? '',
                    }}
                />

                <div className="mt-8 border-t border-brand-green/15 pt-6">
                    <ShareButtons title={article.title} />
                </div>

                {related.length > 0 ? (
                    <div className="mt-12">
                        <h2 className="text-xl font-bold text-brand-ink">
                            {copy.more_from} {article.category}
                        </h2>
                        <div className="mt-4 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {related.map((item) => (
                                <Card key={item.id} className="gap-0 py-0">
                                    <CardContent className="space-y-2 p-5">
                                        <p className="text-xs tracking-wide text-muted-foreground uppercase">
                                            {item.reading_minutes}{' '}
                                            {copy.min_read}
                                        </p>
                                        <Link
                                            href={healthShow.url({
                                                article: item.slug,
                                            })}
                                            className="block font-semibold hover:text-brand-red"
                                        >
                                            {item.title}
                                        </Link>
                                        {item.excerpt ? (
                                            <p className="line-clamp-2 text-sm text-muted-foreground">
                                                {item.excerpt}
                                            </p>
                                        ) : null}
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    </div>
                ) : null}
            </section>
        </SiteLayout>
    );
}
