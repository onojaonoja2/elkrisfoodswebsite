import { Component } from 'react';
import type { ErrorInfo, ReactNode } from 'react';

type Props = {
    children: ReactNode;
};

type State = {
    hasError: boolean;
    message: string;
};

export class ErrorBoundary extends Component<Props, State> {
    state: State = { hasError: false, message: '' };

    static getDerivedStateFromError(error: Error): State {
        return { hasError: true, message: error.message };
    }

    componentDidCatch(error: Error, info: ErrorInfo): void {
        console.error('Uncaught render error:', error, info.componentStack);
    }

    handleReload = (): void => {
        window.location.reload();
    };

    handleGoHome = (): void => {
        window.location.assign('/');
    };

    render(): ReactNode {
        if (!this.state.hasError) {
            return this.props.children;
        }

        return (
            <div className="flex min-h-screen flex-col items-center justify-center gap-4 bg-brand-cream px-6 text-center">
                <img
                    src="/media/logo/Elkris_logo.png"
                    alt="Elkris Foods"
                    className="h-14 w-auto"
                />
                <h1 className="text-2xl font-bold text-brand-ink">
                    Something went wrong
                </h1>
                <p className="max-w-md text-sm text-brand-ink/70">
                    The page hit an unexpected error. Your visit is safe. Try
                    reloading, or head back home.
                </p>
                {this.state.message ? (
                    <p className="max-w-lg rounded-xl border border-brand-red/30 bg-white px-4 py-2 font-mono text-xs text-brand-red">
                        {this.state.message}
                    </p>
                ) : null}
                <div className="mt-2 flex gap-3">
                    <button
                        type="button"
                        onClick={this.handleReload}
                        className="rounded-full bg-brand-red px-6 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-red-dark"
                    >
                        Reload page
                    </button>
                    <button
                        type="button"
                        onClick={this.handleGoHome}
                        className="rounded-full border border-brand-green px-6 py-2.5 text-sm font-semibold text-brand-green transition-colors hover:bg-brand-green hover:text-white"
                    >
                        Go to homepage
                    </button>
                </div>
            </div>
        );
    }
}
