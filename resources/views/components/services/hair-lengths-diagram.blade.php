{{-- Visual Hair Length Diagram (Page 8) --}}
<div class="my-auto flex items-center justify-center py-2 sm:py-4">
    <div class="w-full max-w-[640px] mx-auto flex items-center justify-center px-2">
        <picture class="w-full flex justify-center">
            <source srcset="{{ asset('images/services/hair-lengths.webp') }} 2x, {{ asset('images/services/hair-lengths-1x.webp') }} 1x" type="image/webp">
            <source srcset="{{ asset('images/services/hair-lengths.png') }} 2x, {{ asset('images/services/hair-lengths-1x.png') }} 1x" type="image/png">
            <img 
                src="{{ asset('images/services/hair-lengths.webp') }}" 
                alt="دليل أطوال الشعر - Hair Lengths Guide" 
                class="w-full h-auto max-h-[680px] object-contain mx-auto mix-blend-multiply select-none"
                width="572"
                height="595"
                loading="eager"
                decoding="async"
            />
        </picture>
    </div>
</div>

