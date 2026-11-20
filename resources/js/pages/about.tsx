import { usePage } from '@inertiajs/react';
import {
    Compass,
    Eye,
    Factory,
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
    const [intro, ...story] = about.who_we_are;

    return (
        <SiteLayout>
            <Seo title={page.seo.title} description={page.seo.description} />

            <section className="bg-brand-cream">
                <div className="mx-auto max-w-4xl px-4 py-14 sm:px-6 sm:py-16 lg:px-8">
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

                    <Reveal className="mx-auto mt-8 max-w-3xl">
                        {intro ? (
                            <p className="text-lg leading-relaxed font-medium text-brand-ink sm:text-xl">
                                {intro}
                            </p>
                        ) : null}
                    </Reveal>

                    <Reveal
                        delay={0.1}
                        className="relative mx-auto mt-8 max-w-3xl sm:mt-10"
                    >
                        <div
                            aria-hidden="true"
                            className="absolute inset-0 translate-x-3 translate-y-3 rounded-3xl bg-brand-green/10 sm:translate-x-4 sm:translate-y-4"
                        />
                        <figure className="relative overflow-hidden rounded-3xl bg-brand-sand shadow-xl ring-1 ring-brand-green/15">
                            <img
                                src="/factoryImage.jpg"
                                alt="The Elkris Foods manufacturing and packaging facility in Lagos, Nigeria"
                                className="aspect-[4/3] w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            />
                            <figcaption className="absolute inset-x-3 bottom-3 flex items-center gap-3 rounded-2xl bg-white/90 px-4 py-3 shadow-sm backdrop-blur sm:inset-x-4 sm:bottom-4">
                                <span className="flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-mint text-brand-green">
                                    <Factory className="size-4" />
                                </span>
                                <span>
                                    <span className="block text-[11px] font-semibold tracking-widest text-brand-green uppercase">
                                        Our Facility
                                    </span>
                                    <span className="block text-xs font-medium text-brand-ink/65">
                                        Manufacturing &amp; packaging · Lagos,
                                        Nigeria
                                    </span>
                                </span>
                            </figcaption>
                        </figure>
                    </Reveal>

                    <Reveal
                        delay={0.05}
                        className="mx-auto mt-8 max-w-3xl space-y-5 sm:mt-10"
                    >
                        {story.map((paragraph) => (
                            <p
                                key={paragraph}
                                className="text-lg leading-relaxed font-medium text-brand-ink sm:text-xl"
                            >
                                {paragraph}
                            </p>
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
                            <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
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
                            <p className="text-base leading-relaxed text-muted-foreground sm:text-lg">
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
