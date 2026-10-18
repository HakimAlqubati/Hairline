@props([
    'heroSlides' => [],
])

<section aria-label="البانر الإعلاني" class="relative">
    <div id="heroSliderContainer" class="relative w-full h-64 sm:h-72 rounded-3xl overflow-hidden shadow-lg border border-white">
        
        <!-- Dynamic Slide Image -->
        <img id="heroSlideImg" 
             src="{{ asset($heroSlides[0]['img'] ?? 'images/salon/reception-desk.jpg') }}" 
             alt="هيرلاين بيوتي لاونج - أناقتك تتجدد"
             onerror="this.src='{{ $heroSlides[0]['fallback'] ?? 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80' }}'"
             class="w-full h-full object-cover object-center transition-all duration-700 transform scale-100">

        <!-- Dark Gradient Overlay from Bottom for Text Legibility -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#11221A] via-[#1F3A2E]/45 to-transparent"></div>

        <!-- Banner Content -->
        <div class="absolute inset-0 p-6 flex flex-col justify-end">
            <div class="flex items-end justify-between gap-4">
                <!-- Right Side Text Content -->
                <div class="space-y-2">
                    <!-- Translucent Badge: HAIRLINE BEAUTY LOUNGE -->
                    <div class="inline-block px-3.5 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-gold-light text-[11px] font-semibold tracking-[0.18em] uppercase">
                        HAIRLINE BEAUTY LOUNGE
                    </div>
                    <!-- Large Prominent White Title: أناقتكِ تتجدد -->
                    <h2 id="heroSlideTitle" class="text-3xl sm:text-4xl font-black text-white tracking-tight drop-shadow-sm">
                        {{ $heroSlides[0]['title'] ?? 'أناقتكِ تتجدد' }}
                    </h2>
                    <p id="heroSlideSubtitle" class="text-xs sm:text-sm text-gray-200 font-medium max-w-[260px]">
                        {{ $heroSlides[0]['subtitle'] ?? 'أحدث تقنيات المعالجات الحارة والعناية الفائقة بالشعر على أيدي نخبة من الأخصائيات' }}
                    </p>
                </div>

                <!-- Left Side Slider Navigation Indicators (Gold & White Line/Dots) -->
                <div class="flex items-center gap-1.5 pb-1 shrink-0" id="sliderIndicators">
                    @foreach($heroSlides as $idx => $slide)
                        <button onclick="setHeroSlide({{ $idx }})" 
                                aria-label="الشريحة {{ $idx + 1 }}" 
                                class="{{ $idx === 0 ? 'h-2 w-7 rounded-full bg-gold transition-all duration-300' : 'h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300' }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
