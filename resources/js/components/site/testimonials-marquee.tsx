import { usePage } from '@inertiajs/react';
import { Quote, Star } from 'lucide-react';
import { Card } from '@/components/ui/card';
import type { SharedProps, Testimonial } from '@/types';

function initials(name: string): string {
    return name
        .split(' ')
        .map((part) => part.charAt(0))
        .filter(Boolean)
        .slice(0, 2)
        .join('')
        .toUpperCase();
}

function TestimonialCard({ testimonial }: { testimonial: Testimonial }) {
    const { content } = usePage<SharedProps>().props;
    const labels = content.global.testimonials;

    return (
        <Card className="flex h-full w-[320px] shrink-0 flex-col gap-4 p-6 sm:w-[360px]">
            <div className="flex items-center justify-between">
                <Quote className="size-6 text-brand-red" aria-hidden="true" />
                <div
                    className="flex gap-0.5 text-brand-gold"
                    role="img"
                    aria-label={labels.rating_label}
                >
                    {Array.from({ length: 5 }).map((_, i) => (
                        <Star
                            key={i}
                            className="size-3.5 fill-current"
                            aria-hidden="true"
                        />
                    ))}
                </div>
            </div>
            <blockquote className="line-clamp-5 text-[15px] leading-relaxed text-brand-ink/85">
                “{testimonial.quote}”
            </blockquote>
            <div className="mt-auto flex items-center gap-3 border-t border-border/60 pt-4">
                {testimonial.photo_path ? (
                    <img
                        src={testimonial.photo_path}
                        alt=""
                        className="size-10 shrink-0 rounded-full object-cover"
                        loading="lazy"
                    />
                ) : (
                    <span
                        className="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-green text-xs font-bold text-white"
                        aria-hidden="true"
                    >
                        {initials(testimonial.name)}
                    </span>
                )}
                <div className="min-w-0">
                    <p className="truncate text-sm font-semibold text-brand-ink">
                        {testimonial.name}
                    </p>
                    {testimonial.location ? (
                        <p className="truncate text-xs text-muted-foreground">
                            {testimonial.location}
                        </p>
                    ) : null}
                </div>
            </div>
        </Card>
    );
}

export function TestimonialsMarquee({
    testimonials,
}: {
    testimonials: Testimonial[];
}) {
    const { content } = usePage<SharedProps>().props;

    if (testimonials.length === 0) {
        return null;
    }

    const loop = [...testimonials, ...testimonials];

    return (
        <div
            className="overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_8%,black_92%,transparent)] motion-reduce:overflow-x-auto motion-reduce:[mask-image:none]"
            aria-label={content.global.testimonials.aria_label}
        >
            <div className="flex w-max animate-[testimonials-marquee_55s_linear_infinite] items-stretch gap-5 py-2 focus-within:[animation-play-state:paused] hover:[animation-play-state:paused] motion-reduce:w-auto motion-reduce:animate-none">
                {loop.map((testimonial, index) => (
                    <div
                        key={`${testimonial.id}-${index}`}
                        aria-hidden={index >= testimonials.length}
                        className="flex"
                    >
                        <TestimonialCard testimonial={testimonial} />
                    </div>
                ))}
            </div>
        </div>
    );
}
