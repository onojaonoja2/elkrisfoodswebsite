import { useForm, usePage } from '@inertiajs/react';
import { Check, Clock, MessageCircle, ShoppingBag, Star } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Seo } from '@/components/site/seo';
import { ShareButtons } from '@/components/site/share-buttons';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { Textarea } from '@/components/ui/textarea';
import SiteLayout from '@/layouts/site-layout';
import { track } from '@/lib/analytics';
import { buildWhatsAppUrl, productOrderMessage } from '@/lib/whatsapp';
import * as productsRoute from '@/routes/products';
import * as recipesRoute from '@/routes/recipes';
import { store as reviewsStore } from '@/routes/reviews';
import type { Product, RatingSummary, Review, SharedProps } from '@/types';

type ProductDetailContent = {
    breadcrumb: string;
    size_pack: string;
    carton: string;
    order_whatsapp: string;
    view_in_shop: string;
    great_for: string;
    nutrition_fallback: string;
    one_product_many: string;
    how_to_use: string;
    perfect_with: string;
    reviews_heading: string;
    no_reviews: string;
    write_review: string;
    review_name: string;
    review_email: string;
    review_rating: string;
    review_body: string;
    review_submit: string;
    review_submitting: string;
    usage_fallback: string;
    image_fallback: string;
    why_love: string;
    delivery: string;
    nutrition_notes: string;
    meal_ideas_with: string;
    all_meal_ideas: string;
    review_note: string;
    review_placeholder: string;
    rating_label: string;
    review_name_required: string;
    review_email_required: string;
    review_body_required: string;
    decrease_quantity: string;
    increase_quantity: string;
};

const useCopy: Record<string, string> = {
    swallow:
        'Make soft, satisfying swallow in about 10 minutes. Add the flour gradually to boiling water, stir until smooth and firm, then serve hot with your favourite soup.',
    breakfast:
        'Whisk into porridge, pancakes or smoothies for a fibre-rich start to the day. Ready in minutes, no fuss.',
    baking: 'Swap part of your wheat flour when baking cookies, muffins and cakes, so you get the Elkris goodness without losing the treat.',
    porridge:
        'Cook gently with milk or water for a warm, comforting bowl. Sweeten to taste and top with fruit or groundnuts.',
};

function Stars({
    rating,
    className = 'size-4',
}: {
    rating: number;
    className?: string;
}) {
    return (
        <span
            className="flex items-center gap-0.5"
            aria-label={`${rating} out of 5 stars`}
        >
            {[1, 2, 3, 4, 5].map((star) => (
                <Star
                    key={star}
                    className={`${className} ${star <= Math.round(rating) ? 'fill-brand-gold text-brand-gold' : 'text-muted-foreground/40'}`}
                />
            ))}
        </span>
    );
}

export default function ProductShow({
    product,
    reviews,
    ratingSummary,
}: {
    product: Product;
    reviews: Review[];
    ratingSummary: RatingSummary | null;
}) {
    const { settings, content } = usePage<SharedProps>().props;
    const copy = content.product_detail as ProductDetailContent;
    const variants = (product.variants ?? []).filter(
        (variant) => variant.is_available,
    );
    const [variantId, setVariantId] = useState(
        variants.find((v) => v.pack_type === 'single')?.id ?? variants[0]?.id,
    );
    const [quantity, setQuantity] = useState(1);
    const [activeImage, setActiveImage] = useState(product.hero_image);
    const [activeUse, setActiveUse] = useState(
        product.mealTypes?.[0]?.slug ?? '',
    );

    useEffect(() => {
        track('view_item', {
            items: [
                {
                    item_id: product.slug,
                    item_name: product.name,
                },
            ],
        });
    }, [product.id, product.name, product.slug]);

    const review = useForm({
        name: '',
        email: '',
        rating: 5,
        body: '',
    });

    const selectedVariant =
        variants.find((variant) => variant.id === variantId) ?? variants[0];
    const activeMeal = product.mealTypes?.find(
        (meal) => meal.slug === activeUse,
    );
    const gallery = [product.hero_image, ...(product.gallery ?? [])].filter(
        Boolean,
    ) as string[];
    const whatsapp = product.whatsapp || settings?.['contact.whatsapp'];
    const deliveryInfo = settings?.['delivery.info'];

    const waUrl = whatsapp
        ? buildWhatsAppUrl(
              whatsapp,
              productOrderMessage(
                  content.global.whatsapp.order_message,
                  product.name,
                  quantity,
                  selectedVariant?.size_label,
              ),
          )
        : null;

    function submitReview(e: React.FormEvent) {
        e.preventDefault();
        review.post(reviewsStore.url({ product: product.slug }), {
            preserveScroll: true,
            onSuccess: () => review.reset(),
        });
    }

    return (
        <SiteLayout>
            <Seo
                title={product.meta_title ?? product.name}
                description={
                    product.meta_description ??
                    product.tagline ??
                    product.description ??
                    product.name
                }
                type="product"
                image={product.og_image ?? product.hero_image ?? undefined}
            />

            <div className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <nav
                    className="mb-6 text-sm text-muted-foreground"
                    aria-label="Breadcrumb"
                >
                    <a
                        href={productsRoute.index.url()}
                        className="hover:text-brand-red"
                    >
                        {copy.breadcrumb}
                    </a>
                    <span className="mx-2">/</span>
                    <span className="text-foreground">{product.name}</span>
                </nav>

                <div className="grid gap-10 lg:grid-cols-2">
                    <div>
                        <div className="overflow-hidden rounded-3xl border bg-brand-cream">
                            <img
                                src={activeImage ?? copy.image_fallback}
                                alt={product.name}
                                className="aspect-square w-full object-cover"
                            />
                        </div>
                        {gallery.length > 1 ? (
                            <div className="mt-3 flex gap-2 overflow-x-auto">
                                {gallery.map((image) => (
                                    <button
                                        key={image}
                                        type="button"
                                        onClick={() => setActiveImage(image)}
                                        className={`size-16 shrink-0 overflow-hidden rounded-lg border-2 ${
                                            activeImage === image
                                                ? 'border-brand-red'
                                                : 'border-transparent'
                                        }`}
                                    >
                                        <img
                                            src={image}
                                            alt=""
                                            className="size-full object-cover"
                                        />
                                    </button>
                                ))}
                            </div>
                        ) : null}
                    </div>

                    <div>
                        {product.category ? (
                            <Badge
                                className="rounded-full bg-brand-green/10 text-brand-green"
                                variant="secondary"
                            >
                                {product.category.name}
                            </Badge>
                        ) : null}
                        <h1 className="mt-3 text-3xl font-extrabold text-brand-ink sm:text-4xl">
                            {product.name}
                        </h1>
                        {product.tagline ? (
                            <p className="mt-2 text-lg text-muted-foreground">
                                {product.tagline}
                            </p>
                        ) : null}

                        {ratingSummary && ratingSummary.count > 0 ? (
                            <div className="mt-3 flex items-center gap-2">
                                <Stars rating={ratingSummary.average} />
                                <span className="text-sm text-muted-foreground">
                                    {ratingSummary.average} ·{' '}
                                    {ratingSummary.count} review
                                    {ratingSummary.count !== 1 ? 's' : ''}
                                </span>
                            </div>
                        ) : null}

                        {variants.length > 0 ? (
                            <div className="mt-6">
                                <p className="text-sm font-semibold">
                                    {copy.size_pack}
                                </p>
                                <div className="mt-2 flex flex-wrap gap-2">
                                    {variants.map((variant) => (
                                        <button
                                            key={variant.id}
                                            type="button"
                                            onClick={() =>
                                                setVariantId(variant.id)
                                            }
                                            className={`rounded-full border px-4 py-2 text-sm font-medium transition-colors ${
                                                variant.id === variantId
                                                    ? 'border-brand-red bg-brand-red text-white'
                                                    : 'border-border hover:border-brand-green'
                                            }`}
                                        >
                                            {variant.size_label}
                                            {variant.pack_type === 'carton'
                                                ? ` · ${copy.carton}`
                                                : ''}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        ) : null}

                        <div className="mt-6 flex items-center gap-3">
                            <div className="flex items-center rounded-full border">
                                <button
                                    type="button"
                                    className="px-4 py-2 text-lg"
                                    onClick={() =>
                                        setQuantity((q) => Math.max(1, q - 1))
                                    }
                                    aria-label={copy.decrease_quantity}
                                >
                                    −
                                </button>
                                <span className="min-w-8 text-center text-sm font-semibold">
                                    {quantity}
                                </span>
                                <button
                                    type="button"
                                    className="px-4 py-2 text-lg"
                                    onClick={() => setQuantity((q) => q + 1)}
                                    aria-label={copy.increase_quantity}
                                >
                                    +
                                </button>
                            </div>
                        </div>

                        <div className="mt-6 flex flex-wrap gap-3">
                            {waUrl ? (
                                <a
                                    href={waUrl}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <Button
                                        size="lg"
                                        className="rounded-full bg-[#25D366] px-8 font-semibold hover:bg-[#1ebe5b]"
                                    >
                                        <MessageCircle className="size-4" />
                                        {copy.order_whatsapp}
                                    </Button>
                                </a>
                            ) : (
                                <Button
                                    size="lg"
                                    className="rounded-full px-8 font-semibold"
                                    disabled
                                >
                                    <ShoppingBag className="size-4" />
                                    {copy.order_whatsapp}
                                </Button>
                            )}
                            <a href="/shop">
                                <Button
                                    size="lg"
                                    variant="outline"
                                    className="rounded-full px-8 font-semibold"
                                >
                                    {copy.view_in_shop}
                                </Button>
                            </a>
                        </div>

                        {product.description ? (
                            <p className="mt-6 leading-relaxed text-brand-ink/80">
                                {product.description}
                            </p>
                        ) : null}

                        {deliveryInfo ? (
                            <div className="mt-6 rounded-2xl border border-brand-green/30 bg-brand-mint p-4">
                                <p className="text-sm font-semibold text-brand-green-dark">
                                    {copy.delivery}
                                </p>
                                <p className="mt-1 text-sm leading-relaxed text-brand-ink/80">
                                    {deliveryInfo}
                                </p>
                            </div>
                        ) : null}
                    </div>
                </div>

                <div className="mt-12 grid gap-6 lg:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.why_love}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ul className="space-y-2">
                                {(product.benefits ?? []).map((benefit) => (
                                    <li
                                        key={benefit}
                                        className="flex gap-2 text-sm"
                                    >
                                        <Check className="mt-0.5 size-4 shrink-0 text-brand-green" />
                                        {benefit}
                                    </li>
                                ))}
                            </ul>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.perfect_with}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            {(product.best_with ?? []).length > 0 ? (
                                <div className="flex flex-wrap gap-2">
                                    {(product.best_with ?? []).map((soup) => (
                                        <Badge
                                            key={soup}
                                            variant="outline"
                                            className="rounded-full"
                                        >
                                            {soup}
                                        </Badge>
                                    ))}
                                </div>
                            ) : (
                                <p className="text-sm text-muted-foreground">
                                    {copy.usage_fallback}
                                </p>
                            )}
                            {product.mealTypes &&
                            product.mealTypes.length > 0 ? (
                                <>
                                    <Separator className="my-4" />
                                    <p className="text-sm font-semibold">
                                        {copy.great_for}
                                    </p>
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        {product.mealTypes.map((meal) => (
                                            <Badge
                                                key={meal.id}
                                                className="rounded-full bg-brand-green/10 text-brand-green"
                                                variant="secondary"
                                            >
                                                {meal.name}
                                            </Badge>
                                        ))}
                                    </div>
                                </>
                            ) : null}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.nutrition_notes}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-muted-foreground">
                                {product.nutrition_notes ??
                                    copy.nutrition_fallback}
                            </p>
                        </CardContent>
                    </Card>
                </div>

                {product.mealTypes && product.mealTypes.length > 0 ? (
                    <section className="mt-12 rounded-3xl border bg-brand-cream p-6 sm:p-8">
                        <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {copy.one_product_many}
                        </p>
                        <h2 className="mt-1 text-2xl font-bold text-brand-ink">
                            {copy.how_to_use} {product.name}
                        </h2>

                        <div
                            className="mt-5 flex flex-wrap gap-2"
                            role="tablist"
                            aria-label="Ways to use this product"
                        >
                            {product.mealTypes.map((meal) => (
                                <button
                                    key={meal.id}
                                    type="button"
                                    role="tab"
                                    aria-selected={activeUse === meal.slug}
                                    onClick={() => setActiveUse(meal.slug)}
                                    className={`rounded-full px-5 py-2 text-sm font-semibold transition-colors ${
                                        activeUse === meal.slug
                                            ? 'bg-brand-red text-white shadow-sm'
                                            : 'border border-brand-green/40 bg-background text-brand-ink hover:border-brand-green'
                                    }`}
                                >
                                    {meal.name}
                                </button>
                            ))}
                        </div>

                        <div className="mt-5">
                            <p className="max-w-3xl leading-relaxed text-brand-ink/80">
                                {activeMeal?.usage_copy ??
                                    useCopy[activeUse] ??
                                    copy.usage_fallback}
                            </p>
                            {(product.best_with ?? []).length > 0 ? (
                                <p className="mt-3 text-sm text-brand-ink/75">
                                    <span className="font-semibold text-brand-green">
                                        {copy.perfect_with}:
                                    </span>{' '}
                                    {(product.best_with ?? []).join(' · ')}
                                </p>
                            ) : null}
                        </div>
                    </section>
                ) : null}

                <div className="mt-8">
                    <ShareButtons title={product.name} text={product.tagline} />
                </div>

                {product.recipes && product.recipes.length > 0 ? (
                    <section className="mt-12">
                        <div className="mb-5 flex items-center justify-between">
                            <h2 className="text-2xl font-bold">
                                {copy.meal_ideas_with} {product.name}
                            </h2>
                            <a
                                href={recipesRoute.index.url()}
                                className="text-sm font-semibold text-brand-red hover:underline"
                            >
                                {copy.all_meal_ideas}
                            </a>
                        </div>
                        <div className="grid gap-5 md:grid-cols-3">
                            {product.recipes.map((recipe) => (
                                <a
                                    key={recipe.id}
                                    href={recipesRoute.show.url({
                                        recipe: recipe.slug,
                                    })}
                                    className="group rounded-2xl border bg-card p-5 shadow-sm transition-shadow hover:shadow-md"
                                >
                                    <p className="font-semibold group-hover:text-brand-red">
                                        {recipe.title}
                                    </p>
                                    <p className="mt-2 flex items-center gap-1 text-xs text-muted-foreground">
                                        <Clock className="size-3.5" />{' '}
                                        {recipe.prep_minutes} mins ·{' '}
                                        {recipe.difficulty}
                                    </p>
                                </a>
                            ))}
                        </div>
                    </section>
                ) : null}

                <section className="mt-12 grid gap-8 lg:grid-cols-2">
                    <div>
                        <h2 className="text-2xl font-bold">
                            {copy.reviews_heading}
                        </h2>
                        {ratingSummary && ratingSummary.count > 0 ? (
                            <div className="mt-3 flex items-center gap-3">
                                <Stars
                                    rating={ratingSummary.average}
                                    className="size-5"
                                />
                                <span className="text-sm text-muted-foreground">
                                    {ratingSummary.average} out of 5 ·{' '}
                                    {ratingSummary.count} review
                                    {ratingSummary.count !== 1 ? 's' : ''}
                                </span>
                            </div>
                        ) : null}

                        {reviews.length > 0 ? (
                            <ul className="mt-6 space-y-5">
                                {reviews.map((entry) => (
                                    <li
                                        key={entry.id}
                                        className="rounded-2xl border bg-card p-5"
                                    >
                                        <div className="flex items-center justify-between gap-3">
                                            <p className="font-semibold">
                                                {entry.name}
                                            </p>
                                            <Stars rating={entry.rating} />
                                        </div>
                                        <p className="mt-2 text-sm leading-relaxed text-brand-ink/80">
                                            {entry.body}
                                        </p>
                                        <p className="mt-2 text-xs text-muted-foreground">
                                            {new Date(
                                                entry.created_at,
                                            ).toLocaleDateString('en-NG', {
                                                year: 'numeric',
                                                month: 'long',
                                                day: 'numeric',
                                            })}
                                        </p>
                                    </li>
                                ))}
                            </ul>
                        ) : (
                            <p className="mt-4 text-sm text-muted-foreground">
                                {copy.no_reviews}
                            </p>
                        )}
                    </div>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-lg">
                                {copy.write_review}
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <form onSubmit={submitReview} className="space-y-4">
                                <div className="space-y-2">
                                    <Label>{copy.review_rating} *</Label>
                                    <div className="flex gap-1">
                                        {[1, 2, 3, 4, 5].map((star) => (
                                            <button
                                                key={star}
                                                type="button"
                                                onClick={() =>
                                                    review.setData(
                                                        'rating',
                                                        star,
                                                    )
                                                }
                                                aria-label={`Rate ${star} star${star !== 1 ? 's' : ''}`}
                                            >
                                                <Star
                                                    className={`size-6 transition-colors ${
                                                        star <=
                                                        review.data.rating
                                                            ? 'fill-brand-gold text-brand-gold'
                                                            : 'text-muted-foreground/40 hover:text-brand-gold'
                                                    }`}
                                                />
                                            </button>
                                        ))}
                                    </div>
                                    {review.errors.rating ? (
                                        <p className="text-sm text-destructive">
                                            {review.errors.rating}
                                        </p>
                                    ) : null}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="review-name">
                                        {copy.review_name} *
                                    </Label>
                                    <Input
                                        id="review-name"
                                        value={review.data.name}
                                        onChange={(e) =>
                                            review.setData(
                                                'name',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    {review.errors.name ? (
                                        <p className="text-sm text-destructive">
                                            {review.errors.name}
                                        </p>
                                    ) : null}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="review-email">
                                        {copy.review_email} *
                                    </Label>
                                    <Input
                                        id="review-email"
                                        type="email"
                                        value={review.data.email}
                                        onChange={(e) =>
                                            review.setData(
                                                'email',
                                                e.target.value,
                                            )
                                        }
                                        required
                                    />
                                    {review.errors.email ? (
                                        <p className="text-sm text-destructive">
                                            {review.errors.email}
                                        </p>
                                    ) : null}
                                </div>
                                <div className="space-y-2">
                                    <Label htmlFor="review-body">
                                        {copy.review_body} *
                                    </Label>
                                    <Textarea
                                        id="review-body"
                                        rows={4}
                                        value={review.data.body}
                                        onChange={(e) =>
                                            review.setData(
                                                'body',
                                                e.target.value,
                                            )
                                        }
                                        placeholder={copy.review_placeholder}
                                        required
                                    />
                                    {review.errors.body ? (
                                        <p className="text-sm text-destructive">
                                            {review.errors.body}
                                        </p>
                                    ) : null}
                                </div>
                                <Button
                                    type="submit"
                                    disabled={review.processing}
                                    className="rounded-full bg-brand-green px-6 font-semibold hover:bg-brand-green/90"
                                >
                                    {review.processing
                                        ? copy.review_submitting
                                        : copy.review_submit}
                                </Button>
                                <p className="text-xs text-muted-foreground">
                                    {copy.review_note}
                                </p>
                            </form>
                        </CardContent>
                    </Card>
                </section>
            </div>
        </SiteLayout>
    );
}
