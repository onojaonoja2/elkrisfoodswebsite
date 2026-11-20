import { usePage } from '@inertiajs/react';
import { Check, Copy, MessageCircle } from 'lucide-react';
import { useState } from 'react';
import { Seo } from '@/components/site/seo';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import SiteLayout from '@/layouts/site-layout';
import type { Order, OrderStatus, SharedProps } from '@/types';

type OrdersContent = {
    seo: { title: string; description: string };
    your_order: string;
    tracking: string;
    copy_order_number: string;
    copied: string;
    copy: string;
    cancelled: string;
    summary: string;
    delivery_note: string;
    delivery_note_placed: string;
    whatsapp_cta: string;
    whatsapp_cta_continue: string;
    whatsapp_cta_message: string;
    whatsapp_unavailable: string;
    final_note: string;
    carton: string;
    quantity_times: string;
};

const steps: OrderStatus[] = ['pending', 'confirmed', 'ready', 'delivered'];

export default function OrderShow({
    order,
    waUrl,
    fromAccount = false,
}: {
    order: Order;
    waUrl: string | null;
    fromAccount?: boolean;
}) {
    const { content } = usePage<SharedProps>().props;
    const page = content.orders as OrdersContent;
    const statusLabels = content.labels.order_statuses;
    const [copied, setCopied] = useState(false);

    const currentStepIndex = steps.indexOf(order.status);
    const cancelled = order.status === 'cancelled';

    const copyNumber = async () => {
        try {
            await navigator.clipboard.writeText(order.number);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch {
            // Clipboard unavailable — number is visible on screen.
        }
    };

    const placedNote = page.delivery_note_placed.replace(
        '{date}',
        new Date(order.created_at).toLocaleDateString('en-NG', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        }),
    );

    return (
        <SiteLayout>
            <Seo
                title={`${page.seo.title} ${order.number}`}
                description={page.seo.description}
                noindex
            />

            <div className="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
                <p className="text-sm text-muted-foreground">
                    {fromAccount ? page.your_order : page.tracking}
                </p>
                <div className="mt-1 flex flex-wrap items-center gap-3">
                    <h1 className="text-3xl font-extrabold text-brand-ink">
                        {order.number}
                    </h1>
                    <button
                        type="button"
                        onClick={copyNumber}
                        className="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-medium text-muted-foreground hover:bg-accent"
                        aria-label={page.copy_order_number}
                    >
                        {copied ? (
                            <Check className="size-3.5 text-brand-green" />
                        ) : (
                            <Copy className="size-3.5" />
                        )}
                        {copied ? page.copied : page.copy}
                    </button>
                </div>

                <div className="mt-4">
                    <span className="inline-flex items-center rounded-full bg-brand-green/10 px-3 py-1 text-sm font-semibold text-brand-green">
                        {statusLabels[order.status] ?? order.status}
                    </span>
                </div>

                {cancelled ? (
                    <Alert className="mt-6 border-destructive/40 bg-destructive/10">
                        <AlertDescription className="text-destructive">
                            {page.cancelled}
                        </AlertDescription>
                    </Alert>
                ) : (
                    <ol className="mt-8 grid grid-cols-4 gap-2">
                        {steps.map((step, index) => {
                            const done = index <= currentStepIndex;

                            return (
                                <li key={step} className="text-center">
                                    <div
                                        className={`mx-auto flex size-9 items-center justify-center rounded-full border-2 ${
                                            done
                                                ? 'border-brand-green bg-brand-green text-white'
                                                : 'border-border bg-background text-muted-foreground'
                                        }`}
                                    >
                                        {done ? (
                                            <Check className="size-4" />
                                        ) : (
                                            <span className="text-xs font-bold">
                                                {index + 1}
                                            </span>
                                        )}
                                    </div>
                                    <p
                                        className={`mt-2 text-xs font-medium ${done ? 'text-brand-green' : 'text-muted-foreground'}`}
                                    >
                                        {statusLabels[step] ?? step}
                                    </p>
                                </li>
                            );
                        })}
                    </ol>
                )}

                <div className="mt-8 rounded-2xl border bg-card p-6">
                    <h2 className="font-bold">{page.summary}</h2>
                    <ul className="mt-4 space-y-3">
                        {order.items.map((item) => {
                            const quantityLabel = page.quantity_times
                                .replace('{quantity}', String(item.quantity))
                                .replace('{size}', item.size_label);

                            return (
                                <li
                                    key={item.id}
                                    className="flex items-start justify-between gap-4 text-sm"
                                >
                                    <div>
                                        <p className="font-medium">
                                            {item.product_name}
                                        </p>
                                        <p className="text-muted-foreground">
                                            {quantityLabel}
                                            {item.pack_type === 'carton'
                                                ? ` · ${page.carton}`
                                                : ''}
                                        </p>
                                    </div>
                                </li>
                            );
                        })}
                    </ul>
                    <p className="mt-3 text-xs text-muted-foreground">
                        {placedNote}
                        {' · '}
                        <span className="font-medium text-foreground">
                            {order.name}
                        </span>
                        {' · '}
                        <span className="font-medium text-foreground">
                            {order.phone}
                        </span>
                    </p>
                </div>

                <div className="mt-6 flex flex-wrap gap-3">
                    {waUrl ? (
                        <a href={waUrl} target="_blank" rel="noreferrer">
                            <Button
                                size="lg"
                                className="rounded-full bg-[#25D366] px-8 font-semibold hover:bg-[#1ebe5b]"
                            >
                                <MessageCircle className="size-4" />
                                {page.whatsapp_cta}
                            </Button>
                        </a>
                    ) : (
                        <Button
                            size="lg"
                            className="rounded-full px-8 font-semibold"
                            disabled
                        >
                            <MessageCircle className="size-4" />
                            {page.whatsapp_unavailable}
                        </Button>
                    )}
                </div>
                <p className="mt-3 text-sm text-muted-foreground">
                    {page.final_note}
                </p>
            </div>
        </SiteLayout>
    );
}
