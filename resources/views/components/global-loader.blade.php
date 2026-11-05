@props([
    'salonIdentity' => [],
])

<!-- ============================================================
     HAIRLINE LUXURY PRELOADER (The Signature Pulse)
     ============================================================ -->
<div id="globalLuxuryLoader"
     class="global-luxury-loader fixed inset-0 z-[99999] flex flex-col items-center justify-center select-none bg-[#FAF8F5] overflow-hidden"
     aria-hidden="false"
     role="status"
     aria-label="جاري التحميل">
    
    <!-- Ambient luxury background glow -->
    <div class="loader-ambient-glow absolute inset-0 pointer-events-none"></div>

    <!-- Center Content Stack -->
    <div class="relative z-10 flex flex-col items-center text-center px-4">
        
        <!-- Animated Logo Emblem with Concentric Breathing Rings -->
        <div class="relative flex items-center justify-center w-24 h-24 sm:w-28 sm:h-28 mb-6">
            <!-- Outermost Pulse Ring -->
            <div class="loader-pulse-ring-outer absolute inset-0 rounded-full pointer-events-none"></div>
            <!-- Middle Accent Ring -->
            <div class="loader-pulse-ring-inner absolute inset-1.5 rounded-full pointer-events-none"></div>

            <!-- Circular Logo Frame -->
            <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white p-2.5 shadow-[0_12px_32px_rgba(33,28,24,0.08)] border-2 border-primary/25 ring-4 ring-primary/5 flex items-center justify-center overflow-hidden transition-transform duration-700">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ $salonIdentity['name_ar'] ?? 'هيرلاين' }} - {{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}"
                     class="w-full h-full object-contain filter drop-shadow-xs"
                     loading="eager">
            </div>
        </div>

        <!-- Salon Titles -->
        <div class="flex flex-col items-center space-y-1">
            <h1 class="text-xl sm:text-2xl font-black text-sage-dark tracking-tight font-sans">
                {{ $salonIdentity['name_ar'] ?? 'هيرلاين' }}
            </h1>
            <div class="flex items-center gap-2 text-xs sm:text-[13px] font-bold font-serif tracking-[0.24em] text-primary uppercase">
                <span>{{ $salonIdentity['name_en'] ?? 'HAIRLINE' }}</span>
                <span class="w-1 h-1 rounded-full bg-primary/60 inline-block"></span>
                <span>{{ $salonIdentity['tagline_en'] ?? 'BEAUTY SALON' }}</span>
            </div>
        </div>

        <!-- Ultra-minimalist luxury gold progress line -->
        <div class="w-32 sm:w-40 h-[2px] bg-primary/15 rounded-full mt-7 overflow-hidden relative">
            <div class="loader-gold-bar absolute top-0 left-0 h-full w-1/2 rounded-full"></div>
        </div>

    </div>
</div>

<style>
    /* Ambient Radial Glow */
    .loader-ambient-glow {
        background: radial-gradient(circle at center, rgba(194, 158, 109, 0.12) 0%, rgba(250, 248, 245, 0.95) 55%, #FAF8F5 100%);
    }

    /* Outermost Expanding Ring */
    @keyframes luxuryPulseOuter {
        0% {
            transform: scale(0.92);
            opacity: 0.2;
            border: 1.5px solid var(--color-primary, #C29E6D);
        }
        50% {
            transform: scale(1.18);
            opacity: 0.65;
            border: 2px solid var(--color-primary, #C29E6D);
            box-shadow: 0 0 20px rgba(194, 158, 109, 0.2);
        }
        100% {
            transform: scale(0.92);
            opacity: 0.2;
            border: 1.5px solid var(--color-primary, #C29E6D);
        }
    }

    /* Inner Echo Ring */
    @keyframes luxuryPulseInner {
        0% {
            transform: scale(0.98);
            opacity: 0.35;
            border: 1px dashed var(--color-primary, #C29E6D);
        }
        50% {
            transform: scale(1.08);
            opacity: 0.75;
            border: 1px dashed var(--color-primary, #C29E6D);
        }
        100% {
            transform: scale(0.98);
            opacity: 0.35;
            border: 1px dashed var(--color-primary, #C29E6D);
        }
    }

    /* Shimmering Progress Bar Motion */
    @keyframes luxuryProgressAnim {
        0% {
            left: -50%;
            width: 25%;
        }
        50% {
            width: 55%;
        }
        100% {
            left: 100%;
            width: 25%;
        }
    }

    .loader-pulse-ring-outer {
        animation: luxuryPulseOuter 2.6s infinite cubic-bezier(0.4, 0, 0.2, 1);
    }

    .loader-pulse-ring-inner {
        animation: luxuryPulseInner 2.6s infinite cubic-bezier(0.4, 0, 0.2, 1) reverse;
    }

    .loader-gold-bar {
        background: linear-gradient(90deg, transparent 0%, var(--color-primary, #C29E6D) 50%, var(--color-gold-light, #F6EFE2) 80%, var(--color-primary, #C29E6D) 100%);
        box-shadow: 0 0 8px rgba(194, 158, 109, 0.4);
        animation: luxuryProgressAnim 1.6s infinite cubic-bezier(0.45, 0, 0.25, 1);
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
        transform: scale(1.03) !important;
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

        setTimeout(function() {
            if (loader.parentNode) {
                loader.parentNode.removeChild(loader);
            }
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
