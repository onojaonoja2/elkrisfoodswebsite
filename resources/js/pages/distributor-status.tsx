import { useForm, usePage } from '@inertiajs/react';
import { SearchCheck } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import SiteLayout from '@/layouts/site-layout';
import { status as distributorStatus } from '@/routes/distributor';
import type { DistributorLead, SharedProps } from '@/types';

type DistributorStatusContent = {
    seo: { title: string; description: string };
    heading: string;
    intro: string;
    card_title: string;
    reference: string;
    submitted: string;
    reference_placeholder: string;
    check: string;
    checking: string;
    not_found: string;
    reference_label: string;
    status_new: string;
    status_approved: string;
    status_rejected: string;
    status_generic: string;
};

export default function DistributorStatus({
    lead,
}: {
    lead: DistributorLead | null;
}) {
    const { content, flash } = usePage<SharedProps>().props;
    const page = content.distributor_status as DistributorStatusContent;
    const labels = content.labels.distributor_statuses;
    const { data, setData, get, processing, errors } = useForm({ ref: '' });

    function submit(e: React.FormEvent) {
        e.preventDefault();

        if (!data.ref.trim()) {
            return;
        }

        get(distributorStatus.url({ query: { ref: data.ref.trim() } }), {
            preserveScroll: true,
        });
    }

    const statusMessage = lead
        ? lead.status === 'approved'
            ? page.status_approved
            : lead.status === 'rejected'
              ? page.status_rejected
              : page.status_new
        : page.status_generic;

    return (
        <SiteLayout>
            <Seo
                title={page.seo.title}
                description={page.seo.description}
                noindex
            />

            <section className="bg-brand-mint">
                <div className="mx-auto max-w-3xl px-4 py-12 text-center sm:px-6 lg:px-8">
                    <SearchCheck className="mx-auto size-9 text-brand-green" />
                    <h1 className="mt-4 text-3xl font-extrabold text-brand-ink">
                        {page.heading}
                    </h1>
                    <p className="mx-auto mt-2 max-w-xl text-muted-foreground">
                        {page.intro}
                    </p>
                </div>
            </section>

            <section className="mx-auto max-w-2xl px-4 py-10 sm:px-6 lg:px-8">
                <Card>
                    <CardHeader>
                        <CardTitle className="text-xl">
                            {page.card_title}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {flash?.success && !lead ? (
                            <Alert className="mb-5 border-brand-green bg-brand-green/10">
                                <AlertDescription className="text-brand-green-dark">
                                    {flash.success}
                                </AlertDescription>
                            </Alert>
                        ) : null}

                        {lead ? (
                            <div className="space-y-4">
                                <div className="flex flex-wrap items-center justify-between gap-3 rounded-2xl border bg-brand-cream p-4">
                                    <div>
                                        <p className="text-xs tracking-wide text-muted-foreground uppercase">
                                            {page.reference}
                                        </p>
                                        <p className="text-lg font-bold">
                                            {lead.reference}
                                        </p>
                                    </div>
                                    <span className="rounded-full bg-brand-green/10 px-4 py-1.5 text-sm font-semibold text-brand-green">
                                        {labels[lead.status] ?? lead.status}
                                    </span>
                                </div>
                                <p className="text-sm leading-relaxed text-brand-ink/80">
                                    {statusMessage}
                                </p>
                                {lead.created_at ? (
                                    <p className="text-xs text-muted-foreground">
                                        {page.submitted}{' '}
                                        {new Date(
                                            lead.created_at,
                                        ).toLocaleDateString('en-NG', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                        })}
                                    </p>
                                ) : null}
                            </div>
                        ) : null}

                        <form
                            onSubmit={submit}
                            className="mt-4 flex flex-wrap items-end gap-3"
                        >
                            <div className="min-w-52 flex-1 space-y-2">
                                <Label htmlFor="ref">
                                    {page.reference_label}
                                </Label>
                                <Input
                                    id="ref"
                                    value={data.ref}
                                    onChange={(e) =>
                                        setData('ref', e.target.value)
                                    }
                                    placeholder={page.reference_placeholder}
                                    required
                                />
                                {errors.ref ? (
                                    <p className="text-sm text-destructive">
                                        {errors.ref}
                                    </p>
                                ) : null}
                            </div>
                            <Button
                                type="submit"
                                disabled={processing}
                                className="rounded-full bg-brand-green px-6 font-semibold hover:bg-brand-green/90"
                            >
                                {processing ? page.checking : page.check}
                            </Button>
                        </form>

                        {!lead && data.ref ? (
                            <p className="mt-4 text-sm text-destructive">
                                {page.not_found}
                            </p>
                        ) : null}
                    </CardContent>
                </Card>
            </section>
        </SiteLayout>
    );
}
