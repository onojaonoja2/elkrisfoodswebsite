import { Link, usePage } from '@inertiajs/react';
import { Play } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import SiteLayout from '@/layouts/site-layout';
import { index as galleryIndex } from '@/routes/gallery';
import type { GalleryItem, SharedProps } from '@/types';

type GalleryContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    intro: string;
    intro_count: string;
    all: string;
    empty: string;
};

export default function GalleryIndex({
    items,
    categories,
    videoCount,
    activeCategory,
}: {
    items: GalleryItem[];
    categories: string[];
    videoCount: number;
    activeCategory?: string | null;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.gallery as GalleryContent;

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
                        {page.intro_count.replace(
                            '{count}',
                            String(videoCount),
                        )}
                    </p>

                    <div className="mt-6 flex flex-wrap gap-2">
                        <Link
                            href={galleryIndex.url()}
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
                                href={galleryIndex.url({ query: { category } })}
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
                {items.length > 0 ? (
                    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        {items.map((item) => (
                            <figure
                                key={item.id}
                                className="group overflow-hidden rounded-2xl border bg-card shadow-sm"
                            >
                                {item.media_type === 'video' ? (
                                    <video
                                        src={item.media_url}
                                        controls
                                        preload="metadata"
                                        className="aspect-video w-full bg-black object-contain"
                                    />
                                ) : (
                                    <Dialog>
                                        <DialogTrigger asChild>
                                            <button
                                                type="button"
                                                className="relative block w-full cursor-zoom-in"
                                            >
                                                <img
                                                    src={item.media_url}
                                                    alt={item.title}
                                                    className="aspect-video w-full object-cover transition-transform group-hover:scale-105"
                                                    loading="lazy"
                                                />
                                                <span className="absolute inset-0 flex items-center justify-center bg-black/0 transition group-hover:bg-black/25">
                                                    <span className="flex size-11 items-center justify-center rounded-full bg-white/90 opacity-0 transition group-hover:opacity-100">
                                                        <Play className="size-5 text-brand-ink" />
                                                    </span>
                                                </span>
                                            </button>
                                        </DialogTrigger>
                                        <DialogContent className="max-w-4xl p-3">
                                            <DialogTitle className="sr-only">
                                                {item.title}
                                            </DialogTitle>
                                            <img
                                                src={item.media_url}
                                                alt={item.title}
                                                className="w-full rounded-lg object-contain"
                                            />
                                        </DialogContent>
                                    </Dialog>
                                )}
                                <figcaption className="space-y-1.5 p-4">
                                    <Badge
                                        variant="secondary"
                                        className="rounded-full bg-brand-green/10 text-brand-green"
                                    >
                                        {item.category}
                                    </Badge>
                                    <p className="font-semibold">
                                        {item.title}
                                    </p>
                                    {item.caption ? (
                                        <p className="text-sm text-muted-foreground">
                                            {item.caption}
                                        </p>
                                    ) : null}
                                </figcaption>
                            </figure>
                        ))}
                    </div>
                ) : (
                    <div className="rounded-2xl border bg-card p-12 text-center text-muted-foreground">
                        {page.empty}
                    </div>
                )}
            </section>
        </SiteLayout>
    );
}
