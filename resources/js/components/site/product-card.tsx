import { Link, usePage } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter } from '@/components/ui/card';
import * as productsRoute from '@/routes/products';
import type { Product, SharedProps } from '@/types';

export function ProductCard({
    product,
    image,
    imageFit = 'cover',
}: {
    product: Product;
    image?: string | null;
    imageFit?: 'cover' | 'contain';
}) {
    const { content } = usePage<SharedProps>().props;
    const labels = content.global.product_card;
    const imageSrc = image ?? product.hero_image;

    return (
        <Card className="group h-full gap-0 overflow-hidden py-0 transition-shadow hover:shadow-md">
            <Link
                href={productsRoute.show.url({ product: product.slug })}
                className="relative block aspect-square overflow-hidden bg-brand-cream"
            >
                {imageSrc ? (
                    imageFit === 'contain' ? (
                        <>
                            <div
                                aria-hidden="true"
                                className="absolute inset-0 scale-110 bg-cover bg-center opacity-40 blur-2xl"
                                style={{
                                    backgroundImage: `url(${imageSrc})`,
                                }}
                            />
                            <img
                                src={imageSrc}
                                alt={product.name}
                                className="relative z-10 h-full w-full object-contain"
                                loading="lazy"
                                decoding="async"
                            />
                        </>
                    ) : (
                        <img
                            src={imageSrc}
                            alt={product.name}
                            className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                            decoding="async"
                        />
                    )
                ) : (
                    <div className="flex h-full w-full items-center justify-center text-muted-foreground">
                        {labels.no_image}
                    </div>
                )}
            </Link>
            <CardContent className="flex flex-col gap-1 px-4 pt-4">
                <p className="text-xs font-semibold tracking-wide text-brand-green uppercase">
                    {product.category?.name}
                </p>
                <Link
                    href={productsRoute.show.url({ product: product.slug })}
                    className="text-base leading-tight font-semibold hover:text-brand-red"
                >
                    {product.name}
                </Link>
                {product.tagline ? (
                    <p className="line-clamp-2 text-sm text-muted-foreground">
                        {product.tagline}
                    </p>
                ) : null}
            </CardContent>
            <CardFooter className="px-4 pt-3 pb-4">
                <Link
                    href={productsRoute.show.url({ product: product.slug })}
                    className="w-full"
                >
                    <Button variant="outline" className="w-full rounded-full">
                        {labels.view}
                    </Button>
                </Link>
            </CardFooter>
        </Card>
    );
}
