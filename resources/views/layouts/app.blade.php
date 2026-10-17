<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#E2EFEA">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>HAIRLINE BEAUTY LOUNGE | صالون هيرلاين النسائي</title>

    <!-- Google Fonts: Readex Pro (Arabic) + Montserrat (Brand English) + Cormorant Garamond (Logo) + Tajawal (Fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Montserrat:wght@300;400;500;600&family=Readex+Pro:wght@300;400;500;600;700&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- Swiper.js for touch carousels -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>

    <!-- Fixed SAR Currency Badge -->
    <div class="sar-fixed-badge" title="العملة المعتمدة: الريال السعودي (SAR)">
        <span>SAR</span>
    </div>

    <!-- Mobile App Shell Container -->
    <div class="app-shell">
        {{ $slot }}
    </div>

    <!-- Swiper.js -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    @livewireScripts
</body>
</html>
