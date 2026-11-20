import { usePage } from '@inertiajs/react';
import { Clock, MessageCircle, ShoppingBag, Users } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { ShareButtons } from '@/components/site/share-buttons';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import { buildWhatsAppUrl, productOrderMessage } from '@/lib/whatsapp';
import * as productsRoute from '@/routes/products';
import * as recipes from '@/routes/recipes';
import type { Recipe, SharedProps } from '@/types';

type RecipeDetailContent = {
    breadcrumb: string;
    minutes: string;
    serves: string;
    ingredients: string;
    preparation: string;
    tips: string;
    product_used: string;
    buy_product: string;
    order_whatsapp: string;
    more_recipes: string;
    all_recipes: string;
    fallback_image: string;
};

export default function RecipeShow({
    recipe,
    related,
}: {
    recipe: Recipe;
    related: Recipe[];
}) {
    const { settings, content } = usePage<SharedProps>().props;
    const copy = content.recipe_detail as RecipeDetailContent;
    const product = recipe.product;
    const whatsapp = product?.whatsapp || settings?.['contact.whatsapp'];

    const waUrl =
        whatsapp && product
            ? buildWhatsAppUrl(
                  whatsapp,
                  productOrderMessage(
                      content.global.whatsapp.order_message,
                      product.name,
                      1,
                  ),
              )
            : null;

    return (
        <SiteLayout>
            <Seo
                title={recipe.meta_title ?? recipe.title}
                description={
                    recipe.meta_description ?? recipe.summary ?? recipe.title
                }
                image={recipe.og_image ?? recipe.image_path ?? undefined}
            />

            <div className="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
                <nav
                    className="mb-6 text-sm text-muted-foreground"
                    aria-label="Breadcrumb"
                >
                    <a
                        href={recipes.index.url()}
                        className="hover:text-brand-red"
                    >
                        {copy.breadcrumb}
                    </a>
                    <span className="mx-2">/</span>
                    <span className="text-foreground">{recipe.title}</span>
                </nav>

                <img
                    src={recipe.image_path ?? copy.fallback_image}
                    alt={recipe.title}
                    className="aspect-video w-full rounded-3xl object-cover"
                />

                <div className="mt-6 flex flex-wrap items-center gap-3">
                    <Badge
                        variant="secondary"
                        className="rounded-full bg-brand-green/10 text-brand-green"
                    >
                        {recipe.category}
                    </Badge>
                    <span className="flex items-center gap-1 text-sm text-muted-foreground">
                        <Clock className="size-4" /> {recipe.prep_minutes}{' '}
                        {copy.minutes}
                    </span>
                    <span className="flex items-center gap-1 text-sm text-muted-foreground">
                        <Users className="size-4" /> {copy.serves}{' '}
                        {recipe.servings}
                    </span>
                    <span className="text-sm text-muted-foreground">
                        {recipe.difficulty}
                    </span>
                </div>

                <h1 className="mt-3 text-3xl font-extrabold text-brand-ink sm:text-4xl">
                    {recipe.title}
                </h1>
                {recipe.summary ? (
                    <p className="mt-3 text-lg text-muted-foreground">
                        {recipe.summary}
                    </p>
                ) : null}

                <div className="mt-5">
                    <ShareButtons title={recipe.title} text={recipe.summary} />
                </div>

                <div className="mt-8 grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.ingredients}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ul className="list-inside list-disc space-y-2 text-sm">
                                {(recipe.ingredients ?? []).map(
                                    (ingredient) => (
                                        <li key={ingredient}>{ingredient}</li>
                                    ),
                                )}
                            </ul>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.preparation}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ol className="list-inside list-decimal space-y-3 text-sm">
                                {(recipe.steps ?? []).map((step) => (
                                    <li key={step} className="leading-relaxed">
                                        {step}
                                    </li>
                                ))}
                            </ol>
                        </CardContent>
                    </Card>
                </div>

                {recipe.tips ? (
                    <Card className="mt-6 border-brand-gold/50 bg-brand-cream">
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.tips}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm leading-relaxed text-brand-ink/80">
                                {recipe.tips}
                            </p>
                        </CardContent>
                    </Card>
                ) : null}

                {product ? (
                    <Card className="mt-8 border-brand-green bg-brand-green/5">
                        <CardContent className="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-sm font-semibold tracking-wider text-brand-green uppercase">
                                    {copy.product_used}
                                </p>
                                <p className="mt-1 text-lg font-bold">
                                    {product.name}
                                </p>
                            </div>
                            <div className="flex flex-wrap gap-3">
                                <a
                                    href={productsRoute.show.url({
                                        product: product.slug,
                                    })}
                                >
                                    <Button className="rounded-full font-semibold">
                                        <ShoppingBag className="size-4" />
                                        {copy.buy_product} {product.name}
                                    </Button>
                                </a>
                                {waUrl ? (
                                    <a
                                        href={waUrl}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        <Button
                                            variant="outline"
                                            className="rounded-full border-[#25D366] font-semibold text-[#25D366] hover:bg-[#25D366] hover:text-white"
                                        >
                                            <MessageCircle className="size-4" />
                                            {copy.order_whatsapp}
                                        </Button>
                                    </a>
                                ) : null}
                            </div>
                        </CardContent>
                    </Card>
                ) : null}

                {related.length > 0 ? (
                    <section className="mt-12">
                        <div className="mb-5 flex items-center justify-between">
                            <h2 className="text-2xl font-bold text-brand-ink">
                                {copy.more_recipes}
                            </h2>
                            <a
                                href={recipes.index.url()}
                                className="text-sm font-semibold text-brand-red hover:underline"
                            >
                                {copy.all_recipes}
                            </a>
                        </div>
                        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                            {related.map((item) => (
                                <a
                                    key={item.id}
                                    href={recipes.show.url({
                                        recipe: item.slug,
                                    })}
                                    className="group overflow-hidden rounded-2xl border bg-card shadow-sm transition-shadow hover:shadow-md"
                                >
                                    <img
                                        src={
                                            item.image_path ??
                                            copy.fallback_image
                                        }
                                        alt={item.title}
                                        className="aspect-video w-full object-cover"
                                        loading="lazy"
                                    />
                                    <div className="space-y-1.5 p-4">
                                        <Badge
                                            variant="secondary"
                                            className="rounded-full bg-brand-green/10 text-brand-green"
                                        >
                                            {item.category}
                                        </Badge>
                                        <p className="font-semibold group-hover:text-brand-red">
                                            {item.title}
                                        </p>
                                        <p className="flex items-center gap-1 text-xs text-muted-foreground">
                                            <Clock className="size-3.5" />{' '}
                                            {item.prep_minutes} {copy.minutes} ·{' '}
                                            {item.difficulty}
                                        </p>
                                    </div>
                                </a>
                            ))}
                        </div>
                    </section>
                ) : null}
            </div>
        </SiteLayout>
    );
}
