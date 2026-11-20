<?php

namespace App\Http\Responses;

use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $isContentStaff = $user?->isContentStaff() ?? false;

        $intended = $request->session()->get('url.intended');
        $intendedPath = is_string($intended)
            ? parse_url($intended, PHP_URL_PATH)
            : null;
        $intendedPath = is_string($intendedPath) ? $intendedPath : null;

        $intendedAdminPath = $this->isAdminPath($intendedPath);
        $intendedStudioPath = $this->isStudioPath($intendedPath);

        if (! $isAdmin && $intendedAdminPath) {
            $request->session()->forget('url.intended');
            $intended = null;
            $intendedStudioPath = false;
        }

        if (! $isContentStaff && $intendedStudioPath) {
            $request->session()->forget('url.intended');
            $intended = null;
        }

        if ($isAdmin && is_string($intended) && ($intendedAdminPath || $intendedStudioPath)) {
            $request->session()->forget('url.intended');

            return Inertia::location($intended);
        }

        if ($isContentStaff && is_string($intended) && $intendedStudioPath) {
            $request->session()->forget('url.intended');

            return Inertia::location($intended);
        }

        if ($isAdmin) {
            return Inertia::location('/admin');
        }

        if ($isContentStaff) {
            return Inertia::location('/studio');
        }

        return redirect()->intended(Fortify::redirects('login'));
    }

    private function isAdminPath(?string $path): bool
    {
        return $path === '/admin' || ($path !== null && str_starts_with($path, '/admin/'));
    }

    private function isStudioPath(?string $path): bool
    {
        return $path === '/studio' || ($path !== null && str_starts_with($path, '/studio/'));
    }
}
