import { usePage } from '@inertiajs/react';
import { Clock } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import { index as recipesIndex, show as recipeShow } from '@/routes/recipes';
import type { Recipe, SharedProps } from '@/types';

type RecipesContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    intro: string;
    all: string;
    empty: string;
    fallback_image: string;
};

export default function RecipesIndex({
    recipes: items,
    categories,
    activeCategory,
}: {
    recipes: Recipe[];
    categories: string[];
    activeCategory?: string | null;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.recipes as RecipesContent;

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
                        <a
                            href={recipesIndex.url()}
                            className={`rounded-full border px-4 py-1.5 text-sm font-medium ${
                                !activeCategory
                                    ? 'border-brand-red bg-brand-red text-white'
                                    : 'border-brand-green/40 bg-background hover:border-brand-green'
                            }`}
                        >
                            {page.all}
                        </a>
                        {categories.map((category) => (
                            <a
                                key={category}
                                href={recipesIndex.url({ query: { category } })}
                                className={`rounded-full border px-4 py-1.5 text-sm font-medium ${
                                    activeCategory === category
                                        ? 'border-brand-red bg-brand-red text-white'
                                        : 'border-brand-green/40 bg-background hover:border-brand-green'
                                }`}
                            >
                                {category}
                            </a>
                        ))}
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {items.length > 0 ? (
                    <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                        {items.map((recipe) => (
                            <Card
                                key={recipe.id}
                                className="group gap-0 overflow-hidden py-0"
                            >
                                <a
                                    href={recipeShow.url({
                                        recipe: recipe.slug,
                                    })}
                                >
                                    <img
                                        src={
                                            recipe.image_path ??
                                            page.fallback_image
                                        }
                                        alt={recipe.title}
                                        className="aspect-video w-full object-cover transition-transform group-hover:scale-105"
                                        loading="lazy"
                                    />
                                </a>
                                <CardContent className="space-y-2 p-5">
                                    <Badge
                                        variant="secondary"
                                        className="rounded-full bg-brand-green/10 text-brand-green"
                                    >
                                        {recipe.category}
                                    </Badge>
                                    <a
                                        href={recipeShow.url({
                                            recipe: recipe.slug,
                                        })}
                                        className="block font-semibold hover:text-brand-red"
                                    >
                                        {recipe.title}
                                    </a>
                                    {recipe.summary ? (
                                        <p className="line-clamp-2 text-sm text-muted-foreground">
                                            {recipe.summary}
                                        </p>
                                    ) : null}
                                    <p className="flex items-center gap-3 text-xs text-muted-foreground">
                                        <span className="flex items-center gap-1">
                                            <Clock className="size-3.5" />{' '}
                                            {recipe.prep_minutes} mins
                                        </span>
                                        <span>{recipe.difficulty}</span>
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
            </section>
        </SiteLayout>
    );
}
