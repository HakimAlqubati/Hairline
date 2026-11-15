<?php

declare(strict_types=1);

use App\Models\SiteVisit;
use App\Services\Analytics\VisitorDeviceParser;

test('parses human user agents into device type, device name, operating system, and browser', function (
    ?string $userAgent,
    string $expectedDeviceType,
    ?string $expectedDeviceName,
    string $expectedOs,
    ?string $expectedBrowser,
) {
    $result = VisitorDeviceParser::parse($userAgent);

    expect($result)->toBe([
        'is_bot' => false,
        'device_type' => $expectedDeviceType,
        'device_name' => $expectedDeviceName,
        'os' => $expectedOs,
        'browser' => $expectedBrowser,
    ]);
})->with([
    'iphone safari' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 Mobile/15E148 Safari/604.1',
        SiteVisit::DEVICE_MOBILE,
        'iPhone',
        'iOS',
        'Safari',
    ],
    'iphone instagram in-app browser' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/21E219 Instagram 329.0.0.29.93',
        SiteVisit::DEVICE_MOBILE,
        'iPhone',
        'iOS',
        'Instagram',
    ],
    'samsung android chrome' => [
        'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Mobile Safari/537.36',
        SiteVisit::DEVICE_MOBILE,
        'Samsung',
        'Android',
        'Chrome',
    ],
    'android snapchat in-app browser' => [
        'Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36 Snapchat/12.80.0',
        SiteVisit::DEVICE_MOBILE,
        'Google Pixel',
        'Android',
        'Snapchat',
    ],
    'android tiktok in-app browser' => [
        'Mozilla/5.0 (Linux; Android 13; M2101K6G) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/118.0.0.0 Mobile Safari/537.36 BytedanceWebview/d8a21c6',
        SiteVisit::DEVICE_MOBILE,
        'Android Phone',
        'Android',
        'TikTok',
    ],
    'whatsapp in-app browser' => [
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 WhatsApp/24.10.74',
        SiteVisit::DEVICE_MOBILE,
        'iPhone',
        'iOS',
        'WhatsApp',
    ],
    'ipad safari' => [
        'Mozilla/5.0 (iPad; CPU OS 17_4 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4 Mobile/15E148 Safari/604.1',
        SiteVisit::DEVICE_TABLET,
        'iPad',
        'iPadOS',
        'Safari',
    ],
    'android tablet chrome' => [
        'Mozilla/5.0 (Linux; Android 13; Lenovo TB125FU) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        SiteVisit::DEVICE_TABLET,
        'Android Tablet',
        'Android',
        'Chrome',
    ],
    'windows edge' => [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
        SiteVisit::DEVICE_DESKTOP,
        'Windows PC',
        'Windows',
        'Edge',
    ],
    'mac safari' => [
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_4_1) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Safari/605.1.15',
        SiteVisit::DEVICE_DESKTOP,
        'Mac',
        'macOS',
        'Safari',
    ],
    'empty user agent' => [
        null,
        SiteVisit::DEVICE_UNKNOWN,
        null,
        'Unknown',
        null,
    ],
]);

test('identifies known bots and link preview crawlers as bots', function (string $userAgent) {
    $result = VisitorDeviceParser::parse($userAgent);

    expect($result['is_bot'])->toBeTrue();
})->with([
    'googlebot' => ['Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
    'bingbot' => ['Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
    'whatsapp link preview bot' => ['WhatsApp/2.23.20.0 A'],
    'lighthouse' => ['Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 Chrome-Lighthouse'],
]);
