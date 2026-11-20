<div class="elkris-admin-hero overflow-hidden rounded-2xl border border-gray-200 shadow-sm dark:border-white/10">
    <div class="flex flex-col gap-6 p-6 sm:p-8 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-2xl">
            <div class="flex items-center gap-3">
                <img src="{{ asset('media/logo/Elkris_logo.png') }}" alt="Elkris Foods" class="h-10 w-auto">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300">
                    Content Studio
                </span>
            </div>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-gray-950 dark:text-white">
                Welcome back, {{ $name }}
            </h1>
            <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                Update the website, publish products and stories, and keep up with new leads and orders from one place.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ $contentUrl }}" class="inline-flex items-center justify-center rounded-full bg-[#d62828] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#a61b1b]">
                Edit website content
            </a>
            <a href="{{ $productUrl }}" class="inline-flex items-center justify-center rounded-full border border-[#1b7a36]/30 bg-white px-5 py-2.5 text-sm font-semibold text-[#1b7a36] transition hover:border-[#1b7a36] dark:bg-white/5">
                Add product
            </a>
            <a href="{{ $articleUrl }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-gray-400 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                Write article
            </a>
            <a href="{{ $galleryUrl }}" class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-gray-400 dark:border-white/10 dark:bg-white/5 dark:text-gray-200">
                Upload media
            </a>
            <a href="{{ $siteUrl }}" target="_blank" rel="noreferrer" class="inline-flex items-center justify-center rounded-full px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:text-[#d62828] dark:text-gray-300">
                View website &rarr;
            </a>
        </div>
    </div>
</div>
