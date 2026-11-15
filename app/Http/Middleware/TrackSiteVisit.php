<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Analytics\VisitorDeviceParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

use function Illuminate\Support\defer;

class TrackSiteVisit
{
    /**
     * Record public page visits asynchronously after the response has been sent.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldTrack($request, $response)) {
            return $response;
        }

        // Capture lightweight scalar values only so request/response objects are never held in memory.
        $rawPath = ltrim($request->path(), '/');
        $path = $rawPath === '' ? '/' : '/'.mb_substr($rawPath, 0, 254);
        $routeName = $request->route()?->getName();
        $pageName = $routeName ? mb_substr($routeName, 0, 50) : null;
        $userAgent = mb_substr((string) $request->userAgent(), 0, 512);
        $ipAddress = $request->ip();
        $referer = $request->headers->get('referer');
        $appHost = strtolower($request->getHost());
        $sessionId = $request->hasSession() ? mb_substr($request->session()->getId(), 0, 100) : null;
        $customerId = $request->hasSession() ? $request->session()->get('logged_in_customer_id') : null;
        $visitedAt = now()->toDateTimeString();

        defer(static function () use (
            $path,
            $pageName,
            $userAgent,
            $ipAddress,
            $referer,
            $appHost,
            $sessionId,
            $customerId,
            $visitedAt,
        ): void {
            $parsed = VisitorDeviceParser::parse($userAgent);

            if ($parsed['is_bot']) {
                return;
            }

            $refererHost = null;
            if (is_string($referer) && $referer !== '') {
                $parsedHost = parse_url($referer, PHP_URL_HOST);
                if (is_string($parsedHost) && $parsedHost !== '') {
                    $normalizedHost = strtolower($parsedHost);
                    if ($normalizedHost !== $appHost) {
                        $refererHost = mb_substr($normalizedHost, 0, 120);
                    }
                }
            }

            DB::table('site_visits')->insert([
                'device_type' => $parsed['device_type'],
                'device_name' => $parsed['device_name'],
                'os' => $parsed['os'],
                'browser' => $parsed['browser'],
                'page_name' => $pageName,
                'path' => $path,
                'referer_host' => $refererHost,
                'ip_address' => $ipAddress ? mb_substr($ipAddress, 0, 45) : null,
                'session_id' => $sessionId !== '' ? $sessionId : null,
                'customer_id' => is_numeric($customerId) ? (int) $customerId : null,
                'user_agent' => $userAgent !== '' ? $userAgent : null,
                'visited_at' => $visitedAt,
            ]);
        });

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET') || $request->ajax() || $request->expectsJson()) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $purpose = strtolower(
            (string) ($request->headers->get('Sec-Purpose')
                ?? $request->headers->get('Purpose')
                ?? $request->headers->get('X-Purpose')
                ?? '')
        );

        if ($purpose !== '' && (str_contains($purpose, 'prefetch') || str_contains($purpose, 'preview'))) {
            return false;
        }

        return true;
    }
}
