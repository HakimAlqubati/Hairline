<div class="hl-auth-root" dir="{{ __('filament-panels::layout.direction') ?? 'rtl' }}">
    <style>
        /* ============================================================
           HAIRLINE SALON — COMPACT LUXURY GLASSMORPHISM LOGIN
           Palette (Strictly derived from Logo):
           - Hairline Champagne Gold: #C29E6D
           - Deep Antique Gold:       #A3803B
           - Warm Ivory / Cream:      #FBF9F5
           - Deep Espresso:           #1E1915
           ============================================================ */

        .hl-auth-root {
            height: 100vh;
            height: 100dvh;
            width: 100%;
            background-color: #1E1915;
            color: #1E1915;
            font-family: 'Tajawal', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            padding: 1rem;
        }

        @media (max-height: 640px) {
            .hl-auth-root {
                height: auto;
                min-height: 100dvh;
                overflow-y: auto;
                padding: 1.25rem 1rem 3rem;
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
            background-color: #1E1915;
        }

        /* Mobile First: Vertical Salon Image (694x1024) */
        .hl-bg-photo {
            position: absolute;
            inset: 0;
            background-image: url('{{ asset('images/salon/hair-styling-chairs.jpg') }}');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            transform: scale(1.02);
            filter: brightness(0.95) contrast(1.04);
        }

        /* Tablet & Desktop: Horizontal Salon Reception Image (1024x682) */
        @media (min-width: 768px) {
            .hl-bg-photo {
                background-image: url('{{ asset('images/salon/reception-desk.jpg') }}');
                background-position: center center;
                filter: brightness(0.96) contrast(1.03);
            }
        }

        /* Balanced Warm Overlay — Keeps background photo clearly visible */
        .hl-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                165deg,
                rgba(30, 25, 21, 0.34) 0%,
                rgba(30, 25, 21, 0.18) 45%,
                rgba(194, 158, 109, 0.16) 75%,
                rgba(30, 25, 21, 0.52) 100%
            );
            z-index: 2;
        }

        /* Soft Ambient Vignette */
        .hl-bg-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(
                circle at 50% 50%,
                rgba(255, 252, 245, 0.10) 0%,
                transparent 65%
            );
            z-index: 3;
        }

        /* ------------------------------------------------------------
           Center Glassmorphic Card (True Translucent Glass + Compact)
           ------------------------------------------------------------ */
        .hl-center-stage {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 15;
        }

        .hl-glass-card {
            width: 100%;
            max-width: 420px;
            position: relative;
            /* True Translucent Frosted Glass — Background photo shows through */
            background: linear-gradient(
                135deg,
                rgba(253, 250, 245, 0.62) 0%,
                rgba(247, 240, 229, 0.48) 50%,
                rgba(243, 233, 218, 0.56) 100%
            );
            backdrop-filter: blur(14px) saturate(160%);
            -webkit-backdrop-filter: blur(14px) saturate(160%);
            border: 1px solid rgba(255, 255, 255, 0.68);
            border-radius: 26px;
            box-shadow:
                0 24px 50px -12px rgba(18, 14, 10, 0.42),
                0 0 0 1px rgba(194, 158, 109, 0.38),
                inset 0 1px 1px rgba(255, 255, 255, 0.85),
                inset 0 -1px 1px rgba(194, 158, 109, 0.22);
            padding: 1.5rem 1.5rem 1.25rem;
        }

        @media (min-width: 640px) {
            .hl-glass-card {
                padding: 1.75rem 2.15rem 1.35rem;
            }
        }

        /* Luminous Gold Specular Line at Top of Glass Card */
        .hl-glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            inset-inline: 24%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent, #C29E6D, #A3803B, #C29E6D, transparent);
            border-radius: 0 0 999px 999px;
            opacity: 0.9;
        }

        /* ------------------------------------------------------------
           Repositioned Language Switcher (Top Corner of Glass Card)
           ------------------------------------------------------------ */
        .hl-card-lang-corner {
            position: absolute;
            top: 1rem;
            inset-inline-start: 1.1rem;
            z-index: 25;
        }

        .hl-card-lang-corner .fi-ls-trigger {
            background: rgba(255, 255, 255, 0.65) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            border: 1px solid rgba(194, 158, 109, 0.48) !important;
            border-radius: 9999px !important;
            padding: 0.3rem 0.65rem !important;
            height: 2rem !important;
            color: #1E1915 !important;
            box-shadow: 0 2px 8px rgba(30, 25, 21, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
            transition: all 0.2s ease !important;
        }

        .hl-card-lang-corner .fi-ls-trigger:hover {
            background: rgba(255, 255, 255, 0.92) !important;
            border-color: #C29E6D !important;
            box-shadow: 0 4px 12px rgba(194, 158, 109, 0.28) !important;
            transform: translateY(-1px);
        }

        .hl-card-lang-corner .fi-ls-trigger span {
            color: #1E1915 !important;
            font-weight: 700 !important;
            font-size: 0.78rem !important;
        }

        .hl-card-lang-corner .fi-ls-trigger svg {
            color: #9A7632 !important;
            width: 1rem !important;
            height: 1rem !important;
        }

        /* ------------------------------------------------------------
           Brand Logo & Compact Subtitle (No redundant welcome heading)
           ------------------------------------------------------------ */
        .hl-brand-logo-container {
            text-align: center;
            margin-bottom: 0.65rem;
        }

        .hl-brand-logo-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.45rem 0.9rem;
            border-radius: 16px;
            background: rgba(255, 253, 250, 0.68);
            border: 1px solid rgba(255, 255, 255, 0.78);
            box-shadow:
                0 4px 14px rgba(30, 25, 21, 0.06),
                0 0 0 1px rgba(194, 158, 109, 0.25);
            text-decoration: none;
            transition: transform 0.25s ease, background-color 0.25s ease;
        }

        .hl-brand-logo-link:hover {
            transform: translateY(-1.5px);
            background: rgba(255, 255, 255, 0.86);
        }

        .hl-brand-logo {
            max-height: 52px;
            width: auto;
            max-width: 165px;
            display: block;
            object-fit: contain;
        }

        @media (max-width: 639px) {
            .hl-brand-logo {
                max-height: 46px;
                max-width: 150px;
            }
        }

        .hl-subheading {
            font-size: 0.95rem;
            color: #1E1915;
            font-weight: 700;
            margin: 0 0 1.1rem;
            line-height: 1.5;
            text-align: center;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.65);
        }

        /* ------------------------------------------------------------
           FILAMENT FORM STYLING OVERRIDES (Compact, Glassy & Crisp)
           ------------------------------------------------------------ */
        .hl-form-wrapper {
            width: 100%;
        }

        /* Tighten Filament's default large vertical gaps */
        .hl-form-wrapper form,
        .hl-form-wrapper .fi-sc,
        .hl-form-wrapper .fi-fo-component-ctn,
        .hl-form-wrapper [class*="gap-y-"] {
            gap: 0.75rem !important;
            row-gap: 0.75rem !important;
        }

        .hl-form-wrapper .fi-fo-field-wrp {
            margin-bottom: 0 !important;
        }

        /* Labels — High Contrast Espresso with subtle light halo for readability */
        .hl-form-wrapper .fi-fo-field-wrp-label,
        .hl-form-wrapper label,
        .hl-form-wrapper .fi-fo-field-wrp-label label {
            color: #1E1915 !important;
            font-weight: 700 !important;
            font-size: 0.86rem !important;
            margin-bottom: 0.25rem !important;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.6);
        }

        /* Inputs — Frosted Glass White with Warm Gold Border */
        .hl-form-wrapper .fi-input-wrp {
            background-color: rgba(255, 255, 255, 0.76) !important;
            backdrop-filter: blur(6px) !important;
            -webkit-backdrop-filter: blur(6px) !important;
            border: 1px solid rgba(194, 158, 109, 0.5) !important;
            border-radius: 12px !important;
            box-shadow:
                0 2px 6px rgba(30, 25, 21, 0.04),
                inset 0 1px 1px rgba(255, 255, 255, 0.85) !important;
            transition: all 0.2s ease !important;
        }

        .hl-form-wrapper .fi-input-wrp:hover {
            background-color: rgba(255, 255, 255, 0.88) !important;
            border-color: rgba(163, 128, 59, 0.7) !important;
        }

        .hl-form-wrapper .fi-input-wrp:focus-within {
            background-color: rgba(255, 255, 255, 0.96) !important;
            border-color: #B08A48 !important;
            box-shadow: 0 0 0 3px rgba(194, 158, 109, 0.28) !important;
        }

        .hl-form-wrapper input[type="email"],
        .hl-form-wrapper input[type="password"],
        .hl-form-wrapper input[type="text"] {
            color: #1E1915 !important;
            background-color: transparent !important;
            font-size: 0.92rem !important;
            font-weight: 600 !important;
            padding: 0.56rem 0.85rem !important;
        }

        .hl-form-wrapper input::placeholder {
            color: #7D7064 !important;
        }

        /* Password Reveal Icon Button */
        .hl-form-wrapper .fi-input-wrp button {
            color: #6E5F51 !important;
        }

        .hl-form-wrapper .fi-input-wrp button:hover {
            color: #9A7632 !important;
        }

        /* Remember Me Checkbox & Text */
        .hl-form-wrapper .fi-fo-checkbox label span,
        .hl-form-wrapper .fi-checkbox-label {
            color: #1E1915 !important;
            font-weight: 700 !important;
            font-size: 0.84rem !important;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.55);
        }

        .hl-form-wrapper input[type="checkbox"] {
            border-radius: 5px !important;
            border-color: #B08A48 !important;
            color: #A3803B !important;
            background-color: rgba(255, 255, 255, 0.85) !important;
        }

        /* Validation Error Messages */
        .hl-form-wrapper .fi-fo-field-wrp-error-message,
        .hl-form-wrapper .text-danger-600,
        .hl-form-wrapper [class*="text-danger"] {
            color: #B91C1C !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            margin-top: 0.25rem !important;
            display: block !important;
            text-shadow: 0 1px 1px rgba(255, 255, 255, 0.7);
        }

        /* Submit Button — Signature Hairline Gold Gradient */
        .hl-form-wrapper .fi-btn {
            width: 100% !important;
            background: linear-gradient(135deg, #C29E6D 0%, #9E7B36 100%) !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            font-size: 0.96rem !important;
            padding: 0.68rem 1.25rem !important;
            border-radius: 12px !important;
            border: 1px solid rgba(255, 255, 255, 0.35) !important;
            box-shadow:
                0 8px 20px -4px rgba(158, 123, 54, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
            cursor: pointer !important;
            transition: all 0.22s ease !important;
            margin-top: 0.15rem !important;
        }

        .hl-form-wrapper .fi-btn:hover {
            background: linear-gradient(135deg, #CCA876 0%, #AD8940 100%) !important;
            box-shadow: 0 10px 24px -4px rgba(158, 123, 54, 0.62) !important;
            transform: translateY(-1px) !important;
        }

        .hl-form-wrapper .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 12px rgba(158, 123, 54, 0.38) !important;
        }

        /* ------------------------------------------------------------
           Card Bottom Utility Link (Back to Website — Compact)
           ------------------------------------------------------------ */
        .hl-card-bottom-actions {
            margin-top: 0.9rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(194, 158, 109, 0.32);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hl-back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #5C4419;
            text-decoration: none;
            padding: 0.28rem 0.8rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.48);
            border: 1px solid rgba(194, 158, 109, 0.38);
            transition: all 0.2s ease;
        }

        .hl-back-link:hover {
            color: #1E1915;
            background: rgba(255, 255, 255, 0.82);
            border-color: #C29E6D;
            transform: translateY(-1px);
        }

        /* ------------------------------------------------------------
           Full-Screen Bottom Footer Bar (Pinned so it never causes scroll)
           ------------------------------------------------------------ */
        .hl-screen-footer {
            position: fixed;
            bottom: 0.75rem;
            inset-inline: 1.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 500;
            color: rgba(251, 249, 245, 0.92);
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.55);
            z-index: 10;
            pointer-events: none;
        }

        .hl-screen-footer-brand {
            color: #F5E6CE;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        @media (max-width: 639px) {
            .hl-screen-footer {
                inset-inline: 1rem;
                bottom: 0.5rem;
                font-size: 0.74rem;
            }
        }
    </style>

    {{-- Full-Screen Background (Vertical Salon Image on Mobile, Horizontal on Desktop) --}}
    <div class="hl-bg-stage" aria-hidden="true">
        <div class="hl-bg-photo"></div>
        <div class="hl-bg-overlay"></div>
        <div class="hl-bg-glow"></div>
    </div>

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

            {{-- Single Compact Subtitle (Welcome heading removed as requested) --}}
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
