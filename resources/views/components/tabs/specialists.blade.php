@props([
    'specialists' => [],
    'isActive' => false,
])

@php
    $specialistsList = array_values($specialists);
    $totalSpecialists = count($specialistsList);
@endphp

<style>
    /* Smooth hover zoom animation on specialist portrait */
    .specialist-card {
        transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                    box-shadow 0.45s cubic-bezier(0.16, 1, 0.3, 1),
                    border-color 0.35s ease;
        will-change: transform;
    }

    .specialist-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 45px -12px rgba(194, 158, 109, 0.28), 0 8px 20px -6px rgba(33, 28, 24, 0.08);
    }

    .specialist-portrait-img {
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                    filter 0.45s ease;
        transform-origin: center bottom;
        will-change: transform;
    }

    .specialist-card:hover .specialist-portrait-img {
        transform: scale(1.085) translateY(-4px);
        filter: drop-shadow(0 14px 24px rgba(33, 28, 24, 0.14));
    }

    .specialist-arch-bg {
        transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 0.4s ease,
                    background-color 0.4s ease;
    }

    .specialist-card:hover .specialist-arch-bg {
        transform: scale(1.03);
        border-color: rgba(194, 158, 109, 0.45);
        background-color: rgba(255, 255, 255, 0.92);
    }

    /* Landing impact pulse when modal drops back onto the active specialist card */
    @keyframes specialistCardLandingPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(194, 158, 109, 0.75);
        }
        30% {
            transform: scale(0.955) translateY(6px);
            box-shadow: 0 0 0 6px rgba(194, 158, 109, 0.55), 0 25px 50px -12px rgba(194, 158, 109, 0.45);
        }
        65% {
            transform: scale(1.02) translateY(-3px);
            box-shadow: 0 0 0 10px rgba(194, 158, 109, 0.2), 0 20px 40px -10px rgba(194, 158, 109, 0.3);
        }
        100% {
            transform: scale(1) translateY(0);
            box-shadow: 0 6px 24px -4px rgba(33, 28, 24, 0.06);
        }
    }

    .specialist-card-landed {
        animation: specialistCardLandingPulse 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        border-color: var(--color-primary) !important;
        z-index: 20;
    }

    /* Directional slide transitions when flipping inside the modal */
    @keyframes modalSlideFromRight {
        0% {
            opacity: 0;
            transform: translateX(28px) scale(0.97);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    @keyframes modalSlideFromLeft {
        0% {
            opacity: 0;
            transform: translateX(-28px) scale(0.97);
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    .modal-slide-right {
        animation: modalSlideFromRight 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .modal-slide-left {
        animation: modalSlideFromLeft 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<section id="specialistsTabView"
         aria-label="صفحة الأخصائيات المعتمدات"
         class="{{ $isActive ? '' : 'hidden' }} flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-5 md:pt-8 pb-16 space-y-6 md:space-y-8 animate-step">

    <!-- Responsive Specialists Showcase Grid -->
    <div id="specialistsGridContainer" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-6 lg:gap-7">
        @forelse($specialistsList as $index => $sp)
            @php
                $nameParts = array_map('trim', explode('|', $sp['name'] ?? ''));
                $nameAr = $nameParts[0] ?? ($sp['name'] ?? '');
                $nameEn = $nameParts[1] ?? '';
            @endphp

            <article id="specialist-card-{{ $index }}"
                     data-specialist-index="{{ $index }}"
                     data-specialist-id="{{ $sp['id'] }}"
                     onclick="openSpecialistModal({{ $index }})"
                     role="button"
                     tabindex="0"
                     aria-label="عرض تفاصيل وصورة الأخصائية {{ $sp['name'] }}"
                     onkeydown="if(event.key === 'Enter' || event.key === ' ') { event.preventDefault(); openSpecialistModal({{ $index }}); }"
                     class="specialist-card group relative rounded-[28px] bg-white border border-primary-soft/75 hover:border-primary shadow-card overflow-hidden flex flex-col cursor-pointer select-none focus:outline-none focus:ring-2 focus:ring-primary">

                <!-- Top Portrait Showcase Area (Large Image with Hover Scale Animation) -->
                <div class="relative w-full h-[310px] sm:h-[320px] lg:h-[295px] xl:h-[330px] bg-gradient-to-b from-[#FAF8F5] via-[#F6EFE2]/75 to-[#E6D5B8]/50 pt-5 px-4 flex items-end justify-center overflow-hidden">

                    <!-- Decorative Luxury Studio Arch Behind Specialist -->
                    <div class="specialist-arch-bg absolute inset-x-5 top-6 bottom-0 rounded-t-[140px] bg-white/80 border border-primary/20 shadow-inner pointer-events-none"></div>

                    <!-- Experience Badge (Top Right) -->
                    <div class="absolute top-3.5 right-3.5 z-20">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary/35 text-primary-dark text-[11px] font-extrabold shadow-xs">
                            <i class="fa-solid fa-medal text-primary text-[10px]"></i>
                            <span>{{ $sp['experience'] }}</span>
                        </span>
                    </div>

                    <!-- Large Specialist Portrait Image -->
                    <img id="specialist-card-img-{{ $index }}"
                         src="{{ $sp['avatar'] }}"
                         alt="{{ $sp['name'] }}"
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($nameAr) }}&background=FFFFFF&color=C29E6D&bold=true&size=512'"
                         class="specialist-portrait-img relative z-10 w-full h-full object-contain object-bottom" />

                    <!-- Bottom Primary Accent Line -->
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-gradient-to-r from-primary-light via-primary to-primary-light opacity-75 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <!-- Card Body: Balanced Distribution of Name, Role, Bio & Action -->
                <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white space-y-4 text-right">
                    <div class="space-y-2.5">
                        <!-- Specialist Name (AR + EN) -->
                        <div class="flex items-baseline justify-between gap-2">
                            <h3 class="text-lg sm:text-xl font-black text-sage-dark group-hover:text-primary-dark transition-colors leading-tight">
                                {{ $nameAr }}
                            </h3>
                            @if($nameEn)
                                <span class="text-xs sm:text-sm font-serif font-bold tracking-wider text-primary-dark uppercase shrink-0">
                                    {{ $nameEn }}
                                </span>
                            @endif
                        </div>

                        <!-- Role / Specialty -->
                        <p class="text-xs sm:text-[13px] font-extrabold text-primary-dark leading-relaxed">
                            {{ $sp['role'] }}
                        </p>

                        <!-- Subtle Divider -->
                        <div class="h-px w-full bg-gradient-to-l from-primary-soft/70 via-primary-soft/30 to-transparent"></div>

                        <!-- Bio Description -->
                        <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">
                            {{ $sp['bio'] }}
                        </p>
                    </div>

                
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-10 text-center border border-primary-soft/50">
                <p class="text-sm text-gray-500 font-bold">لا توجد بيانات أخصائيات متاحة حالياً.</p>
            </div>
        @endforelse
    </div>
</section>

<!-- ==================== INTERACTIVE SPECIALIST LIGHTBOX & DETAIL MODAL ==================== -->
<div id="specialistDetailModal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="specialistModalName"
     class="fixed inset-0 z-[70] hidden items-center justify-center p-3 sm:p-6 md:p-10 overflow-hidden select-none">

    <!-- Dark Frosted Backdrop -->
    <div id="specialistModalBackdrop"
         onclick="closeSpecialistModal()"
         class="fixed inset-0 bg-[#211C18]/75 backdrop-blur-md transition-opacity duration-300 opacity-0">
    </div>

    <!-- RIGHT SCREEN NAVIGATION BUTTON (Previous Specialist in RTL) -->
    <button type="button"
            id="specialistModalPrevBtn"
            onclick="navigateSpecialistModal(-1, event)"
            aria-label="الأخصائية السابقة"
            title="الأخصائية السابقة"
            class="fixed right-2 sm:right-4 md:right-7 lg:right-10 top-1/2 -translate-y-1/2 z-[85] w-11 h-11 sm:w-13 sm:h-13 md:w-14 md:h-14 rounded-full bg-white/95 hover:bg-primary text-sage-dark hover:text-white border-2 border-primary/60 hover:border-white shadow-2xl flex items-center justify-center transition-all duration-300 active:scale-90 cursor-pointer group">
        <i class="fa-solid fa-chevron-right text-base sm:text-lg text-primary-dark group-hover:text-white group-hover:translate-x-0.5 transition-all"></i>
    </button>

    <!-- LEFT SCREEN NAVIGATION BUTTON (Next Specialist in RTL) -->
    <button type="button"
            id="specialistModalNextBtn"
            onclick="navigateSpecialistModal(1, event)"
            aria-label="الأخصائية التالية"
            title="الأخصائية التالية"
            class="fixed left-2 sm:left-4 md:left-7 lg:left-10 top-1/2 -translate-y-1/2 z-[85] w-11 h-11 sm:w-13 sm:h-13 md:w-14 md:h-14 rounded-full bg-white/95 hover:bg-primary text-sage-dark hover:text-white border-2 border-primary/60 hover:border-white shadow-2xl flex items-center justify-center transition-all duration-300 active:scale-90 cursor-pointer group">
        <i class="fa-solid fa-chevron-left text-base sm:text-lg text-primary-dark group-hover:text-white group-hover:-translate-x-0.5 transition-all"></i>
    </button>

    <!-- Main Animated Modal Card (Animates from/to the active Specialist Card on close) -->
    <div id="specialistModalCard"
         onclick="event.stopPropagation()"
         class="relative z-[80] w-full max-w-[430px] md:max-w-4xl max-h-[90vh] rounded-[32px] bg-white border-2 border-primary/50 shadow-2xl overflow-hidden flex flex-col md:flex-row will-change-transform">

        <!-- Top Primary Shimmer Strip -->
        <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-primary-light via-primary to-primary-light z-30"></div>

        <!-- Close Button (Top Left) -->
        <button type="button"
                id="specialistModalCloseBtn"
                onclick="closeSpecialistModal()"
                aria-label="إغلاق نافذة الأخصائية"
                title="إغلاق"
                class="absolute top-4 left-4 z-30 w-10 h-10 rounded-full bg-white/95 hover:bg-sage-dark text-sage-dark hover:text-white border border-primary/40 shadow-md flex items-center justify-center transition-all duration-200 active:scale-90 cursor-pointer">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>

        <!-- Counter Badge (Top Right) -->
        <div class="absolute top-4 right-4 z-30">
            <span id="specialistModalCounter"
                  dir="ltr"
                  class="inline-flex items-center gap-1 px-3.5 py-1 rounded-full bg-white/95 backdrop-blur-md border border-primary/40 text-xs font-black text-primary-dark shadow-xs">
                1 / {{ $totalSpecialists }}
            </span>
        </div>

        <!-- Modal Content Wrapper (Animated on Next/Prev flip) -->
        <div id="specialistModalAnimatedBody" class="w-full flex flex-col md:flex-row overflow-y-auto md:overflow-hidden max-h-[90vh]">

            <!-- RIGHT SIDE (on Desktop) / TOP (on Mobile): Enlarged Portrait Showcase -->
            <div class="relative w-full md:w-5/12 lg:w-[46%] h-[310px] sm:h-[360px] md:h-auto md:min-h-[480px] bg-gradient-to-b from-[#FAF8F5] via-[#F6EFE2] to-[#E6D5B8]/65 pt-10 px-6 flex items-end justify-center overflow-hidden shrink-0 border-b md:border-b-0 md:border-l border-primary-soft/60">

                <!-- Studio Arch Backdrop -->
                <div class="absolute inset-x-7 top-10 bottom-0 rounded-t-[180px] bg-white/85 border border-primary/25 shadow-inner pointer-events-none"></div>

                <!-- Enlarged High-Resolution Specialist Image -->
                <img id="specialistModalImg"
                     src=""
                     alt=""
                     class="relative z-10 w-full h-full max-h-[285px] sm:max-h-[330px] md:max-h-[430px] object-contain object-bottom drop-shadow-xl" />

                <!-- Bottom Gradient Floor -->
                <div class="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-t from-primary-soft/40 to-transparent pointer-events-none"></div>
            </div>

            <!-- LEFT SIDE (on Desktop) / BOTTOM (on Mobile): Full Specialist Details -->
            <div class="w-full md:w-7/12 lg:w-[54%] p-5 sm:p-7 md:p-8 md:overflow-y-auto flex flex-col justify-between space-y-5 text-right bg-white">
                <div class="space-y-4">
                    <!-- Experience Badge -->
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-primary-light text-primary-dark border border-primary/35 text-xs font-extrabold">
                            <i class="fa-solid fa-medal text-primary"></i>
                            <span id="specialistModalExperience"></span>
                        </span>
                    </div>

                    <!-- Specialist Full Name & Role -->
                    <div>
                        <h3 id="specialistModalName" class="text-2xl sm:text-3xl font-black text-sage-dark leading-tight"></h3>
                        <p id="specialistModalRole" class="text-sm sm:text-base font-extrabold text-primary-dark mt-1.5 leading-relaxed"></p>
                    </div>

                    <!-- Bio Section: Plain Text Without Box or Borders -->
                    <div class="space-y-1.5 pt-1">
                        <h4 class="text-xs font-extrabold text-sage-dark flex items-center gap-1.5">
                            <i class="fa-solid fa-sparkles text-primary text-xs"></i>
                            <span>نبذة عن الخبرة والمهارات</span>
                        </h4>
                        <p id="specialistModalBio" class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium"></p>
                    </div>
                </div>

                <!-- Modal Footer: Thumbnails Switcher -->
                <div class="pt-4 border-t border-primary-soft/50">
                    <!-- Quick Specialist Avatar Thumbnails Bar -->
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-gray-500">التنقل السريع بين الأخصائيات:</span>
                        <div id="specialistModalThumbnails" class="flex items-center gap-2">
                            @foreach($specialistsList as $thumbIdx => $thumbSp)
                                <button type="button"
                                        onclick="selectSpecialistInModal({{ $thumbIdx }})"
                                        id="specialist-modal-thumb-{{ $thumbIdx }}"
                                        title="{{ $thumbSp['name'] }}"
                                        class="w-9 h-9 rounded-full overflow-hidden border-2 border-primary-soft hover:border-primary transition-all duration-200 cursor-pointer bg-surface shrink-0">
                                    <img src="{{ $thumbSp['avatar'] }}"
                                         alt="{{ $thumbSp['name'] }}"
                                         class="w-full h-full object-cover object-top" />
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
