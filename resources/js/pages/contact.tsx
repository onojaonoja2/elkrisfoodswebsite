import { Link, useForm, usePage } from '@inertiajs/react';
import {
    Building2,
    Mail,
    MapPin,
    MessageCircle,
    Phone,
    Store,
} from 'lucide-react';
import { Seo } from '@/components/site/seo';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import SiteLayout from '@/layouts/site-layout';
import { buildWhatsAppUrl } from '@/lib/whatsapp';
import { store as contactStore } from '@/routes/contact';
import type { SharedProps } from '@/types';

type ContactContent = {
    seo: { title: string; description: string };
    heading: string;
    intro: string;
    card_email: string;
    card_phone: string;
    card_address: string;
    card_whatsapp: string;
    phone_fallback: string;
    address_fallback: string;
    whatsapp_message: string;
    map_url: string;
    form_name: string;
    form_email: string;
    form_phone: string;
    form_intent: string;
    form_message: string;
    form_submit: string;
    form_sending: string;
    success_fallback: string;
    form_heading: string;
    intent_label: string;
    corporate_office: string;
    whatsapp_cta: string;
    call_cta: string;
    directions_cta: string;
    find_store_cta: string;
    intents: Record<string, string>;
};

export default function Contact({ intents }: { intents: string[] }) {
    const { content, settings, flash } = usePage<SharedProps>().props;
    const page = content.contact as ContactContent;
    const { data, setData, post, processing, errors, recentlySuccessful } =
        useForm({
            intent: intents[0] ?? 'order',
            name: '',
            email: '',
            phone: '',
            message: '',
        });

    const whatsapp = settings?.['contact.whatsapp'];

    function submit(e: React.FormEvent) {
        e.preventDefault();
        post(contactStore.url());
    }

    return (
        <SiteLayout>
            <Seo title={page.seo.title} description={page.seo.description} />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
                    <h1 className="text-4xl font-extrabold text-brand-ink">
                        {page.heading}
                    </h1>
                    <p className="mt-3 max-w-2xl text-brand-ink/75">
                        {page.intro}
                    </p>
                </div>
            </section>

            <section className="mx-auto grid max-w-6xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-5 lg:px-8">
                <div className="space-y-4 lg:col-span-2">
                    <Card className="gap-3 p-6">
                        <div className="flex items-center gap-3">
                            <Phone className="size-5 text-brand-red" />
                            <h2 className="font-semibold">{page.card_phone}</h2>
                        </div>
                        {settings?.['contact.phone'] ? (
                            <a
                                href={`tel:${settings['contact.phone']}`}
                                className="text-sm text-muted-foreground hover:text-brand-red"
                            >
                                {settings['contact.phone']}
                            </a>
                        ) : (
                            <p className="text-sm text-muted-foreground">
                                {page.phone_fallback}
                            </p>
                        )}
                    </Card>

                    <Card className="gap-3 p-6">
                        <div className="flex items-center gap-3">
                            <Mail className="size-5 text-brand-red" />
                            <h2 className="font-semibold">{page.card_email}</h2>
                        </div>
                        {settings?.['contact.email'] ? (
                            <a
                                href={`mailto:${settings['contact.email']}`}
                                className="text-sm text-muted-foreground hover:text-brand-red"
                            >
                                {settings['contact.email']}
                            </a>
                        ) : null}
                    </Card>

                    <Card className="gap-3 p-6">
                        <div className="flex items-center gap-3">
                            <Building2 className="size-5 text-brand-green" />
                            <h2 className="font-semibold">
                                {page.corporate_office}
                            </h2>
                        </div>
                        <p className="flex gap-2 text-sm text-muted-foreground">
                            <MapPin className="mt-0.5 size-4 shrink-0 text-brand-green" />
                            {settings?.['contact.address'] ??
                                page.address_fallback}
                        </p>
                    </Card>

                    <div className="grid gap-3 sm:grid-cols-2">
                        {whatsapp ? (
                            <a
                                href={buildWhatsAppUrl(
                                    whatsapp,
                                    page.whatsapp_message,
                                )}
                                target="_blank"
                                rel="noreferrer"
                            >
                                <Button className="w-full rounded-full bg-[#25D366] font-semibold hover:bg-[#1ebe5b]">
                                    <MessageCircle className="size-4" />
                                    {page.whatsapp_cta}
                                </Button>
                            </a>
                        ) : null}
                        {settings?.['contact.phone'] ? (
                            <a href={`tel:${settings['contact.phone']}`}>
                                <Button
                                    variant="outline"
                                    className="w-full rounded-full font-semibold"
                                >
                                    <Phone className="size-4" />
                                    {page.call_cta}
                                </Button>
                            </a>
                        ) : null}
                        <a href={page.map_url} target="_blank" rel="noreferrer">
                            <Button
                                variant="outline"
                                className="w-full rounded-full font-semibold"
                            >
                                <MapPin className="size-4" />
                                {page.directions_cta}
                            </Button>
                        </a>
                        <Link href="/where-to-buy">
                            <Button
                                variant="outline"
                                className="w-full rounded-full font-semibold"
                            >
                                <Store className="size-4" />
                                {page.find_store_cta}
                            </Button>
                        </Link>
                    </div>
                </div>

                <Card className="lg:col-span-3">
                    <CardHeader>
                        <CardTitle className="text-xl">
                            {page.form_heading}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        {flash?.success || recentlySuccessful ? (
                            <Alert className="mb-4 border-brand-green bg-brand-green/10">
                                <AlertDescription className="text-brand-green-dark">
                                    {flash?.success ?? page.success_fallback}
                                </AlertDescription>
                            </Alert>
                        ) : null}

                        <form onSubmit={submit} className="space-y-5">
                            <div className="space-y-2">
                                <Label htmlFor="intent">
                                    {page.intent_label}
                                </Label>
                                <div className="grid gap-2 sm:grid-cols-2">
                                    {intents.map((intent) => (
                                        <button
                                            key={intent}
                                            type="button"
                                            onClick={() =>
                                                setData('intent', intent)
                                            }
                                            className={`rounded-lg border px-3 py-2 text-left text-sm font-medium transition-colors ${
                                                data.intent === intent
                                                    ? 'border-brand-red bg-brand-red text-white'
                                                    : 'border-border bg-background hover:border-brand-green'
                                            }`}
                                        >
                                            {page.intents[intent] ?? intent}
                                        </button>
                                    ))}
                                </div>
                                {errors.intent ? (
                                    <p className="text-sm text-destructive">
                                        {errors.intent}
                                    </p>
                                ) : null}
                            </div>

                            <div className="grid gap-4 sm:grid-cols-2">
                                <div className="space-y-2">
                                    <Label htmlFor="name">
                                        {page.form_name}
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
                                    <Label htmlFor="email">
                                        {page.form_email}
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
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="phone">{page.form_phone}</Label>
                                <Input
                                    id="phone"
                                    value={data.phone}
                                    onChange={(e) =>
                                        setData('phone', e.target.value)
                                    }
                                />
                                {errors.phone ? (
                                    <p className="text-sm text-destructive">
                                        {errors.phone}
                                    </p>
                                ) : null}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="message">
                                    {page.form_message}
                                </Label>
                                <Textarea
                                    id="message"
                                    value={data.message}
                                    onChange={(e) =>
                                        setData('message', e.target.value)
                                    }
                                    rows={5}
                                    required
                                />
                                {errors.message ? (
                                    <p className="text-sm text-destructive">
                                        {errors.message}
                                    </p>
                                ) : null}
                            </div>

                            <Button
                                type="submit"
                                disabled={processing}
                                className="rounded-full px-8 font-semibold"
                            >
                                {processing
                                    ? page.form_sending
                                    : page.form_submit}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </section>
        </SiteLayout>
    );
}
