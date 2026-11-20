import { Link } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Play } from 'lucide-react';
import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import { useCallback, useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';

type Slide = {
    eyebrow?: string;
    title: string;
    highlight: string;
    body?: string;
    bodyClassName?: string;
    ctaLabel: string;
    ctaHref: string;
};

type HeroCarouselProps = {
    slides: Slide[];
    video: string;
    videoPoster?: string;
    labels?: {
        ariaLabel: string;
        watchLabel: string;
        previousLabel: string;
        nextLabel: string;
        slideLabel: string;
    };
};

export function HeroCarousel({
    slides,
    video,
    videoPoster,
    labels,
}: HeroCarouselProps) {
    const [index, setIndex] = useState(0);
    const reduced = useReducedMotion();

    const go = useCallback(
        (next: number) => setIndex((next + slides.length) % slides.length),
        [slides.length],
    );

    useEffect(() => {
        const onKey = (e: KeyboardEvent) => {
            if (e.key === 'ArrowLeft') {
                go(index - 1);
            }

            if (e.key === 'ArrowRight') {
                go(index + 1);
            }
        };
        window.addEventListener('keydown', onKey);

        return () => window.removeEventListener('keydown', onKey);
    }, [go, index]);

    const slide = slides[index];

    return (
        <section
            className="relative min-h-svh w-full overflow-hidden bg-brand-cream"
            aria-roledescription="carousel"
            aria-label={labels?.ariaLabel ?? 'Elkris highlights'}
        >
            <div className="relative z-10 mx-auto grid min-h-svh max-w-7xl items-center gap-10 px-4 pt-28 pb-28 sm:px-6 lg:grid-cols-2 lg:gap-14 lg:px-8">
                <AnimatePresence mode="wait">
                    <motion.div
                        key={index}
                        initial={
                            reduced ? { opacity: 1 } : { opacity: 0, y: 32 }
                        }
                        animate={{ opacity: 1, y: 0 }}
                        exit={reduced ? { opacity: 0 } : { opacity: 0, y: -24 }}
                        transition={{
                            duration: 0.5,
                            ease: [0.21, 0.65, 0.35, 1],
                        }}
                    >
                        {slide.eyebrow ? (
                            <motion.p
                                className="text-sm font-bold tracking-[0.2em] text-brand-green uppercase"
                                initial={reduced ? {} : { opacity: 0, x: -16 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ delay: 0.1, duration: 0.4 }}
                            >
                                {slide.eyebrow}
                            </motion.p>
                        ) : null}

                        <h1 className="mt-4 text-4xl leading-[1.05] font-extrabold tracking-tight text-brand-ink sm:text-5xl xl:text-6xl">
                            {slide.title.split(' ').map((word, i) => (
                                <motion.span
                                    key={`${word}-${i}`}
                                    className={
                                        word === slide.highlight
                                            ? 'text-brand-red'
                                            : undefined
                                    }
                                    aria-hidden="true"
                                    initial={
                                        reduced
                                            ? {}
                                            : { opacity: 0, y: '0.5em' }
                                    }
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{
                                        delay: 0.15 + i * 0.05,
                                        duration: 0.45,
                                        ease: [0.21, 0.65, 0.35, 1],
                                    }}
                                >
                                    {word}{' '}
                                </motion.span>
                            ))}
                        </h1>

                        {slide.body ? (
                            <motion.p
                                className={`mt-5 max-w-xl leading-relaxed ${slide.bodyClassName ?? 'text-lg text-brand-ink/80'}`}
                                initial={reduced ? {} : { opacity: 0, y: 16 }}
                                animate={{ opacity: 1, y: 0 }}
                                transition={{ delay: 0.4, duration: 0.45 }}
                            >
                                {slide.body}
                            </motion.p>
                        ) : null}

                        <motion.div
                            className="mt-8 flex flex-wrap gap-3"
                            initial={reduced ? {} : { opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ delay: 0.55, duration: 0.45 }}
                        >
                            <Link href={slide.ctaHref}>
                                <Button
                                    size="lg"
                                    className="rounded-full px-8 font-semibold"
                                >
                                    {slide.ctaLabel}
                                    <ArrowRight className="size-4" />
                                </Button>
                            </Link>
                        </motion.div>
                    </motion.div>
                </AnimatePresence>

                <motion.div
                    initial={reduced ? {} : { opacity: 0, scale: 0.96 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ duration: 0.6, ease: 'easeOut' }}
                    className="relative mx-auto w-full max-w-xl lg:max-w-none"
                >
                    <div className="overflow-hidden rounded-3xl border-4 border-background bg-black shadow-2xl ring-1 ring-brand-green/15">
                        <video
                            src={video}
                            poster={videoPoster}
                            autoPlay
                            muted
                            loop
                            playsInline
                            controls
                            preload="metadata"
                            className="aspect-square w-full object-cover sm:aspect-video"
                        />
                    </div>
                    <div className="pointer-events-none absolute -bottom-4 left-1/2 flex -translate-x-1/2 items-center gap-2 rounded-full bg-brand-red px-5 py-2 text-xs font-bold tracking-widest text-white uppercase shadow-lg">
                        <Play className="size-3.5" />
                        {labels?.watchLabel ?? 'Watch Elkris'}
                    </div>
                </motion.div>
            </div>

            {/* Controls */}
            <div className="absolute inset-x-0 bottom-6 z-20 flex items-center justify-center gap-4 px-4">
                <button
                    type="button"
                    onClick={() => go(index - 1)}
                    aria-label={labels?.previousLabel ?? 'Previous slide'}
                    className="hidden size-10 items-center justify-center rounded-full border border-brand-ink/15 bg-background/80 text-brand-ink backdrop-blur transition-colors hover:bg-background sm:flex"
                >
                    <ArrowLeft className="size-4" />
                </button>

                <div
                    className="flex items-center gap-2"
                    role="tablist"
                    aria-label="Slides"
                >
                    {slides.map((s, i) => (
                        <button
                            key={s.title}
                            type="button"
                            role="tab"
                            aria-selected={i === index}
                            aria-label={`${labels?.slideLabel ?? 'Go to slide'} ${i + 1}`}
                            onClick={() => go(i)}
                            className={`h-2 rounded-full transition-all duration-300 ${
                                i === index
                                    ? 'w-8 bg-brand-red'
                                    : 'w-2 bg-brand-ink/30 hover:bg-brand-ink/50'
                            }`}
                        />
                    ))}
                </div>

                <button
                    type="button"
                    onClick={() => go(index + 1)}
                    aria-label={labels?.nextLabel ?? 'Next slide'}
                    className="hidden size-10 items-center justify-center rounded-full border border-brand-ink/15 bg-background/80 text-brand-ink backdrop-blur transition-colors hover:bg-background sm:flex"
                >
                    <ArrowRight className="size-4" />
                </button>
            </div>
        </section>
    );
}
