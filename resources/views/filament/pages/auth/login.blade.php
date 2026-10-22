<div class="hl-auth-root" dir="{{ __('filament-panels::layout.direction') ?? 'rtl' }}">
    <style>
        /* ============================================================
           HAIRLINE SALON — MODERN FULL-SCREEN GLASSMORPHIC LOGIN
           Palette (Strictly derived from Logo):
           - Hairline Champagne Gold: #C29E6D
           - Deep Antique Gold:       #A3803B
           - Warm Ivory / Cream:      #FBF9F5
           - Soft Sand:               #F2EAE0
           - Deep Espresso:           #211C18
           ============================================================ */

        .hl-auth-root {
            min-height: 100vh;
            min-height: 100dvh;
            width: 100%;
            background-color: #211C18;
            color: #211C18;
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow-x: hidden;
            box-sizing: border-box;
            padding: 1.25rem 1rem;
        }

        @media (min-width: 640px) {
            .hl-auth-root {
                padding: 1.75rem 2rem;
            }
        }

        .hl-auth-root *,
        .hl-auth-root *::before,
        .hl-auth-root *::after {
            box-sizing: border-box;
        }

        /* ------------------------------------------------------------
           Full-Screen Background Layer (Desktop Horizontal & Mobile Vertical)
           ------------------------------------------------------------ */
        .hl-bg-stage {
            position: fixed;
            inset: 0;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
            background-color: #211C18;
        }

        /* Mobile First: Vertical Salon Image (694x1024) */
        .hl-bg-photo {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/salon/hair-styling-chairs.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            transform: scale(1.03);
            transition: transform 1.4s cubic-bezier(0.16, 1, 0.3, 1);
            filter: brightness(0.92) contrast(1.03);
        }

        /* Tablet & Desktop: Horizontal Salon Reception Image (1024x682) */
        @media (min-width: 768px) {
            .hl-bg-photo {
                background-image: url('{{ asset('images/salon/reception-desk.jpg') }}');
                background-position: center center;
                filter: brightness(0.94) contrast(1.02);
            }
        }

        /* Warm Gold & Espresso Luxury Overlay */
        .hl-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                160deg,
                rgba(33, 28, 24, 0.52) 0%,
                rgba(33, 28, 24, 0.38) 45%,
                rgba(194, 158, 109, 0.28) 80%,
                rgba(33, 28, 24, 0.68) 100%
            );
            z-index: 2;
        }

        /* Subtle Golden Radial Glow behind the centered card */
        .hl-bg-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(
                circle at 50% 48%,
                rgba(251, 249, 245, 0.22) 0%,
                rgba(194, 158, 109, 0.18) 35%,
                transparent 70%
            );
            z-index: 3;
        }

        /* ------------------------------------------------------------
           TopSpacer / Viewport Balance
           ------------------------------------------------------------ */
        .hl-viewport-top {
            width: 100%;
            max-width: 1280px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            position: relative;
            z-index: 20;
            min-height: 0.5rem;
        }

        /* ------------------------------------------------------------
           Center Glassmorphic Card (Lightly Translucent & Ultra-Readable)
           ------------------------------------------------------------ */
        .hl-center-stage {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto 0;
            position: relative;
            z-index: 15;
            padding: 0.5rem 0;
        }

        .hl-glass-card {
            width: 100%;
            max-width: 450px;
            position: relative;
            /* Lightly translucent warm ivory glass matching Logo palette */
            background: linear-gradient(
                145deg,
                rgba(251, 249, 245, 0.85) 0%,
                rgba(245, 239, 230, 0.79) 100%
            );
            backdrop-filter: blur(18px) saturate(145%);
            -webkit-backdrop-filter: blur(18px) saturate(145%);
            border: 1.5px solid rgba(194, 158, 109, 0.48);
            border-radius: 28px;
            box-shadow:
                0 28px 65px -12px rgba(20, 16, 13, 0.52),
                0 0 0 1px rgba(255, 255, 255, 0.65) inset,
                0 8px 28px -6px rgba(194, 158, 109, 0.22);
            padding: 2.25rem 1.5rem 1.75rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        @media (min-width: 640px) {
            .hl-glass-card {
                padding: 2.6rem 2.5rem 2rem;
                border-radius: 30px;
            }
        }

        /* Subtle Decorative Gold Line at Top of Glass Card */
        .hl-glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            inset-inline: 22%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #C29E6D, #A3803B, #C29E6D, transparent);
            border-radius: 0 0 999px 999px;
            opacity: 0.85;
        }

        /* ------------------------------------------------------------
           Repositioned Language Switcher (Inside Top Corner of Glass Card)
           ------------------------------------------------------------ */
        .hl-card-lang-corner {
            position: absolute;
            top: 1.15rem;
            inset-inline-start: 1.15rem;
            z-index: 25;
        }

        @media (min-width: 640px) {
            .hl-card-lang-corner {
                top: 1.35rem;
                inset-inline-start: 1.5rem;
            }
        }

        /* Style Filament Language Switcher Trigger to match Logo Gold & Glass */
        .hl-card-lang-corner .fi-ls-trigger {
            background: rgba(255, 255, 255, 0.72) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(194, 158, 109, 0.45) !important;
            border-radius: 9999px !important;
            padding: 0.35rem 0.75rem !important;
            height: 2.15rem !important;
            color: #211C18 !important;
            box-shadow: 0 2px 8px rgba(33, 28, 24, 0.06) !important;
            transition: all 0.2s ease !important;
        }

        .hl-card-lang-corner .fi-ls-trigger:hover {
            background: rgba(255, 255, 255, 0.95) !important;
            border-color: #C29E6D !important;
            box-shadow: 0 4px 12px rgba(194, 158, 109, 0.25) !important;
            transform: translateY(-1px);
        }

        .hl-card-lang-corner .fi-ls-trigger span {
            color: #211C18 !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
        }

        .hl-card-lang-corner .fi-ls-trigger svg {
            color: #A3803B !important;
            width: 1.05rem !important;
            height: 1.05rem !important;
        }

        /* ------------------------------------------------------------
           Brand Logo & Header Typography
           ------------------------------------------------------------ */
        .hl-brand-logo-container {
            text-align: center;
            margin-bottom: 1.25rem;
            padding-top: 0.25rem;
        }

        .hl-brand-logo-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.85rem;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.55);
            border: 1px solid rgba(194, 158, 109, 0.28);
            box-shadow: 0 4px 15px rgba(194, 158, 109, 0.12);
            text-decoration: none;
            transition: transform 0.25s ease, background-color 0.25s ease;
        }

        .hl-brand-logo-link:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.78);
        }

        .hl-brand-logo {
            max-height: 62px;
            width: auto;
            max-width: 190px;
            display: block;
            object-fit: contain;
        }

        @media (max-width: 639px) {
            .hl-brand-logo {
                max-height: 54px;
                max-width: 165px;
            }
        }

        .hl-heading {
            font-size: 1.65rem;
            font-weight: 800;
            color: #211C18;
            margin: 0 0 0.35rem;
            letter-spacing: -0.015em;
            text-align: center;
        }

        @media (min-width: 640px) {
            .hl-heading {
                font-size: 1.8rem;
            }
        }

        .hl-subheading {
            font-size: 0.92rem;
            color: #5A4E44;
            font-weight: 500;
            margin: 0 0 1.65rem;
            line-height: 1.55;
            text-align: center;
        }

        /* ------------------------------------------------------------
           FILAMENT FORM STYLING OVERRIDES (Crisp, High Contrast, Gold)
           ------------------------------------------------------------ */
        .hl-form-wrapper {
            width: 100%;
        }

        /* Labels — High Contrast Espresso */
        .hl-form-wrapper .fi-fo-field-wrp-label,
        .hl-form-wrapper label,
        .hl-form-wrapper .fi-fo-field-wrp-label label {
            color: #211C18 !important;
            font-weight: 700 !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.4rem !important;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .hl-form-wrapper .fi-fo-field-wrp {
            margin-bottom: 1.15rem !important;
        }

        /* Inputs — Translucent Pure White with Warm Gold Border */
        .hl-form-wrapper .fi-input-wrp {
            background-color: rgba(255, 255, 255, 0.86) !important;
            border: 1.5px solid rgba(194, 158, 109, 0.42) !important;
            border-radius: 14px !important;
            box-shadow: 0 2px 6px rgba(33, 28, 24, 0.04) !important;
            transition: all 0.2s ease !important;
        }

        .hl-form-wrapper .fi-input-wrp:hover {
            background-color: rgba(255, 255, 255, 0.94) !important;
            border-color: rgba(194, 158, 109, 0.65) !important;
        }

        .hl-form-wrapper .fi-input-wrp:focus-within {
            background-color: #FFFFFF !important;
            border-color: #C29E6D !important;
            box-shadow: 0 0 0 3.5px rgba(194, 158, 109, 0.25) !important;
        }

        .hl-form-wrapper input[type="email"],
        .hl-form-wrapper input[type="password"],
        .hl-form-wrapper input[type="text"] {
            color: #211C18 !important;
            background-color: transparent !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
            padding: 0.72rem 0.95rem !important;
        }

        .hl-form-wrapper input::placeholder {
            color: #8C7E71 !important;
        }

        /* Password Reveal Icon Button */
        .hl-form-wrapper .fi-input-wrp button {
            color: #7A6B5D !important;
        }

        .hl-form-wrapper .fi-input-wrp button:hover {
            color: #A3803B !important;
        }

        /* Remember Me Checkbox & Text */
        .hl-form-wrapper .fi-fo-checkbox label span,
        .hl-form-wrapper .fi-checkbox-label {
            color: #3D342C !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
        }

        .hl-form-wrapper input[type="checkbox"] {
            border-radius: 6px !important;
            border-color: #C29E6D !important;
            color: #C29E6D !important;
            background-color: rgba(255, 255, 255, 0.9) !important;
        }

        /* Validation Error Messages */
        .hl-form-wrapper .fi-fo-field-wrp-error-message,
        .hl-form-wrapper .text-danger-600,
        .hl-form-wrapper [class*="text-danger"] {
            color: #B91C1C !important;
            font-size: 0.84rem !important;
            font-weight: 700 !important;
            margin-top: 0.35rem !important;
            display: block !important;
        }

        /* Submit Button — Signature Hairline Gold Gradient */
        .hl-form-wrapper .fi-btn {
            width: 100% !important;
            background: linear-gradient(135deg, #C29E6D 0%, #A3803B 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            font-size: 1.02rem !important;
            padding: 0.82rem 1.5rem !important;
            border-radius: 14px !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 8px 22px -4px rgba(163, 128, 59, 0.48) !important;
            cursor: pointer !important;
            transition: all 0.25s ease !important;
            margin-top: 0.35rem !important;
        }

        .hl-form-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #CCA876 0%, #B08D46 100%) !important;
            box-shadow: 0 10px 26px -4px rgba(163, 128, 59, 0.6) !important;
            transform: translateY(-1.5px) !important;
        }

        .hl-form-wrapper .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 12px rgba(163, 128, 59, 0.35) !important;
        }

        /* ------------------------------------------------------------
           Card Bottom Utility Link (Back to Website)
           ------------------------------------------------------------ */
        .hl-card-bottom-actions {
            margin-top: 1.35rem;
            padding-top: 1.1rem;
            border-top: 1px solid rgba(194, 158, 109, 0.28);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hl-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.86rem;
            font-weight: 700;
            color: #7D602B;
            text-decoration: none;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: rgba(194, 158, 109, 0.12);
            border: 1px solid rgba(194, 158, 109, 0.25);
            transition: all 0.2s ease;
        }

        .hl-back-link:hover {
            color: #211C18;
            background: rgba(194, 158, 109, 0.24);
            border-color: #C29E6D;
            transform: translateY(-1px);
        }

        /* ------------------------------------------------------------
           Full-Screen Bottom Footer Bar (Hairline Salon & Copyright)
           ------------------------------------------------------------ */
        .hl-screen-footer {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem 1.5rem;
            padding-top: 0.75rem;
            font-size: 0.84rem;
            font-weight: 500;
            color: rgba(251, 249, 245, 0.92);
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.45);
            position: relative;
            z-index: 15;
        }

        .hl-screen-footer-brand {
            color: #F5E6CE;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        @media (max-width: 639px) {
            .hl-screen-footer {
                flex-direction: column;
                justify-content: center;
                text-align: center;
                gap: 0.25rem;
                font-size: 0.78rem;
            }
        }
    </style>

    {{-- Full-Screen Background (Vertical Salon Image on Mobile, Horizontal on Desktop) --}}
    <div class="hl-bg-stage" aria-hidden="true">
        <div class="hl-bg-photo"></div>
        <div class="hl-bg-overlay"></div>
        <div class="hl-bg-glow"></div>
    </div>

    {{-- Top Spacer for vertical centering balance --}}
    <div class="hl-viewport-top" aria-hidden="true"></div>

    {{-- Center Glassmorphic Login Form Card --}}
    <main class="hl-center-stage">
        <div class="hl-glass-card">
            {{-- Repositioned Language Switcher (Top Corner of Glass Card) --}}
            @if (class_exists(\BezhanSalleh\LanguageSwitch\LanguageSwitch::class))
                <div class="hl-card-lang-corner">
                    @livewire('language-switch-component')
                </div>
            @endif

            {{-- Brand Logo --}}
            <div class="hl-brand-logo-container">
                <a href="/" class="hl-brand-logo-link" title="{{ __('admin.brand.name') }}">
                    <img
                        src="{{ asset('images/logo.png') }}"
                        alt="{{ __('admin.brand.name') }}"
                        class="hl-brand-logo"
                    />
                </a>
            </div>

            {{-- Heading & Subheading --}}
            <h1 class="hl-heading">
                {{ $this->getHeading() }}
            </h1>
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

            {{-- Return to Main Website Link --}}
            <div class="hl-card-bottom-actions">
                <a href="/" class="hl-back-link">
                    <span class="rtl:rotate-180" style="display: inline-block;">&rarr;</span>
                    <span>{{ __('admin.auth.back_to_website') }}</span>
                </a>
            </div>
        </div>
    </main>

    {{-- Bottom Footer Bar --}}
    <footer class="hl-screen-footer">
        <span>{{ __('admin.auth.copyright') }} &copy; {{ date('Y') }}</span>
        <span class="hl-screen-footer-brand">Hairline Salon</span>
    </footer>
</div>
