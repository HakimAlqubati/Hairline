<div class="hl-auth-root" dir="{{ __('filament-panels::layout.direction') ?? 'rtl' }}">
    <style>
        /* ============================================================
           HAIRLINE LUXURY SALON — CUSTOM AUTHENTICATION DESIGN
           Palette: Hairline Gold (#C29E6D), Warm Cream (#FBF9F5),
           Deep Espresso (#211C18), Soft Sand (#F5F0E8)
           ============================================================ */

        .hl-auth-root {
            min-height: 100vh;
            width: 100%;
            background-color: #FBF9F5;
            color: #211C18;
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            display: flex;
            position: relative;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        .hl-auth-root * {
            box-sizing: border-box;
        }

        .hl-auth-container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ------------------------------------------------------------
           Right/Showcase Section (Desktop >= 1024px)
           ------------------------------------------------------------ */
        .hl-auth-showcase {
            display: none;
        }

        @media (min-width: 1024px) {
            .hl-auth-showcase {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 56%;
                position: relative;
                padding: 3.5rem 4rem;
                overflow: hidden;
                background-color: #211C18;
                border-inline-end: 1px solid rgba(194, 158, 109, 0.25);
            }
        }

        .hl-showcase-photo {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/salon/reception-desk.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transform: scale(1.02);
            transition: transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1;
        }

        .hl-auth-showcase:hover .hl-showcase-photo {
            transform: scale(1.05);
        }

        /* Luxury Warm Golden Overlay (NOT black) */
        .hl-showcase-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(33, 28, 24, 0.90) 0%,
                rgba(33, 28, 24, 0.55) 45%,
                rgba(194, 158, 109, 0.30) 80%,
                rgba(33, 28, 24, 0.40) 100%
            );
            z-index: 2;
        }

        .hl-showcase-radial {
            position: absolute;
            inset: 0;
            background: radial-gradient(
                ellipse at top right,
                rgba(194, 158, 109, 0.35) 0%,
                transparent 65%
            );
            z-index: 3;
        }

        .hl-showcase-content {
            position: relative;
            z-index: 10;
        }

        .hl-showcase-title {
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.25;
            color: #FFFFFF;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.35);
        }

        @media (min-width: 1280px) {
            .hl-showcase-title {
                font-size: 3.25rem;
            }
        }

        .hl-showcase-desc {
            color: #F6EFE2;
            font-size: 1.15rem;
            line-height: 1.7;
            max-width: 32rem;
            font-weight: 400;
            text-shadow: 0 1px 6px rgba(0, 0, 0, 0.25);
        }

        .hl-showcase-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #E6D5B8;
            position: relative;
            z-index: 10;
        }

        /* ------------------------------------------------------------
           Left/Form Section (Desktop & Mobile) — WARM LIGHT PALETTE
           ------------------------------------------------------------ */
        .hl-auth-form-side {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.25rem 1.5rem;
            background-color: #FBF9F5;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 640px) {
            .hl-auth-form-side {
                padding: 3rem 2.5rem;
            }
        }

        @media (min-width: 1024px) {
            .hl-auth-form-side {
                width: 44%;
                padding: 3.5rem 3.5rem;
                background-color: #FBF9F5;
                overflow-y: auto;
            }
        }

        /* Mobile Ambient Background */
        .hl-mobile-ambient {
            display: block;
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/salon/reception-desk.jpg') }}');
            background-size: cover;
            background-position: center;
            filter: blur(12px) brightness(0.92);
            transform: scale(1.08);
            z-index: 1;
            pointer-events-none;
        }

        .hl-mobile-ambient-overlay {
            display: block;
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(251, 249, 245, 0.88) 0%, rgba(245, 240, 232, 0.94) 100%);
            z-index: 2;
            pointer-events-none;
        }

        @media (min-width: 1024px) {
            .hl-mobile-ambient,
            .hl-mobile-ambient-overlay {
                display: none;
            }
        }

        /* Top Header Bar */
        .hl-form-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            width: 100%;
            max-width: 420px;
            margin: 0 auto 1.5rem;
            position: relative;
            z-index: 10;
        }

        .hl-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #A3803B;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .hl-back-link:hover {
            color: #C29E6D;
        }

        /* Form Card */
        .hl-form-card {
            width: 100%;
            max-width: 420px;
            margin: auto;
            position: relative;
            z-index: 10;
        }

        @media (max-width: 1023px) {
            .hl-form-card {
                background: #FFFFFF;
                border: 1px solid rgba(194, 158, 109, 0.25);
                border-radius: 24px;
                box-shadow: 0 20px 45px -10px rgba(33, 28, 24, 0.10);
                padding: 2.5rem 2rem;
                margin-top: 1.5rem;
                margin-bottom: 1.5rem;
            }
        }

        /* SINGLE Logo — Precise Dimensions */
        .hl-brand-logo-container {
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .hl-brand-logo {
            max-height: 56px;
            width: auto;
            max-width: 200px;
            margin: 0 auto;
            display: block;
            object-fit: contain;
        }

        .hl-heading {
            font-size: 1.75rem;
            font-weight: 800;
            color: #211C18;
            margin-top: 0;
            margin-bottom: 0.4rem;
            letter-spacing: -0.015em;
            text-align: center;
        }

        .hl-subheading {
            font-size: 0.925rem;
            color: #6E6259;
            margin-bottom: 2rem;
            line-height: 1.55;
            text-align: center;
        }

        /* ------------------------------------------------------------
           FILAMENT FORM STYLING OVERRIDES (Crisp, High Contrast, Luxury)
           ------------------------------------------------------------ */
        .hl-form-wrapper {
            width: 100%;
        }

        /* Labels — Crystal Clear & High Contrast (NOT hidden) */
        .hl-form-wrapper .fi-fo-field-wrp-label,
        .hl-form-wrapper label,
        .hl-form-wrapper .fi-fo-field-wrp-label label {
            color: #211C18 !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.45rem !important;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .hl-form-wrapper .fi-fo-field-wrp {
            margin-bottom: 1.35rem !important;
        }

        /* Inputs — Clean White with Soft Gold Border */
        .hl-form-wrapper .fi-input-wrp {
            background-color: #FFFFFF !important;
            border: 1.5px solid #D8CFBF !important;
            border-radius: 12px !important;
            box-shadow: 0 1px 3px rgba(33, 28, 24, 0.04) !important;
            transition: all 0.2s ease !important;
        }

        .hl-form-wrapper .fi-input-wrp:focus-within {
            border-color: #C29E6D !important;
            box-shadow: 0 0 0 3px rgba(194, 158, 109, 0.25) !important;
        }

        .hl-form-wrapper input[type="email"],
        .hl-form-wrapper input[type="password"],
        .hl-form-wrapper input[type="text"] {
            color: #211C18 !important;
            background-color: transparent !important;
            font-size: 0.95rem !important;
            padding: 0.75rem 0.95rem !important;
        }

        .hl-form-wrapper input::placeholder {
            color: #94887D !important;
        }

        /* Remember Me Checkbox & Text */
        .hl-form-wrapper .fi-fo-checkbox label span,
        .hl-form-wrapper .fi-checkbox-label {
            color: #52473E !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
        }

        .hl-form-wrapper input[type="checkbox"] {
            border-radius: 6px !important;
            border-color: #C29E6D !important;
            color: #C29E6D !important;
        }

        /* Validation Error Messages — High Visibility Red */
        .hl-form-wrapper .fi-fo-field-wrp-error-message,
        .hl-form-wrapper .text-danger-600,
        .hl-form-wrapper [class*="text-danger"] {
            color: #DC2626 !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
            margin-top: 0.4rem !important;
            display: block !important;
        }

        /* Submit Button — Luxurious Hairline Gold */
        .hl-form-wrapper .fi-btn {
            width: 100% !important;
            background: linear-gradient(135deg, #C29E6D 0%, #A3803B 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            font-size: 1.05rem !important;
            padding: 0.85rem 1.5rem !important;
            border-radius: 12px !important;
            border: none !important;
            box-shadow: 0 4px 20px rgba(194, 158, 109, 0.35) !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            margin-top: 0.5rem !important;
        }

        .hl-form-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #CCA876 0%, #B08D46 100%) !important;
            box-shadow: 0 6px 24px rgba(194, 158, 109, 0.45) !important;
            transform: translateY(-1px) !important;
        }

        .hl-form-wrapper .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 10px rgba(194, 158, 109, 0.3) !important;
        }

        /* Footer */
        .hl-form-footer {
            width: 100%;
            max-width: 420px;
            margin: 2rem auto 0;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(194, 158, 109, 0.2);
            text-align: center;
            font-size: 0.85rem;
            color: #8C8276;
            position: relative;
            z-index: 10;
        }
    </style>

    {{-- Mobile Ambient Layer --}}
    <div class="hl-mobile-ambient" aria-hidden="true"></div>
    <div class="hl-mobile-ambient-overlay" aria-hidden="true"></div>

    <div class="hl-auth-container">
        {{-- Right/Visual Showcase Column (Desktop Only >= 1024px) --}}
        <div class="hl-auth-showcase">
            <div class="hl-showcase-photo" aria-hidden="true"></div>
            <div class="hl-showcase-overlay" aria-hidden="true"></div>
            <div class="hl-showcase-radial" aria-hidden="true"></div>

            {{-- Center Showcase Greeting & Atmosphere (NO feature list) --}}
            <div class="hl-showcase-content" style="margin-top: auto; margin-bottom: auto; padding-top: 2rem; padding-bottom: 2rem;">
                <h1 class="hl-showcase-title">
                    {{ __('admin.brand.name') }}
                </h1>
                <p class="hl-showcase-desc">
                    {{ __('admin.auth.salon_quote') }}
                </p>
            </div>

            {{-- Bottom Showcase Footer --}}
            <div class="hl-showcase-footer">
                <span>{{ __('admin.auth.copyright') }} &copy; {{ date('Y') }}</span>
                <span style="color: #FAF8F5; opacity: 0.85;">Hairline Luxury Lounge</span>
            </div>
        </div>

        {{-- Left/Form Column (Warm Luxury Palette) --}}
        <div class="hl-auth-form-side">
            {{-- Top Bar (Return link & Language switcher) --}}
            <div class="hl-form-topbar">
                <a href="/" class="hl-back-link">
                    <span class="rtl:rotate-180" style="display: inline-block;">&rarr;</span>
                    <span>{{ __('admin.auth.back_to_website') }}</span>
                </a>

                @if (class_exists(\BezhanSalleh\LanguageSwitch\LanguageSwitch::class))
                    <div>
                        @livewire('language-switch-component')
                    </div>
                @endif
            </div>

            {{-- Main Form Card --}}
            <div class="hl-form-card">
                {{-- Single Brand Logo (Clean & Sized) --}}
                <div class="hl-brand-logo-container">
                    <a href="/" style="display: inline-block; text-decoration: none;">
                        <img
                            src="{{ asset('images/logo.png') }}"
                            alt="{{ __('admin.brand.name') }}"
                            class="hl-brand-logo"
                        />
                    </a>
                </div>

                <h2 class="hl-heading">
                    {{ $this->getHeading() }}
                </h2>
                <p class="hl-subheading">
                    {{ $this->getSubheading() }}
                </p>

                {{-- Filament Livewire Auth Form --}}
                <div class="hl-form-wrapper">
                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_START, scopes: $this->getRenderHookScopes()) }}

                    {{ $this->content }}

                    <x-filament-actions::modals />

                    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_PAGE_END, scopes: $this->getRenderHookScopes()) }}
                </div>
            </div>

            {{-- Bottom Footer --}}
            <div class="hl-form-footer">
                <span>{{ __('admin.auth.copyright') }} &copy; {{ date('Y') }}</span>
            </div>
        </div>
    </div>
</div>
