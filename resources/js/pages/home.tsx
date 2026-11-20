import { Link, usePage } from '@inertiajs/react';
import {
    Clock,
    ShieldCheck,
    Sparkles,
    Truck,
    UtensilsCrossed,
} from 'lucide-react';
import { useState } from 'react';
import {
    Reveal,
    RevealGroup,
    RevealHeading,
    RevealItem,
} from '@/components/motion/reveal';
import { HeroCarousel } from '@/components/site/hero-carousel';
import { NewsletterSignup } from '@/components/site/newsletter-signup';
import { ProductCard } from '@/components/site/product-card';
import { Seo } from '@/components/site/seo';
import { TestimonialsMarquee } from '@/components/site/testimonials-marquee';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import * as galleryRoute from '@/routes/gallery';
import * as productsRoute from '@/routes/products';
import * as recipesRoute from '@/routes/recipes';
import * as shopRoute from '@/routes/shop';
import * as storesRoute from '@/routes/stores';
import type {
    Category,
    Faq,
    GalleryItem,
    MealType,
    Product,
    Recipe,
    Store,
    Testimonial,
} from '@/types';

type HomeProps = {
    featuredProducts: Product[];
    categories: Category[];
    mealTypes: MealType[];
    recipes: Recipe[];
    testimonials: Testimonial[];
    nearbyStores: Store[];
    faqs: Faq[];
    videos: GalleryItem[];
    heroVideo: string;
};

const showcaseImages = [
    '/media/products/swallow/swallow1.webp',
    '/media/products/swallow/swallow2.webp',
    '/media/products/swallow/swallow3.webp',
];

type HomeContent = {
    seo: { title: string; description: string };
    hero: {
        aria_label: string;
        watch_label: string;
        previous_label: string;
        next_label: string;
        slide_label: string;
        slides: {
            eyebrow: string;
            title: string;
            highlight: string;
            body: string;
            body_class: string;
            cta_label: string;
            cta_url: string;
        }[];
    };
    trust_items: string[];
    sections: Record<string, string> & { why_benefits: string[] };
    images: {
        why_primary: string;
        why_secondary: string;
        video_poster: string;
        recipe_fallback: string;
    };
};

export default function Home({
    featuredProducts,
    categories,
    mealTypes,
    recipes,
    testimonials,
    nearbyStores,
    faqs,
    videos,
    heroVideo,
}: HomeProps) {
    const { content } = usePage<{ content: { home: HomeContent } }>().props;
    const home = content.home;
    const sections = home.sections;
    const [activeMeal, setActiveMeal] = useState<string | null>(null);
    const [openFaq, setOpenFaq] = useState<number | null>(null);

    const selectedMeal =
        mealTypes.find((meal) => meal.slug === activeMeal) ?? mealTypes[0];
    const perfectWith = Array.from(
        new Set(
            (selectedMeal?.products ?? []).flatMap(
                (product) => product.best_with ?? [],
            ),
        ),
    );

    const heroSlides = home.hero.slides.map((slide) => ({
        eyebrow: slide.eyebrow,
        title: slide.title,
        highlight: slide.highlight,
        body: slide.body,
        bodyClassName: slide.body_class,
        ctaLabel: slide.cta_label,
        ctaHref: slide.cta_url,
    }));

    return (
        <SiteLayout>
            <Seo title={home.seo.title} description={home.seo.description} />

            {/* 1. Hero — split banner: write-up left, video right */}
            <HeroCarousel
                slides={heroSlides}
                video={heroVideo}
                videoPoster={home.images.video_poster}
                labels={{
                    ariaLabel: home.hero.aria_label,
                    watchLabel: home.hero.watch_label,
                    previousLabel: home.hero.previous_label,
                    nextLabel: home.hero.next_label,
                    slideLabel: home.hero.slide_label,
                }}
            />

            {/* 2. Swallow showcase */}
            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div className="mb-8 flex items-end justify-between gap-4">
                    <div>
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.products_eyebrow}
                        </Reveal>
                        <RevealHeading className="text-3xl font-bold text-brand-ink">
                            {sections.products_heading}
                        </RevealHeading>
                    </div>
                    <Link
                        href={productsRoute.index.url()}
                        className="hidden text-sm font-semibold text-brand-red hover:underline sm:block"
                    >
                        {sections.products_cta}
                    </Link>
                </div>
                <RevealGroup className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    {featuredProducts.map((product, index) => (
                        <RevealItem key={product.id}>
                            <ProductCard
                                product={product}
                                image={
                                    showcaseImages[index] ??
                                    product.gallery?.[0]
                                }
                                imageFit="contain"
                            />
                        </RevealItem>
                    ))}
                </RevealGroup>
                <div className="mt-6 text-center sm:hidden">
                    <Link
                        href={productsRoute.index.url()}
                        className="text-sm font-semibold text-brand-red"
                    >
                        {sections.products_cta}
                    </Link>
                </div>
            </section>

            {/* 3. Trust bar */}
            <section className="border-y bg-brand-mint-strong text-brand-green-dark">
                <RevealGroup className="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-8 gap-y-2 px-4 py-4 text-sm font-semibold sm:px-6">
                    {home.trust_items.map((item) => (
                        <RevealItem
                            key={item}
                            as="span"
                            className="flex items-center gap-2"
                        >
                            <ShieldCheck className="size-4 text-brand-gold" />
                            {item}
                        </RevealItem>
                    ))}
                </RevealGroup>
            </section>

            {/* 4. Quick product categories */}
            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div className="mb-8 text-center">
                    <RevealHeading className="text-3xl font-bold text-brand-ink">
                        {sections.categories_heading}
                    </RevealHeading>
                    <Reveal className="mt-2 text-muted-foreground">
                        {sections.categories_intro}
                    </Reveal>
                </div>
                <RevealGroup className="mx-auto grid max-w-5xl gap-4 sm:grid-cols-3">
                    {categories.map((category) => (
                        <RevealItem key={category.id}>
                            <Link
                                href={productsRoute.index.url({
                                    query: { category: category.slug },
                                })}
                                className="group block h-full rounded-2xl border bg-card p-6 text-center shadow-sm transition-all hover:-translate-y-1 hover:border-brand-green hover:shadow-md"
                            >
                                <div className="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-mint text-brand-green">
                                    <UtensilsCrossed className="size-6" />
                                </div>
                                <h3 className="mt-4 font-semibold group-hover:text-brand-red">
                                    {category.name}
                                </h3>
                                <p className="mt-1 text-sm text-muted-foreground">
                                    {category.description}
                                </p>
                            </Link>
                        </RevealItem>
                    ))}
                </RevealGroup>
            </section>

            {/* 5. Discover Elkris */}
            <section className="bg-brand-sand">
                <div className="mx-auto max-w-7xl px-4 py-14 text-center sm:px-6 lg:px-8">
                    <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                        {sections.discover_eyebrow}
                    </Reveal>
                    <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                        {sections.discover_heading}
                    </RevealHeading>
                    <Reveal
                        delay={0.15}
                        className="mt-8 flex flex-wrap justify-center gap-3"
                    >
                        {mealTypes.map((meal) => (
                            <button
                                key={meal.id}
                                type="button"
                                onClick={() => setActiveMeal(meal.slug)}
                                className={`rounded-full border px-6 py-2.5 text-sm font-semibold transition-colors ${
                                    selectedMeal?.slug === meal.slug
                                        ? 'border-brand-red bg-brand-red text-white'
                                        : 'border-brand-green/40 bg-background text-brand-green hover:border-brand-green'
                                }`}
                            >
                                {meal.name}
                            </button>
                        ))}
                    </Reveal>
                    {selectedMeal ? (
                        <Reveal
                            delay={0.2}
                            className="mx-auto mt-8 max-w-4xl rounded-2xl border border-brand-green/20 bg-background p-6 shadow-sm"
                        >
                            <p className="text-sm font-semibold text-brand-green">
                                {sections.enjoy_as} {selectedMeal.name}
                            </p>
                            <div className="mt-4 grid gap-4 sm:grid-cols-3">
                                {(selectedMeal.products ?? []).map(
                                    (product) => (
                                        <Link
                                            key={product.id}
                                            href={productsRoute.show.url({
                                                product: product.slug,
                                            })}
                                            className="group overflow-hidden rounded-xl border bg-card text-left transition-colors hover:border-brand-green"
                                        >
                                            {product.hero_image ? (
                                                <img
                                                    src={product.hero_image}
                                                    alt={product.name}
                                                    className="aspect-video w-full object-cover transition-transform group-hover:scale-105"
                                                    loading="lazy"
                                                />
                                            ) : null}
                                            <p className="p-3 text-sm font-semibold group-hover:text-brand-red">
                                                {product.name}
                                            </p>
                                        </Link>
                                    ),
                                )}
                            </div>
                            {perfectWith.length > 0 ? (
                                <p className="mt-4 text-sm text-brand-ink/75">
                                    <span className="font-semibold text-brand-green">
                                        {sections.perfect_with}
                                    </span>{' '}
                                    {perfectWith.join(' · ')}
                                </p>
                            ) : null}
                        </Reveal>
                    ) : null}
                </div>
            </section>

            {/* 6. What can you make? */}
            <section className="bg-brand-mint">
                <div className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.meal_builder_eyebrow}
                        </Reveal>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {sections.meal_builder_heading}
                        </RevealHeading>
                        <Reveal className="mt-2 text-brand-ink/75">
                            {sections.meal_builder_intro}
                        </Reveal>
                    </div>
                    <Reveal
                        delay={0.1}
                        className="mt-8 flex flex-wrap justify-center gap-3"
                    >
                        {mealTypes.map((meal) => (
                            <button
                                key={meal.id}
                                type="button"
                                onClick={() => setActiveMeal(meal.slug)}
                                className={`rounded-full px-5 py-2 text-sm font-semibold transition-colors ${
                                    selectedMeal?.slug === meal.slug
                                        ? 'bg-brand-red text-white shadow-sm'
                                        : 'border border-brand-green/30 bg-background text-brand-ink hover:border-brand-green'
                                }`}
                            >
                                {meal.name}
                            </button>
                        ))}
                    </Reveal>
                    {selectedMeal ? (
                        <RevealGroup className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            {(selectedMeal.products ?? []).map((product) => (
                                <RevealItem key={product.id}>
                                    <Link
                                        href={productsRoute.show.url({
                                            product: product.slug,
                                        })}
                                        className="block h-full rounded-2xl border border-brand-green/20 bg-background p-5 shadow-sm transition-colors hover:border-brand-green hover:bg-card"
                                    >
                                        <h3 className="font-semibold text-brand-ink">
                                            {product.name}
                                        </h3>
                                        <p className="mt-1 text-sm font-medium text-brand-green">
                                            {sections.view_product}
                                        </p>
                                    </Link>
                                </RevealItem>
                            ))}
                            {(selectedMeal.products ?? []).length === 0 ? (
                                <p className="text-brand-ink/70">
                                    {sections.meal_builder_empty}
                                </p>
                            ) : null}
                        </RevealGroup>
                    ) : null}
                    {perfectWith.length > 0 ? (
                        <Reveal
                            delay={0.1}
                            className="mt-8 rounded-2xl bg-brand-cream p-6 text-center"
                        >
                            <p className="font-semibold text-brand-green">
                                {sections.perfect_with}
                            </p>
                            <p className="mt-2 text-brand-ink/85">
                                {perfectWith.join(' · ')}
                            </p>
                        </Reveal>
                    ) : null}
                </div>
            </section>

            {/* 7. Why Elkris */}
            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div className="grid items-center gap-10 lg:grid-cols-2">
                    <div>
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.why_eyebrow}
                        </Reveal>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {sections.why_heading}
                        </RevealHeading>
                        <RevealGroup as="ul" className="mt-6 space-y-3">
                            {sections.why_benefits.map((benefit) => (
                                <RevealItem
                                    key={benefit}
                                    as="li"
                                    className="flex gap-3 text-brand-ink/85"
                                >
                                    <span className="mt-1 flex size-5 shrink-0 items-center justify-center rounded-full bg-brand-green text-[10px] text-white">
                                        ✓
                                    </span>
                                    {benefit}
                                </RevealItem>
                            ))}
                        </RevealGroup>
                        <Reveal
                            delay={0.1}
                            className="mt-6 flex gap-4 text-sm text-muted-foreground"
                        >
                            <span className="flex items-center gap-1">
                                <Sparkles className="size-4 text-brand-gold" />{' '}
                                {sections.why_note_one}
                            </span>
                            <span className="flex items-center gap-1">
                                <Truck className="size-4 text-brand-green" />{' '}
                                {sections.why_note_two}
                            </span>
                        </Reveal>
                    </div>
                    <Reveal delay={0.15} className="grid grid-cols-2 gap-4">
                        <img
                            src={home.images.why_primary}
                            alt="Elkris products in the kitchen"
                            className="h-full w-full rounded-2xl object-cover"
                            loading="lazy"
                        />
                        <img
                            src={home.images.why_secondary}
                            alt="Elkris product range"
                            className="h-full w-full rounded-2xl object-cover"
                            loading="lazy"
                        />
                    </Reveal>
                </div>
            </section>

            {/* 8. Meal Ideas */}
            <section className="bg-brand-cream">
                <div className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="mb-8 text-center">
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.meal_ideas_eyebrow}
                        </Reveal>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {sections.meal_ideas_heading}
                        </RevealHeading>
                    </div>
                    <RevealGroup className="grid gap-5 md:grid-cols-3">
                        {recipes.map((recipe) => (
                            <RevealItem key={recipe.id}>
                                <Card className="group h-full gap-0 overflow-hidden py-0">
                                    <Link
                                        href={recipesRoute.show.url({
                                            recipe: recipe.slug,
                                        })}
                                    >
                                        <img
                                            src={
                                                recipe.image_path ??
                                                home.images.recipe_fallback
                                            }
                                            alt={recipe.title}
                                            className="aspect-video w-full object-cover transition-transform group-hover:scale-105"
                                            loading="lazy"
                                        />
                                    </Link>
                                    <CardContent className="space-y-2 p-5">
                                        <Badge
                                            variant="secondary"
                                            className="rounded-full bg-brand-mint text-brand-green"
                                        >
                                            {recipe.category}
                                        </Badge>
                                        <Link
                                            href={recipesRoute.show.url({
                                                recipe: recipe.slug,
                                            })}
                                            className="block leading-snug font-semibold hover:text-brand-red"
                                        >
                                            {recipe.title}
                                        </Link>
                                        <p className="flex items-center gap-3 text-xs text-muted-foreground">
                                            <span className="flex items-center gap-1">
                                                <Clock className="size-3.5" />{' '}
                                                {recipe.prep_minutes} mins
                                            </span>
                                            <span>{recipe.difficulty}</span>
                                        </p>
                                    </CardContent>
                                </Card>
                            </RevealItem>
                        ))}
                    </RevealGroup>
                    <div className="mt-8 text-center">
                        <Link href={recipesRoute.index.url()}>
                            <Button
                                variant="outline"
                                className="rounded-full border-brand-red px-8 font-semibold text-brand-red hover:bg-brand-red hover:text-white"
                            >
                                {sections.meal_ideas_cta}
                            </Button>
                        </Link>
                    </div>
                </div>
            </section>

            {/* 9. Watch & Learn */}
            {videos.length > 0 ? (
                <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="mb-8 text-center">
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.watch_eyebrow}
                        </Reveal>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {sections.watch_heading}
                        </RevealHeading>
                    </div>
                    <RevealGroup className="grid gap-5 md:grid-cols-3">
                        {videos.map((video) => (
                            <RevealItem key={video.id}>
                                <div className="h-full overflow-hidden rounded-2xl border bg-card shadow-sm">
                                    <video
                                        src={video.media_url}
                                        controls
                                        preload="none"
                                        poster={home.images.video_poster}
                                        className="aspect-video w-full bg-black"
                                    />
                                    <div className="space-y-1 p-4">
                                        <p className="text-sm font-semibold">
                                            {video.title}
                                        </p>
                                        {video.caption ? (
                                            <p className="text-xs text-muted-foreground">
                                                {video.caption}
                                            </p>
                                        ) : null}
                                    </div>
                                </div>
                            </RevealItem>
                        ))}
                    </RevealGroup>
                    <div className="mt-8 text-center">
                        <Link href={galleryRoute.index.url()}>
                            <Button
                                variant="outline"
                                className="rounded-full border-brand-red px-8 font-semibold text-brand-red hover:bg-brand-red hover:text-white"
                            >
                                {sections.watch_cta}
                            </Button>
                        </Link>
                    </div>
                </section>
            ) : null}

            {/* 10. Customer stories */}
            <section className="bg-brand-sand">
                <div className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="mb-8 text-center">
                        <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {sections.stories_eyebrow}
                        </Reveal>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {sections.stories_heading}
                        </RevealHeading>
                    </div>
                    <Reveal delay={0.1}>
                        <TestimonialsMarquee testimonials={testimonials} />
                    </Reveal>
                </div>
            </section>

            {/* 11. Find Elkris near you */}
            <section className="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
                <div className="mb-8 text-center">
                    <Reveal className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                        {sections.stores_eyebrow}
                    </Reveal>
                    <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                        {sections.stores_heading}
                    </RevealHeading>
                </div>
                <RevealGroup className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {nearbyStores.map((store) => (
                        <RevealItem key={store.id}>
                            <Card className="h-full gap-2 p-5">
                                <p className="font-semibold">{store.name}</p>
                                <p className="text-sm text-muted-foreground">
                                    {store.address}, {store.city}
                                </p>
                                <p className="text-xs font-medium text-brand-green">
                                    {store.state}
                                </p>
                            </Card>
                        </RevealItem>
                    ))}
                </RevealGroup>
                <div className="mt-8 text-center">
                    <Link href={storesRoute.index.url()}>
                        <Button className="rounded-full px-8 font-semibold">
                            {sections.stores_cta}
                        </Button>
                    </Link>
                </div>
            </section>

            {/* 12. Shop + community + FAQ */}
            <section className="bg-brand-mint-strong">
                <div className="mx-auto grid max-w-7xl gap-10 px-4 py-14 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div>
                        <RevealHeading className="text-3xl font-bold text-brand-ink">
                            {sections.shop_heading}
                        </RevealHeading>
                        <Reveal className="mt-2 text-brand-ink/80">
                            {sections.shop_body}
                        </Reveal>
                        <Reveal delay={0.1} className="mt-6">
                            <Link href={shopRoute.index.url()}>
                                <Button className="rounded-full bg-brand-red px-8 font-semibold hover:bg-brand-red-dark">
                                    {sections.shop_cta}
                                </Button>
                            </Link>
                        </Reveal>
                        <Reveal delay={0.15} className="mt-8">
                            <NewsletterSignup source="home" />
                        </Reveal>
                    </div>
                    <div id="faqs">
                        <RevealHeading className="text-3xl font-bold text-brand-ink">
                            {sections.faq_heading}
                        </RevealHeading>
                        <Reveal
                            delay={0.1}
                            className="mt-4 divide-y divide-border rounded-2xl border border-brand-green/20 bg-background"
                        >
                            {faqs.map((faq) => (
                                <div key={faq.id}>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            setOpenFaq(
                                                openFaq === faq.id
                                                    ? null
                                                    : faq.id,
                                            )
                                        }
                                        className="flex w-full items-center justify-between gap-4 px-5 py-4 text-left text-sm font-semibold text-brand-ink"
                                        aria-expanded={openFaq === faq.id}
                                    >
                                        {faq.question}
                                        <span aria-hidden="true">
                                            {openFaq === faq.id ? '−' : '+'}
                                        </span>
                                    </button>
                                    {openFaq === faq.id ? (
                                        <p className="px-5 pb-4 text-sm text-brand-ink/75">
                                            {faq.answer}
                                        </p>
                                    ) : null}
                                </div>
                            ))}
                        </Reveal>
                    </div>
                </div>
            </section>

            {/* 13 Footer handled by SiteLayout */}
        </SiteLayout>
    );
}
