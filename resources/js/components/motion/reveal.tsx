import { motion, useReducedMotion } from 'motion/react';
import type { Variants } from 'motion/react';
import type { ReactNode } from 'react';

const DEFAULT_VIEWPORT = { once: true, amount: 0.25 } as const;

const fadeUp: Variants = {
    hidden: { opacity: 0, y: 28 },
    visible: { opacity: 1, y: 0 },
};

const staggerParent: Variants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.08, delayChildren: 0.05 } },
};

type RevealProps = {
    children: ReactNode;
    className?: string;
    delay?: number;
    as?: 'div' | 'section' | 'li' | 'p' | 'span';
};

export function Reveal({
    children,
    className,
    delay = 0,
    as = 'div',
}: RevealProps) {
    const reduced = useReducedMotion();
    const Component = motion[as];

    if (reduced) {
        const Tag = as;

        return <Tag className={className}>{children}</Tag>;
    }

    return (
        <Component
            className={className}
            variants={fadeUp}
            initial="hidden"
            whileInView="visible"
            viewport={DEFAULT_VIEWPORT}
            transition={{ duration: 0.55, ease: [0.21, 0.65, 0.35, 1], delay }}
        >
            {children}
        </Component>
    );
}

type RevealGroupProps = {
    children: ReactNode;
    className?: string;
    as?: 'div' | 'ul' | 'ol';
};

export function RevealGroup({
    children,
    className,
    as = 'div',
}: RevealGroupProps) {
    const reduced = useReducedMotion();
    const Tag = as;

    if (reduced) {
        return <Tag className={className}>{children}</Tag>;
    }

    const Component = motion[as];

    return (
        <Component
            className={className}
            variants={staggerParent}
            initial="hidden"
            whileInView="visible"
            viewport={DEFAULT_VIEWPORT}
        >
            {children}
        </Component>
    );
}

type RevealItemProps = {
    children: ReactNode;
    className?: string;
    as?: 'li' | 'div' | 'span' | 'p';
};

export function RevealItem({
    children,
    className,
    as = 'div',
}: RevealItemProps) {
    const reduced = useReducedMotion();
    const Tag = as;

    if (reduced) {
        return <Tag className={className}>{children}</Tag>;
    }

    const Component = motion[as];

    return (
        <Component className={className} variants={fadeUp}>
            {children}
        </Component>
    );
}

type RevealHeadingProps = {
    children: string;
    className?: string;
    as?: 'h1' | 'h2' | 'h3';
};

export function RevealHeading({
    children,
    className,
    as = 'h2',
}: RevealHeadingProps) {
    const reduced = useReducedMotion();
    const Tag = as;
    const words = children.split(' ');

    if (reduced) {
        return <Tag className={className}>{children}</Tag>;
    }

    const Component = motion[as];

    return (
        <Component className={className} aria-label={children}>
            <span className="sr-only">{children}</span>
            <span aria-hidden="true">
                {words.map((word, i) => (
                    <motion.span
                        key={`${word}-${i}`}
                        className="inline-block will-change-transform"
                        initial={{ opacity: 0, y: '0.6em' }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={DEFAULT_VIEWPORT}
                        transition={{
                            duration: 0.5,
                            ease: [0.21, 0.65, 0.35, 1],
                            delay: i * 0.06,
                        }}
                    >
                        {word}
                        {i < words.length - 1 ? ' ' : ''}
                    </motion.span>
                ))}
            </span>
        </Component>
    );
}
