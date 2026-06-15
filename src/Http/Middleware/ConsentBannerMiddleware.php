<?php

declare(strict_types=1);

namespace YezzMedia\Consent\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use YezzMedia\Consent\Support\ConsentManager;

final class ConsentBannerMiddleware
{
    public function __construct(
        private readonly ConsentManager $consent,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof Response) {
            return $response;
        }

        if ($request->expectsJson()) {
            return $response;
        }

        $contentType = $response->headers->get('Content-Type', 'text/html');

        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        if (str_starts_with($request->path(), '__consent')) {
            return $response;
        }

        if (! config('user-consent.enabled', true)) {
            return $response;
        }

        $user = $request->user();
        $guestToken = $request->cookie(config('user-consent.guest_token_cookie', 'consent_token'));

        $this->consent->usingIdentity(
            userId: $user?->getAuthIdentifier(),
            guestToken: $guestToken,
        );

        if ($this->consent->allDecided()) {
            return $response;
        }

        if ($guestToken === null) {
            $guestToken = Str::uuid()->toString();
            $lifetime = config('user-consent.guest_token_lifetime_days', 365);

            $response->withCookie(
                cookie()->forever(config('user-consent.guest_token_cookie', 'consent_token'), $guestToken)
            );

            $this->consent->usingIdentity(
                userId: $user?->getAuthIdentifier(),
                guestToken: $guestToken,
            );
        }

        $content = $response->getContent();

        if ($content === false || $content === null) {
            return $response;
        }

        $bannerHtml = view('user-consent::components.consent-banner')->render();

        $replaced = str_replace('</body>', "\n".$bannerHtml."\n</body>", $content);

        if ($replaced === $content) {
            $replaced = $content."\n".$bannerHtml;
        }

        $response->setContent($replaced);

        return $response;
    }
}
