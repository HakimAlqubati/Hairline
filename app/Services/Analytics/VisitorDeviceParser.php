<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\SiteVisit;

class VisitorDeviceParser
{
    /**
     * Bot and crawler User-Agent signatures to exclude from human visitor counts.
     *
     * @var array<int, string>
     */
    private const BOT_SIGNATURES = [
        'googlebot',
        'bingbot',
        'yandexbot',
        'duckduckbot',
        'baiduspider',
        'applebot',
        'petalbot',
        'bytespider',
        'gptbot',
        'claudebot',
        'ahrefsbot',
        'semrushbot',
        'mj12bot',
        'twitterbot',
        'linkedinbot',
        'discordbot',
        'telegrambot',
        'facebookexternalhit',
        'crawler',
        'spider',
        'slurp',
        'lighthouse',
        'pagespeed',
        'headlesschrome',
        'uptimerobot',
        'pingdom',
        'screaming frog',
        'scrapy',
        'python-requests',
        'go-http-client',
    ];

    /**
     * Parse a User-Agent string into compact visitor device metadata.
     *
     * @return array{
     *     is_bot: bool,
     *     device_type: string,
     *     device_name: ?string,
     *     os: string,
     *     browser: ?string,
     * }
     */
    public static function parse(?string $userAgent): array
    {
        $ua = strtolower(trim(substr((string) $userAgent, 0, 512)));

        if ($ua === '') {
            return [
                'is_bot' => false,
                'device_type' => SiteVisit::DEVICE_UNKNOWN,
                'device_name' => null,
                'os' => 'Unknown',
                'browser' => null,
            ];
        }

        if (self::isBot($ua)) {
            return [
                'is_bot' => true,
                'device_type' => SiteVisit::DEVICE_UNKNOWN,
                'device_name' => null,
                'os' => 'Unknown',
                'browser' => null,
            ];
        }

        $deviceType = self::detectDeviceType($ua);

        return [
            'is_bot' => false,
            'device_type' => $deviceType,
            'device_name' => self::detectDeviceName($ua, $deviceType),
            'os' => self::detectOs($ua),
            'browser' => self::detectBrowser($ua),
        ];
    }

    private static function isBot(string $ua): bool
    {
        if (str_contains($ua, 'whatsapp') && ! str_contains($ua, 'mozilla')) {
            return true;
        }

        foreach (self::BOT_SIGNATURES as $signature) {
            if (str_contains($ua, $signature)) {
                return true;
            }
        }

        return false;
    }

    private static function detectDeviceType(string $ua): string
    {
        if (self::containsAny($ua, ['ipad', 'tablet', 'playbook', 'silk', 'kindle'])) {
            return SiteVisit::DEVICE_TABLET;
        }

        if (str_contains($ua, 'android') && ! str_contains($ua, 'mobile')) {
            return SiteVisit::DEVICE_TABLET;
        }

        if (self::containsAny($ua, ['iphone', 'ipod', 'mobile', 'android', 'blackberry', 'windows phone', 'opera mini', 'iemobile', 'webos', 'bb10'])) {
            return SiteVisit::DEVICE_MOBILE;
        }

        if (self::containsAny($ua, ['windows nt', 'macintosh', 'mac os x', 'linux', 'x11', 'cros', 'ubuntu', 'fedora'])) {
            return SiteVisit::DEVICE_DESKTOP;
        }

        return SiteVisit::DEVICE_UNKNOWN;
    }

    private static function detectOs(string $ua): string
    {
        return match (true) {
            str_contains($ua, 'ipad') => 'iPadOS',
            self::containsAny($ua, ['iphone', 'ipod']) => 'iOS',
            str_contains($ua, 'android') => 'Android',
            str_contains($ua, 'windows') => 'Windows',
            self::containsAny($ua, ['macintosh', 'mac os x']) => 'macOS',
            str_contains($ua, 'cros') => 'ChromeOS',
            self::containsAny($ua, ['linux', 'x11']) => 'Linux',
            default => 'Unknown',
        };
    }

    private static function detectDeviceName(string $ua, string $deviceType): ?string
    {
        if (str_contains($ua, 'iphone')) {
            return 'iPhone';
        }

        if (str_contains($ua, 'ipad')) {
            return 'iPad';
        }

        if (str_contains($ua, 'ipod')) {
            return 'iPod';
        }

        if (str_contains($ua, 'android')) {
            return match (true) {
                self::containsAny($ua, ['samsung', 'sm-']) => 'Samsung',
                self::containsAny($ua, ['huawei', 'honor']) => 'Huawei / Honor',
                self::containsAny($ua, ['xiaomi', 'redmi', 'poco']) => 'Xiaomi',
                str_contains($ua, 'oppo') => 'OPPO',
                str_contains($ua, 'realme') => 'Realme',
                str_contains($ua, 'pixel') => 'Google Pixel',
                str_contains($ua, 'oneplus') => 'OnePlus',
                $deviceType === SiteVisit::DEVICE_TABLET => 'Android Tablet',
                default => 'Android Phone',
            };
        }

        if (self::containsAny($ua, ['macintosh', 'mac os x'])) {
            return 'Mac';
        }

        if (str_contains($ua, 'windows')) {
            return 'Windows PC';
        }

        if (str_contains($ua, 'cros')) {
            return 'Chromebook';
        }

        if (self::containsAny($ua, ['linux', 'x11'])) {
            return 'Linux PC';
        }

        return null;
    }

    private static function detectBrowser(string $ua): ?string
    {
        return match (true) {
            str_contains($ua, 'instagram') => 'Instagram',
            str_contains($ua, 'snapchat') => 'Snapchat',
            self::containsAny($ua, ['tiktok', 'bytedancewebview', 'musical_ly']) => 'TikTok',
            str_contains($ua, 'whatsapp') => 'WhatsApp',
            self::containsAny($ua, ['fban', 'fbav', 'fb_iab']) => 'Facebook',
            self::containsAny($ua, ['edg/', 'edga/', 'edgios/']) => 'Edge',
            self::containsAny($ua, ['opr/', 'opera', 'opios']) => 'Opera',
            str_contains($ua, 'samsungbrowser') => 'Samsung Internet',
            str_contains($ua, 'ucbrowser') => 'UC Browser',
            self::containsAny($ua, ['firefox', 'fxios']) => 'Firefox',
            str_contains($ua, 'crios') || (str_contains($ua, 'chrome') && ! self::containsAny($ua, ['edg/', 'opr/'])) => 'Chrome',
            str_contains($ua, 'safari') && ! self::containsAny($ua, ['chrome', 'crios']) => 'Safari',
            default => null,
        };
    }

    /**
     * @param  array<int, string>  $needles
     */
    private static function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
