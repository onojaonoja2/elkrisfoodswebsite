import { usePage } from '@inertiajs/react';
import { ProductCard } from '@/components/site/product-card';
import { Seo } from '@/components/site/seo';
import SiteLayout from '@/layouts/site-layout';
import { home } from '@/routes';
import * as productsRoute from '@/routes/products';
import type { Category, Product, SharedProps } from '@/types';

type ProductsContent = {
    seo: { title: string; description: string };
    eyebrow: string;
    heading: string;
    intro: string;
    all: string;
    empty: string;
    empty_link: string;
    comparison_eyebrow: string;
    comparison_heading: string;
    table_product: string;
    table_great_for: string;
    table_benefit: string;
    table_sizes: string;
    everyday_cooking: string;
    dash: string;
    view_product: string;
};

export default function ProductsIndex({
    products,
    categories,
    activeCategory,
}: {
    products: Product[];
    categories: Category[];
    activeCategory?: string | null;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.products as ProductsContent;

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
                            href={productsRoute.index.url()}
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
                                key={category.id}
                                href={productsRoute.index.url({
                                    query: { category: category.slug },
                                })}
                                className={`rounded-full border px-4 py-1.5 text-sm font-medium ${
                                    activeCategory === category.slug
                                        ? 'border-brand-red bg-brand-red text-white'
                                        : 'border-brand-green/40 bg-background hover:border-brand-green'
                                }`}
                            >
                                {category.name}
                            </a>
                        ))}
                    </div>
                </div>
            </section>

            <section className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                {products.length > 0 ? (
                    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        {products.map((product) => (
                            <ProductCard key={product.id} product={product} />
                        ))}
                    </div>
                ) : (
                    <div className="rounded-2xl border bg-card p-12 text-center text-muted-foreground">
                        {page.empty}{' '}
                        <a
                            href={home.url()}
                            className="text-brand-red underline"
                        >
                            {page.empty_link}
                        </a>
                    </div>
                )}
            </section>

            {products.length > 1 ? (
                <section className="border-t bg-brand-cream">
                    <div className="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
                        <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {page.comparison_eyebrow}
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-brand-ink">
                            {page.comparison_heading}
                        </h2>

                        <div className="mt-6 overflow-x-auto rounded-2xl border bg-background">
                            <table className="w-full min-w-[720px] text-left text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50 text-xs tracking-wide text-muted-foreground uppercase">
                                        <th className="px-4 py-3 font-semibold">
                                            {page.table_product}
                                        </th>
                                        <th className="px-4 py-3 font-semibold">
                                            {page.table_great_for}
                                        </th>
                                        <th className="px-4 py-3 font-semibold">
                                            {page.table_benefit}
                                        </th>
                                        <th className="px-4 py-3 font-semibold">
                                            {page.table_sizes}
                                        </th>
                                        <th className="px-4 py-3 font-semibold" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {products.map((product) => {
                                        const sizes = (product.variants ?? [])
                                            .filter(
                                                (variant) =>
                                                    variant.is_available,
                                            )
                                            .map(
                                                (variant) => variant.size_label,
                                            )
                                            .filter(
                                                (size, index, list) =>
                                                    list.indexOf(size) ===
                                                    index,
                                            );

                                        return (
                                            <tr
                                                key={product.id}
                                                className="border-b last:border-b-0"
                                            >
                                                <td className="px-4 py-3">
                                                    <a
                                                        href={productsRoute.show.url(
                                                            {
                                                                product:
                                                                    product.slug,
                                                            },
                                                        )}
                                                        className="font-semibold text-brand-ink hover:text-brand-red"
                                                    >
                                                        {product.name}
                                                    </a>
                                                    {product.tagline ? (
                                                        <p className="mt-0.5 text-xs text-muted-foreground">
                                                            {product.tagline}
                                                        </p>
                                                    ) : null}
                                                </td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {(product.mealTypes ?? [])
                                                        .length > 0
                                                        ? (
                                                              product.mealTypes ??
                                                              []
                                                          )
                                                              .map(
                                                                  (meal) =>
                                                                      meal.name,
                                                              )
                                                              .join(', ')
                                                        : page.everyday_cooking}
                                                </td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {(product.benefits ??
                                                        [])[0] ??
                                                        product.description ??
                                                        page.dash}
                                                </td>
                                                <td className="px-4 py-3 text-muted-foreground">
                                                    {sizes.length > 0
                                                        ? sizes.join(', ')
                                                        : page.dash}
                                                </td>
                                                <td className="px-4 py-3 text-right">
                                                    <a
                                                        href={productsRoute.show.url(
                                                            {
                                                                product:
                                                                    product.slug,
                                                            },
                                                        )}
                                                        className="text-xs font-semibold whitespace-nowrap text-brand-red hover:underline"
                                                    >
                                                        {page.view_product}
                                                    </a>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            ) : null}
        </SiteLayout>
    );
}
