import { heroSlides, state } from './state.js';

export function setHeroSlide(index) {
    state.currentSlideIndex = index;
    const slide = heroSlides[index];
    const imgEl = document.getElementById('heroSlideImg');
    const titleEl = document.getElementById('heroSlideTitle');
    const subtitleEl = document.getElementById('heroSlideSubtitle');
    const indicators = document.getElementById('sliderIndicators')?.children;

    if (imgEl && titleEl && subtitleEl && slide) {
        imgEl.style.opacity = '0.7';
        setTimeout(() => {
            imgEl.src = slide.img;
            imgEl.onerror = () => {
                imgEl.src = slide.fallback || 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=1000&q=80';
            };
            titleEl.textContent = slide.title;
            subtitleEl.textContent = slide.subtitle;
            imgEl.style.opacity = '1';
        }, 150);
    }

    if (indicators) {
        for (let i = 0; i < indicators.length; i++) {
            if (i === index) {
                indicators[i].className = "h-2 w-7 rounded-full bg-gold transition-all duration-300";
            } else {
                indicators[i].className = "h-2 w-2 rounded-full bg-white/70 hover:bg-white transition-all duration-300";
            }
        }
    }
}

export function startHeroSlider() {
    if (state.slideInterval) clearInterval(state.slideInterval);
    state.slideInterval = setInterval(() => {
        if (heroSlides && heroSlides.length > 0) {
            const next = (state.currentSlideIndex + 1) % heroSlides.length;
            setHeroSlide(next);
        }
    }, 5000);
}
