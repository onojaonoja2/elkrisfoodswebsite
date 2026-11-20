import { useForm, usePage } from '@inertiajs/react';
import { Handshake, SearchCheck } from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import SiteLayout from '@/layouts/site-layout';
import { track } from '@/lib/analytics';
import {
    store as distributorStore,
    status as distributorStatus,
} from '@/routes/distributor';
import type { SharedProps } from '@/types';

type DistributorContent = {
    seo: { title: string; description: string };
    heading: string;
    intro: string;
    track_link: string;
    card_title: string;
    success_fallback: string;
    name: string;
    company: string;
    phone: string;
    email: string;
    state: string;
    lga: string;
    business_type: string;
    monthly_order: string;
    locations: string;
    submit: string;
    submitting: string;
    select_state: string;
    select_type: string;
    select_range: string;
    locations_placeholder: string;
    required_hint: string;
    business_types: Record<string, string>;
    monthly_orders: Record<string, string>;
};

const nigerianStates = [
    'Abia',
    'Adamawa',
    'Akwa Ibom',
    'Anambra',
    'Bauchi',
    'Bayelsa',
    'Benue',
    'Borno',
    'Cross River',
    'Delta',
    'Ebonyi',
    'Edo',
    'Ekiti',
    'Enugu',
    'FCT',
    'Gombe',
    'Imo',
    'Jigawa',
    'Kaduna',
    'Kano',
    'Katsina',
    'Kebbi',
    'Kogi',
    'Kwara',
    'Lagos',
    'Nasarawa',
    'Niger',
    'Ogun',
    'Ondo',
    'Osun',
    'Oyo',
    'Plateau',
    'Rivers',
    'Sokoto',
    'Taraba',
    'Yobe',
    'Zamfara',
];

export default function Distributor() {
    const { content, flash } = usePage<SharedProps>().props;
    const page = content.distributor as DistributorContent;
    const { data, setData, post, processing, errors, recentlySuccessful } =
        useForm({
            name: '',
            company: '',
            phone: '',
            email: '',
            state: '',
            lga: '',
            business_type: '',
            monthly_order: '',
            locations: '',
        });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(distributorStore.url(), {
            onSuccess: () =>
                track('distributor_submit', { source: 'application_form' }),
        });
    }

    return (
        <SiteLayout>
            <Seo title={page.seo.title} description={page.seo.description} />

            <section className="bg-brand-green text-white">
                <div className="mx-auto max-w-4xl px-4 py-14 text-center sm:px-6 lg:px-8">
                    <Handshake className="mx-auto size-10 text-brand-gold" />
                    <h1 className="mt-4 text-4xl font-extrabold">
                        {page.heading}
                    </h1>
                    <p className="mx-auto mt-3 max-w-2xl text-white/85">
                        {page.intro}
                    </p>
                </div>
            </section>

            <section className="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
                <div className="mb-5 flex justify-end">
                    <a
                        href={distributorStatus.url()}
                        className="inline-flex items-center gap-1.5 text-sm font-semibold text-brand-green hover:underline"
                    >
                        <SearchCheck className="size-4" />
                        {page.track_link}
                    </a>
                </div>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-xl">
                            {page.card_title}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {flash?.success || recentlySuccessful ? (
                            <Alert className="mb-5 border-brand-green bg-brand-green/10">
                                <AlertDescription className="text-brand-green-dark">
                                    {flash?.success ?? page.success_fallback}
                                </AlertDescription>
                            </Alert>
                        ) : null}

                        <form
                            onSubmit={submit}
                            className="grid gap-5 sm:grid-cols-2"
                        >
                            <div className="space-y-2">
                                <Label htmlFor="name">
                                    {page.name} {page.required_hint}
                                </Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) =>
                                        setData('name', e.target.value)
                                    }
                                    required
                                />
                                {errors.name ? (
                                    <p className="text-sm text-destructive">
                                        {errors.name}
                                    </p>
                                ) : null}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="company">{page.company}</Label>
                                <Input
                                    id="company"
                                    value={data.company}
                                    onChange={(e) =>
                                        setData('company', e.target.value)
                                    }
                                />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="phone">
                                    {page.phone} {page.required_hint}
                                </Label>
                                <Input
                                    id="phone"
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData('phone', e.target.value)
                                    }
                                    required
                                />
                                {errors.phone ? (
                                    <p className="text-sm text-destructive">
                                        {errors.phone}
                                    </p>
                                ) : null}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="email">
                                    {page.email} {page.required_hint}
                                </Label>
                                <Input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) =>
                                        setData('email', e.target.value)
                                    }
                                    required
                                />
                                {errors.email ? (
                                    <p className="text-sm text-destructive">
                                        {errors.email}
                                    </p>
                                ) : null}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="state">
                                    {page.state} {page.required_hint}
                                </Label>
                                <select
                                    id="state"
                                    value={data.state}
                                    onChange={(e) =>
                                        setData('state', e.target.value)
                                    }
                                    required
                                    className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base text-foreground shadow-xs md:text-sm"
                                >
                                    <option value="">
                                        {page.select_state}
                                    </option>
                                    {nigerianStates.map((state) => (
                                        <option key={state} value={state}>
                                            {state}
                                        </option>
                                    ))}
                                </select>
                                {errors.state ? (
                                    <p className="text-sm text-destructive">
                                        {errors.state}
                                    </p>
                                ) : null}
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="lga">{page.lga}</Label>
                                <Input
                                    id="lga"
                                    value={data.lga}
                                    onChange={(e) =>
                                        setData('lga', e.target.value)
                                    }
                                />
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="business_type">
                                    {page.business_type}
                                </Label>
                                <select
                                    id="business_type"
                                    value={data.business_type}
                                    onChange={(e) =>
                                        setData('business_type', e.target.value)
                                    }
                                    className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base text-foreground shadow-xs md:text-sm"
                                >
                                    <option value="">{page.select_type}</option>
                                    {Object.entries(page.business_types).map(
                                        ([key, label]) => (
                                            <option key={key} value={label}>
                                                {label}
                                            </option>
                                        ),
                                    )}
                                </select>
                            </div>
                            <div className="space-y-2">
                                <Label htmlFor="monthly_order">
                                    {page.monthly_order}
                                </Label>
                                <select
                                    id="monthly_order"
                                    value={data.monthly_order}
                                    onChange={(e) =>
                                        setData('monthly_order', e.target.value)
                                    }
                                    className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-base text-foreground shadow-xs md:text-sm"
                                >
                                    <option value="">
                                        {page.select_range}
                                    </option>
                                    {Object.entries(page.monthly_orders).map(
                                        ([key, label]) => (
                                            <option key={key} value={label}>
                                                {label}
                                            </option>
                                        ),
                                    )}
                                </select>
                            </div>
                            <div className="space-y-2 sm:col-span-2">
                                <Label htmlFor="locations">
                                    {page.locations}
                                </Label>
                                <Textarea
                                    id="locations"
                                    value={data.locations}
                                    onChange={(e) =>
                                        setData('locations', e.target.value)
                                    }
                                    rows={3}
                                    placeholder={page.locations_placeholder}
                                />
                            </div>
                            <div className="sm:col-span-2">
                                <Button
                                    type="submit"
                                    disabled={processing}
                                    className="rounded-full px-8 font-semibold"
                                >
                                    {processing ? page.submitting : page.submit}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </section>
        </SiteLayout>
    );
}
