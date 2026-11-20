import { useForm, usePage } from '@inertiajs/react';
import { Check, Mail } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { track } from '@/lib/analytics';
import { store as newsletterStore } from '@/routes/newsletter';
import type { SharedProps } from '@/types';

export function NewsletterSignup({ source = 'home' }: { source?: string }) {
    const { content } = usePage<SharedProps>().props;
    const newsletter = content.global.newsletter;
    const { data, setData, post, processing, errors, wasSuccessful, reset } =
        useForm({
            email: '',
            name: '',
            source,
        });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(newsletterStore.url(), {
            onSuccess: () => {
                track('newsletter_signup', { source });
                reset('email', 'name');
            },
        });
    };

    if (wasSuccessful) {
        return (
            <div className="flex items-start gap-3 rounded-2xl border border-brand-green/40 bg-background p-4">
                <span className="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full bg-brand-green text-white">
                    <Check className="size-4" />
                </span>
                <div>
                    <p className="text-sm font-semibold text-brand-ink">
                        {newsletter.success_heading}
                    </p>
                    <p className="mt-0.5 text-sm text-muted-foreground">
                        {newsletter.success_body}
                    </p>
                </div>
            </div>
        );
    }

    return (
        <form onSubmit={submit} className="space-y-2">
            <label
                htmlFor="newsletter-email"
                className="text-sm font-semibold text-brand-ink"
            >
                {newsletter.heading}
            </label>
            <p className="text-sm text-brand-ink/75">{newsletter.body}</p>
            <div className="mt-2 flex gap-2">
                <div className="relative flex-1">
                    <Mail className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                    <Input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        required
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        placeholder={newsletter.placeholder}
                        className="h-11 rounded-full border-brand-green/40 bg-background pl-9"
                        autoComplete="email"
                    />
                </div>
                <Button
                    type="submit"
                    disabled={processing}
                    className="h-11 shrink-0 rounded-full bg-brand-red px-6 font-semibold hover:bg-brand-red-dark"
                >
                    {processing ? newsletter.processing : newsletter.button}
                </Button>
            </div>
            {errors.email ? (
                <p className="text-sm text-destructive">{errors.email}</p>
            ) : null}
        </form>
    );
}
