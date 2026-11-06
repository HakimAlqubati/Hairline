<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Services\Booking\SpecialistAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GoogleOAuthService
{
    public function __construct(
        private readonly SpecialistAvailabilityService $availabilityService
    ) {}

    /**
     * Build the Google OAuth 2.0 authorization URL and preserve the state/booking context in session.
     */
    public function buildAuthorizationUrl(Request $request): string
    {
        $isProfile = $request->boolean('is_profile');
        $rawPhone = $this->availabilityService->sanitizePhoneDigits((string) $request->query('phone', ''));
        $normalizedPhone = $rawPhone !== '' ? $this->availabilityService->normalizeSaudiPhone($rawPhone) : '';

        $bookingContext = [
            'is_profile' => $isProfile,
            'phone' => $normalizedPhone,
            'service_id' => $isProfile ? '' : (string) $request->query('service_id', ''),
            'specialist_id' => $isProfile ? '' : (string) $request->query('specialist_id', ''),
            'booking_date' => $isProfile ? '' : (string) $request->query('booking_date', ''),
            'booking_time' => $isProfile ? '' : (string) $request->query('booking_time', ''),
        ];

        $stateToken = bin2hex(random_bytes(16));
        session([
            'google_oauth_state' => $stateToken,
            'pending_google_booking' => $bookingContext,
        ]);

        $clientId = (string) config('services.google.client_id');
        $redirectUri = $this->resolveGoogleRedirectUri($request);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'online',
            'prompt' => 'select_account',
            'state' => $stateToken,
        ]);

        return 'https://accounts.google.com/o/oauth2/v2/auth?'.$query;
    }

    /**
     * Resolve the Google OAuth callback URI matching the current request origin or configured redirect URI.
     */
    public function resolveGoogleRedirectUri(Request $request): string
    {
        $configured = (string) config('services.google.redirect', '');
        if (str_starts_with($configured, 'http://') || str_starts_with($configured, 'https://')) {
            return $configured;
        }

        return rtrim($request->getSchemeAndHttpHost(), '/').'/auth/google/callback';
    }

    /**
     * Exchange Google OAuth 2.0 authorization code for user profile.
     *
     * @return ?array{email: string, name: string, picture: ?string}
     */
    public function exchangeGoogleCodeForUser(string $code, string $redirectUri): ?array
    {
        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => (string) config('services.google.client_id'),
            'client_secret' => (string) config('services.google.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if (! $tokenResponse->successful()) {
            return null;
        }

        $accessToken = (string) $tokenResponse->json('access_token', '');
        if ($accessToken === '') {
            return null;
        }

        return $this->fetchGoogleUserFromAccessToken($accessToken);
    }

    /**
     * Fetch Google user profile from access token and verify that email is verified.
     *
     * @return ?array{email: string, name: string, picture: ?string}
     */
    public function fetchGoogleUserFromAccessToken(string $accessToken): ?array
    {
        $userResponse = Http::withToken($accessToken)->get('https://www.googleapis.com/oauth2/v3/userinfo');
        if (! $userResponse->successful()) {
            return null;
        }

        $email = trim((string) $userResponse->json('email', ''));
        $emailVerified = (bool) $userResponse->json('email_verified', false);
        if ($email === '' || ! $emailVerified) {
            return null;
        }

        $name = trim((string) $userResponse->json('name', ''));
        $picture = $userResponse->json('picture');

        return [
            'email' => $email,
            'name' => $name !== '' ? $name : $this->extractNameFromEmail($email),
            'picture' => is_string($picture) ? $picture : null,
        ];
    }

    /**
     * Render a minimal HTML response for the OAuth popup that notifies the parent window via postMessage and closes itself.
     *
     * @param  array<string, mixed>  $payload
     */
    public function renderPopupCallbackScript(array $payload): Response
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول عبر Google - هيرلاين</title>
</head>
<body style="background:#FAF8F5;color:#211C18;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;">
    <div style="text-align:center;">
        <p style="font-weight:bold;">جاري إتمام الحجز والعودة لصفحة الصالون...</p>
    </div>
    <script>
        (function() {
            const payload = {$json};
            if (window.opener && !window.opener.closed) {
                window.opener.postMessage({
                    type: 'HAIRLINE_GOOGLE_AUTH_RESULT',
                    payload: payload
                }, window.location.origin);
                window.close();
            } else {
                window.location.href = '/booking';
            }
        })();
    </script>
</body>
</html>
HTML;

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Extract a readable name from an email address if name was not explicitly entered.
     */
    public function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0] ?? 'عميلة هيرلاين';
        $cleaned = str_replace(['.', '_', '-'], ' ', $localPart);
        $cleaned = trim((string) preg_replace('/\d+/', '', $cleaned));

        return $cleaned !== '' ? mb_convert_case($cleaned, MB_CASE_TITLE, 'UTF-8') : $localPart;
    }
}
