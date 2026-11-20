import {
    useCallback,
    useEffect,
    useLayoutEffect,
    useRef,
    useState,
} from 'react';
import type { ContentNavItem } from '@/types';

const FALLBACK_GAP = 2;

const useIsomorphicLayoutEffect =
    typeof window === 'undefined' ? useEffect : useLayoutEffect;

export function useNavOverflow(items: ContentNavItem[]) {
    const navRef = useRef<HTMLElement | null>(null);
    const measurementRef = useRef<HTMLDivElement | null>(null);
    const [visibleCount, setVisibleCount] = useState(items.length);

    const measure = useCallback(() => {
        const nav = navRef.current;
        const measurement = measurementRef.current;

        if (!nav || !measurement) {
            return;
        }

        const availableWidth = nav.clientWidth;
        const gap =
            Number.parseFloat(getComputedStyle(measurement).columnGap) ||
            FALLBACK_GAP;
        const itemWidths = Array.from(measurement.children).map(
            (child) => child.getBoundingClientRect().width,
        );

        let usedWidth = 0;
        let nextVisibleCount = 0;

        for (const width of itemWidths) {
            const nextWidth =
                usedWidth + width + (nextVisibleCount > 0 ? gap : 0);

            if (nextWidth > availableWidth) {
                break;
            }

            usedWidth = nextWidth;
            nextVisibleCount += 1;
        }

        setVisibleCount((current) =>
            current === nextVisibleCount ? current : nextVisibleCount,
        );
    }, []);

    useIsomorphicLayoutEffect(() => {
        measure();

        const nav = navRef.current;
        const measurement = measurementRef.current;

        window.addEventListener('resize', measure);

        if (!nav || !measurement || typeof ResizeObserver === 'undefined') {
            return () => window.removeEventListener('resize', measure);
        }

        const observer = new ResizeObserver(measure);
        observer.observe(nav);
        observer.observe(measurement);

        document.fonts.ready.then(measure).catch(() => {});

        return () => {
            observer.disconnect();
            window.removeEventListener('resize', measure);
        };
    }, [items, measure]);

    return {
        navRef,
        measurementRef,
        visibleItems: items.slice(0, visibleCount),
        hiddenItems: items.slice(visibleCount),
    };
}
