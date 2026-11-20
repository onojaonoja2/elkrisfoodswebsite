import { usePage } from '@inertiajs/react';
import {
    Compass,
    Eye,
    Globe,
    Heart,
    HeartHandshake,
    Leaf,
    Lightbulb,
    RefreshCw,
    ShieldCheck,
    Sparkles,
    Star,
    Target,
    TrendingUp,
    Users,
} from 'lucide-react';
import {
    Reveal,
    RevealGroup,
    RevealHeading,
    RevealItem,
} from '@/components/motion/reveal';
import { Seo } from '@/components/site/seo';
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import SiteLayout from '@/layouts/site-layout';
import type { AboutContent, SharedProps } from '@/types';

const iconMap: Record<string, typeof Users> = {
    Compass,
    Globe,
    Heart,
    HeartHandshake,
    Leaf,
    Lightbulb,
    RefreshCw,
    ShieldCheck,
    Sparkles,
    Star,
    TrendingUp,
    Users,
};

type AboutPageContent = {
    seo: { title: string; description: string };
    badge: string;
    heading: string;
    vision_heading: string;
    mission_heading: string;
    values_eyebrow: string;
    values_heading: string;
};

export default function About({ about }: { about: AboutContent }) {
    const { content } = usePage<SharedProps>().props;
    const page = content.about as AboutPageContent;

    return (
        <SiteLayout>
            <Seo title={page.seo.title} description={page.seo.description} />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-4xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <Badge className="rounded-full bg-brand-green px-3 py-1 text-white">
                            {page.badge}
                        </Badge>
                        <RevealHeading
                            as="h1"
                            className="mt-4 text-4xl font-extrabold text-brand-ink sm:text-5xl"
                        >
                            {page.heading}
                        </RevealHeading>
                    </div>
                    <Reveal className="mt-8 space-y-5 text-base leading-relaxed text-brand-ink/75 sm:text-lg">
                        {about.who_we_are.map((paragraph) => (
                            <p key={paragraph}>{paragraph}</p>
                        ))}
                    </Reveal>
                </div>
            </section>

            <section className="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                <RevealGroup className="grid gap-6 md:grid-cols-2">
                    <RevealItem>
                        <Card className="h-full gap-3 p-6">
                            <Eye className="size-8 text-brand-green" />
                            <h2 className="text-xl font-bold">
                                {page.vision_heading}
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                {about.vision}
                            </p>
                        </Card>
                    </RevealItem>
                    <RevealItem>
                        <Card className="h-full gap-3 p-6">
                            <Target className="size-8 text-brand-red" />
                            <h2 className="text-xl font-bold">
                                {page.mission_heading}
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                {about.mission}
                            </p>
                        </Card>
                    </RevealItem>
                </RevealGroup>
            </section>

            <section className="bg-brand-sand/60">
                <div className="mx-auto max-w-6xl px-4 py-14 sm:px-6 lg:px-8">
                    <div className="text-center">
                        <p className="text-sm font-semibold tracking-widest text-brand-green uppercase">
                            {page.values_eyebrow}
                        </p>
                        <RevealHeading className="mt-2 text-3xl font-bold text-brand-ink">
                            {page.values_heading}
                        </RevealHeading>
                        <p className="mx-auto mt-4 max-w-3xl text-brand-ink/75">
                            {about.values_intro}
                        </p>
                    </div>
                    <RevealGroup className="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {about.core_values.map((value, index) => {
                            const Icon = iconMap[value.icon] ?? Users;

                            return (
                                <RevealItem key={value.title}>
                                    <Card className="h-full gap-3 p-6">
                                        <div className="flex items-center justify-between">
                                            <Icon className="size-8 text-brand-green" />
                                            <span className="text-3xl font-extrabold text-brand-ink/10">
                                                {String(index + 1).padStart(
                                                    2,
                                                    '0',
                                                )}
                                            </span>
                                        </div>
                                        <h3 className="text-lg font-bold">
                                            {value.title}
                                        </h3>
                                        <p className="text-sm text-muted-foreground">
                                            {value.description}
                                        </p>
                                    </Card>
                                </RevealItem>
                            );
                        })}
                    </RevealGroup>
                </div>
            </section>
        </SiteLayout>
    );
}
