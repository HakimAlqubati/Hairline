@props([
    'salonIdentity' => [],
])

<!-- ============================================================
     HAIRLINE LUXURY PRELOADER (Minimalist Circular Progress)
     ============================================================ -->
<div id="globalLuxuryLoader"
     class="global-luxury-loader fixed inset-0 z-[99999] flex flex-col items-center justify-center select-none bg-[#FAF8F5] overflow-hidden"
     aria-hidden="false"
     role="status"
     aria-label="جاري التحميل">
    
    <!-- Ambient luxury background glow -->
    <div class="loader-ambient-glow absolute inset-0 pointer-events-none"></div>

    <!-- Center Content: Only Emblem & Circular Progress (No Text) -->
    <div class="relative z-10 flex items-center justify-center">
        
        <!-- Circular Progress Ring Container -->
        <div class="relative flex items-center justify-center w-28 h-28 sm:w-32 sm:h-32">
            
            <!-- Rotating Circular Progress SVG -->
            <svg class="loader-progress-svg absolute inset-0 w-full h-full pointer-events-none"
                 viewBox="0 0 100 100">
                <!-- Static background track -->
                <circle cx="50" cy="50" r="44"
                        fill="none"
                        stroke="var(--color-primary, #C29E6D)"
                        stroke-opacity="0.15"
                        stroke-width="2.5" />
                
                <!-- Animated active progress stroke -->
                <circle cx="50" cy="50" r="44"
                        fill="none"
                        stroke="url(#luxuryGoldGradient)"
                        stroke-width="2.75"
                        stroke-linecap="round"
                        class="loader-progress-spinner" />
                
                <!-- Gold gradient definition -->
                <defs>
                    <linearGradient id="luxuryGoldGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="var(--color-primary, #C29E6D)" />
                        <stop offset="50%" stop-color="#EAD5B8" />
                        <stop offset="100%" stop-color="var(--color-primary, #C29E6D)" />
                    </linearGradient>
                </defs>
            </svg>

            <!-- Inner White Emblem Frame -->
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white p-3.5 shadow-[0_10px_28px_rgba(33,28,24,0.06)] border border-primary/20 flex items-center justify-center overflow-hidden">
                <!-- Lightweight Pure Vector Logo (Hairline Silhouette) -->
                <div class="w-full h-full text-primary loader-logo-pulse flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320" fill="currentColor" fill-rule="evenodd" class="w-full h-full">
                        <path d="M 151.0 11.5 L 150.0 12.5 L 135.0 13.5 L 116.0 18.5 L 107.0 22.5 L 95.0 32.5 L 82.5 45.0 L 68.5 64.0 L 59.5 84.0 L 53.5 107.0 L 53.5 113.0 L 52.5 114.0 L 52.5 143.0 L 53.5 144.0 L 55.5 161.0 L 60.5 178.0 L 74.5 206.0 L 88.5 224.0 L 106.5 240.0 L 106.0 242.5 L 98.0 239.5 L 86.0 230.5 L 75.5 220.0 L 67.5 210.0 L 61.5 201.0 L 53.5 185.0 L 48.5 171.0 L 45.5 153.0 L 44.5 152.0 L 43.5 120.0 L 44.5 119.0 L 44.5 111.0 L 45.5 110.0 L 45.5 104.0 L 48.5 91.0 L 53.5 76.0 L 61.5 59.0 L 67.5 49.0 L 74.5 41.0 L 66.0 46.5 L 42.5 72.0 L 36.5 84.0 L 28.5 111.0 L 27.5 124.0 L 26.5 125.0 L 27.5 152.0 L 28.5 153.0 L 28.5 159.0 L 29.5 160.0 L 32.5 177.0 L 40.5 199.0 L 43.5 203.0 L 43.5 205.0 L 48.5 214.0 L 61.5 232.0 L 81.0 251.5 L 91.0 259.5 L 106.0 268.5 L 107.5 270.0 L 107.5 272.0 L 105.0 272.5 L 95.0 268.5 L 72.0 253.5 L 56.5 239.0 L 40.5 218.0 L 30.5 199.0 L 22.5 177.0 L 19.5 159.0 L 18.5 158.0 L 16.0 126.5 L 14.5 137.0 L 13.5 138.0 L 13.5 170.0 L 14.5 171.0 L 14.5 180.0 L 15.5 181.0 L 16.5 192.0 L 20.5 204.0 L 20.5 207.0 L 26.5 222.0 L 34.5 237.0 L 49.5 258.0 L 66.0 274.5 L 87.0 289.5 L 113.0 301.5 L 116.0 301.5 L 119.0 303.5 L 128.0 305.5 L 132.5 299.0 L 134.5 291.0 L 134.5 272.0 L 133.5 271.0 L 133.5 265.0 L 130.5 252.0 L 121.5 230.0 L 117.5 224.0 L 97.5 203.0 L 90.5 193.0 L 82.5 178.0 L 79.5 168.0 L 77.5 165.0 L 72.5 144.0 L 71.5 119.0 L 73.0 117.5 L 77.0 117.5 L 78.0 116.5 L 82.0 116.5 L 83.0 117.5 L 99.0 117.5 L 100.0 118.5 L 106.0 118.5 L 107.0 119.5 L 112.0 119.5 L 113.0 120.5 L 130.0 123.5 L 146.0 129.5 L 146.0 132.5 L 140.0 132.5 L 139.0 131.5 L 125.0 130.5 L 117.0 128.5 L 109.0 128.5 L 108.0 127.5 L 81.0 126.5 L 81.5 142.0 L 82.5 143.0 L 82.5 148.0 L 84.5 156.0 L 90.5 173.0 L 104.5 196.0 L 124.0 216.5 L 143.0 230.5 L 153.0 235.5 L 160.0 234.5 L 179.0 222.5 L 193.0 210.5 L 196.0 206.5 L 199.5 207.0 L 198.5 211.0 L 189.5 224.0 L 176.0 236.5 L 165.0 243.5 L 160.0 245.5 L 153.0 245.5 L 142.0 241.5 L 138.0 238.5 L 135.5 239.0 L 140.5 254.0 L 140.5 258.0 L 142.5 264.0 L 142.5 274.0 L 143.5 275.0 L 142.5 293.0 L 138.5 307.0 L 142.0 308.5 L 160.0 308.5 L 161.0 309.5 L 174.0 308.5 L 176.5 307.0 L 184.5 295.0 L 195.5 274.0 L 205.5 247.0 L 209.5 231.0 L 211.5 212.0 L 212.5 211.0 L 212.5 200.0 L 211.5 199.0 L 210.5 185.0 L 206.5 171.0 L 201.5 160.0 L 192.5 146.0 L 175.0 128.5 L 165.0 121.5 L 144.0 111.5 L 126.0 105.5 L 106.0 100.5 L 90.5 98.0 L 91.0 95.5 L 112.0 95.5 L 113.0 96.5 L 121.0 96.5 L 122.0 97.5 L 132.0 98.5 L 136.0 100.5 L 146.0 102.5 L 156.0 106.5 L 170.0 114.5 L 172.0 114.5 L 189.0 127.5 L 202.5 143.0 L 214.5 166.0 L 220.5 190.0 L 220.5 221.0 L 219.5 222.0 L 219.5 228.0 L 218.5 229.0 L 216.5 243.0 L 204.5 279.0 L 190.0 306.5 L 200.0 304.5 L 211.5 300.0 L 222.5 279.0 L 232.5 250.0 L 235.5 237.0 L 235.5 232.0 L 236.5 231.0 L 236.5 225.0 L 237.5 224.0 L 238.5 199.0 L 237.5 198.0 L 237.5 189.0 L 236.5 188.0 L 235.5 176.0 L 229.5 156.0 L 220.5 138.0 L 209.5 123.0 L 197.0 111.5 L 182.0 100.5 L 163.0 90.5 L 148.0 84.5 L 145.0 84.5 L 142.0 82.5 L 122.0 77.5 L 101.0 74.5 L 100.5 72.0 L 125.0 71.5 L 126.0 72.5 L 133.0 72.5 L 154.0 77.5 L 157.0 79.5 L 170.0 83.5 L 186.0 91.5 L 203.0 103.5 L 220.5 121.0 L 230.5 136.0 L 238.5 152.0 L 243.5 168.0 L 245.5 183.0 L 246.5 184.0 L 247.5 211.0 L 246.5 212.0 L 245.5 232.0 L 244.5 233.0 L 244.5 238.0 L 240.5 255.0 L 232.5 279.0 L 226.0 293.5 L 239.0 286.5 L 252.5 276.0 L 260.5 249.0 L 260.5 245.0 L 263.5 234.0 L 263.5 228.0 L 264.5 227.0 L 264.5 218.0 L 265.5 217.0 L 265.5 191.0 L 264.5 190.0 L 264.5 182.0 L 263.5 181.0 L 262.5 170.0 L 255.5 146.0 L 247.5 129.0 L 238.5 115.0 L 231.5 106.0 L 209.0 84.5 L 198.0 76.5 L 176.0 64.5 L 149.0 54.5 L 129.0 50.5 L 128.5 48.0 L 137.0 47.5 L 138.0 48.5 L 146.0 48.5 L 147.0 49.5 L 162.0 51.5 L 180.0 57.5 L 198.0 66.5 L 213.0 76.5 L 225.0 86.5 L 235.5 97.0 L 250.5 116.0 L 261.5 136.0 L 264.5 146.0 L 266.5 149.0 L 270.5 163.0 L 272.5 179.0 L 273.5 180.0 L 273.5 187.0 L 274.5 188.0 L 274.5 223.0 L 273.5 224.0 L 272.5 238.0 L 268.5 253.0 L 268.5 257.0 L 266.5 261.0 L 267.0 262.5 L 279.5 248.0 L 291.5 228.0 L 291.5 208.0 L 292.5 205.0 L 291.5 204.0 L 291.5 186.0 L 290.5 185.0 L 289.5 170.0 L 283.5 144.0 L 275.5 123.0 L 266.5 106.0 L 255.5 90.0 L 231.0 65.5 L 205.0 47.5 L 176.0 33.5 L 159.0 28.5 L 158.5 26.0 L 165.0 25.5 L 166.0 26.5 L 172.0 26.5 L 184.0 29.5 L 209.0 39.5 L 228.0 51.5 L 242.0 62.5 L 263.5 85.0 L 278.5 107.0 L 286.5 123.0 L 292.5 139.0 L 292.5 142.0 L 297.5 158.0 L 298.5 170.0 L 299.5 171.0 L 300.5 195.0 L 301.5 196.0 L 300.5 199.0 L 301.0 206.5 L 305.5 188.0 L 306.5 176.0 L 307.5 175.0 L 307.5 150.0 L 306.5 149.0 L 306.5 141.0 L 305.5 140.0 L 304.5 129.0 L 301.5 121.0 L 300.5 114.0 L 296.5 104.0 L 291.5 93.0 L 282.5 78.0 L 272.5 64.0 L 250.0 42.5 L 234.0 31.5 L 216.0 22.5 L 202.0 17.5 L 185.0 13.5 L 180.0 13.5 L 179.0 12.5 Z M 145.0 3.5 L 144.0 4.5 L 136.0 4.5 L 135.0 5.5 L 121.0 7.5 L 114.0 10.5 L 111.0 10.5 L 83.0 23.5 L 69.0 32.5 L 57.0 42.5 L 39.5 61.0 L 26.5 80.0 L 17.5 98.0 L 11.5 115.0 L 7.5 132.0 L 6.5 147.0 L 5.5 148.0 L 5.5 174.0 L 6.5 175.0 L 6.5 184.0 L 7.5 185.0 L 7.5 190.0 L 10.5 203.0 L 16.5 221.0 L 22.5 234.0 L 38.5 259.0 L 60.0 281.5 L 88.0 300.5 L 115.0 311.5 L 131.0 315.5 L 136.0 315.5 L 137.0 316.5 L 175.0 316.5 L 184.0 316.5 L 185.0 315.5 L 190.0 315.5 L 206.0 311.5 L 222.0 305.5 L 238.0 297.5 L 253.0 287.5 L 264.0 278.5 L 281.5 260.0 L 297.5 236.0 L 307.5 213.0 L 307.5 210.0 L 310.5 203.0 L 313.5 190.0 L 313.5 184.0 L 314.5 183.0 L 314.5 138.0 L 313.5 137.0 L 312.5 126.0 L 307.5 111.0 L 307.5 108.0 L 300.5 93.0 L 300.5 91.0 L 298.5 89.0 L 294.5 80.0 L 281.5 61.0 L 257.0 36.5 L 238.0 23.5 L 222.0 15.5 L 202.0 8.5 L 189.0 5.5 L 175.0 4.5 L 174.0 3.5 Z" />
                    </svg>
                </div>
            </div>

        </div>

    </div>
</div>

<style>
    /* Ambient Radial Glow */
    .loader-ambient-glow {
        background: radial-gradient(circle at center, rgba(194, 158, 109, 0.12) 0%, rgba(250, 248, 245, 0.95) 55%, #FAF8F5 100%);
    }

    /* Circular Progress Rotation */
    .loader-progress-svg {
        animation: luxuryLoaderSpin 2s linear infinite;
        transform-origin: center center;
    }

    /* Circular Progress Dash Stretch */
    .loader-progress-spinner {
        animation: luxuryLoaderDash 1.8s ease-in-out infinite;
        transform-origin: center center;
    }

    @keyframes luxuryLoaderSpin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes luxuryLoaderDash {
        0% {
            stroke-dasharray: 24 276.46;
            stroke-dashoffset: 0;
        }
        50% {
            stroke-dasharray: 195 276.46;
            stroke-dashoffset: -45;
        }
        100% {
            stroke-dasharray: 24 276.46;
            stroke-dashoffset: -276.46;
        }
    }

    /* Subtle Breathing on the Emblem */
    .loader-logo-pulse {
        animation: luxuryLogoPulse 2.4s ease-in-out infinite;
    }

    @keyframes luxuryLogoPulse {
        0%, 100% {
            transform: scale(0.96);
            opacity: 0.92;
        }
        50% {
            transform: scale(1.02);
            opacity: 1;
        }
    }

    /* Exit Transition */
    .global-luxury-loader {
        transition: opacity 0.55s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.55s cubic-bezier(0.16, 1, 0.3, 1),
                    filter 0.55s cubic-bezier(0.16, 1, 0.3, 1),
                    visibility 0.55s ease;
        will-change: opacity, transform, filter;
    }

    .global-luxury-loader.loader-dismissed {
        opacity: 0 !important;
        transform: scale(1.04) !important;
        filter: blur(4px) !important;
        pointer-events: none !important;
    }
</style>

<script>
(function() {
    let dismissed = false;

    function dismissGlobalLoader() {
        if (dismissed) return;
        dismissed = true;

        const loader = document.getElementById('globalLuxuryLoader');
        if (!loader) return;

        loader.classList.add('loader-dismissed');
        loader.setAttribute('aria-hidden', 'true');

        // Trigger home entrance animation in perfect sync with the loader dissolving
        document.body.classList.add('site-entered');
        window.dispatchEvent(new CustomEvent('hairline:site-entered'));

        setTimeout(function() {
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
            document.body.classList.remove('has-loader');
        }, 600);
    }

    // Dismiss when DOM & critical assets are fully loaded (with slight aesthetic pause)
    if (document.readyState === 'complete') {
        setTimeout(dismissGlobalLoader, 350);
    } else {
        window.addEventListener('load', function() {
            setTimeout(dismissGlobalLoader, 400);
        });
    }

    // Safety timeout fallback (e.g. if external analytics or fonts stall)
    setTimeout(dismissGlobalLoader, 2500);
})();
</script>
