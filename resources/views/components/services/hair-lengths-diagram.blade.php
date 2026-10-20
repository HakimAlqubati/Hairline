{{-- Visual Hair Length Diagram (Page 8) --}}
<div class="my-auto flex items-center justify-center">
    <div class="w-full max-w-[700px] mx-auto">
        <svg viewBox="0 0 700 700" class="w-full h-auto overflow-visible select-none" aria-label="دليل أطوال الشعر">
            <defs>
                <!-- Clip path for clean horizontal bottom edge of the figure -->
                <clipPath id="figureBottomClip">
                    <rect x="180" y="60" width="480" height="548" />
                </clipPath>

                <!-- Rich Dark Brown Hair Base Gradient -->
                <linearGradient id="darkHairBase" x1="0.2" y1="0" x2="0.8" y2="1">
                    <stop offset="0%" stop-color="#2c1b12"/>
                    <stop offset="35%" stop-color="#3c2418"/>
                    <stop offset="70%" stop-color="#2a1810"/>
                    <stop offset="100%" stop-color="#1e110b"/>
                </linearGradient>

                <!-- Warm Chestnut Gloss Highlight Gradient -->
                <linearGradient id="chestnutHighlight" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#5a3825" stop-opacity="0.65"/>
                    <stop offset="45%" stop-color="#7a4e36" stop-opacity="0.85"/>
                    <stop offset="80%" stop-color="#4e3020" stop-opacity="0.7"/>
                    <stop offset="100%" stop-color="#2a1810" stop-opacity="0.3"/>
                </linearGradient>

                <!-- Soft Specular Shine for Waves -->
                <linearGradient id="hairShine" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#9c6f54" stop-opacity="0"/>
                    <stop offset="50%" stop-color="#a8795d" stop-opacity="0.55"/>
                    <stop offset="100%" stop-color="#5c3926" stop-opacity="0"/>
                </linearGradient>

                <!-- Dark Knitted Sweater Gradient -->
                <linearGradient id="sweaterGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#252724"/>
                    <stop offset="50%" stop-color="#191b18"/>
                    <stop offset="100%" stop-color="#121311"/>
                </linearGradient>

                <!-- Soft Shadow under horizontal measurement lines -->
                <filter id="lineShadow" x="-5%" y="-50%" width="110%" height="250%">
                    <feDropShadow dx="0" dy="2.5" stdDeviation="1.6" flood-color="#8c7b5c" flood-opacity="0.42"/>
                </filter>

                <!-- Soft blur for hair strand blending -->
                <filter id="softStrand" x="-10%" y="-10%" width="120%" height="120%">
                    <feGaussianBlur stdDeviation="2.2" />
                </filter>
            </defs>

            <!-- ====================================================
                 WOMAN FROM BEHIND (DARK SWEATER & WAVY BROWN HAIR)
                 ==================================================== -->
            <g clip-path="url(#figureBottomClip)">
                <!-- Knitted Black/Charcoal Sweater Body & Shoulders -->
                <path d="M 332 290
                         C 285 296, 256 312, 245 360
                         C 234 410, 222 480, 214 540
                         C 212 560, 226 575, 232 588
                         L 232 612
                         L 568 612
                         L 575 565
                         C 590 555, 594 520, 588 475
                         C 582 425, 566 355, 545 320
                         C 525 292, 485 288, 435 288 Z"
                      fill="url(#sweaterGrad)" />

                <!-- Subtle Sweater Knit Ribbing Texture on Left Shoulder/Arm -->
                <g stroke="#2f322e" stroke-width="1.2" opacity="0.55" fill="none">
                    <path d="M 262 325 C 248 395, 238 475, 230 565" stroke-dasharray="3 5"/>
                    <path d="M 278 312 C 265 390, 255 475, 246 585" stroke-dasharray="3 5"/>
                    <path d="M 295 304 C 282 385, 272 475, 265 600" stroke-dasharray="3 5"/>
                    <path d="M 312 300 C 300 385, 290 475, 284 605" stroke-dasharray="3 5"/>
                </g>

                <!-- Subtle Sleeve Separation Shadow on Left -->
                <path d="M 280 415 C 276 475, 272 535, 270 595" stroke="#0d0e0c" stroke-width="5" opacity="0.6" fill="none" filter="url(#softStrand)"/>

                <!-- Outer Soft Hair Halo / Flyaway Strands -->
                <path d="M 418 82
                         C 350 84, 308 135, 305 205
                         C 302 248, 316 268, 326 285
                         C 310 302, 298 335, 304 375
                         C 312 420, 315 460, 326 510
                         C 334 548, 344 582, 360 606
                         L 542 606
                         C 555 580, 566 540, 562 495
                         C 578 485, 608 492, 622 505
                         C 610 480, 595 468, 588 450
                         C 602 440, 610 415, 592 395
                         C 574 375, 576 325, 564 265
                         C 552 205, 526 142, 495 106
                         C 472 82, 445 80, 418 82 Z"
                      fill="url(#darkHairBase)" />

                <!-- Stray Wavy Lock on the Far Right -->
                <path d="M 558 310
                         C 575 355, 588 388, 596 418
                         C 604 445, 582 458, 594 478
                         C 602 492, 618 498, 630 502
                         C 612 504, 592 494, 582 478
                         C 572 462, 582 442, 574 415
                         C 566 388, 554 355, 545 320 Z"
                      fill="#3b2316" opacity="0.92"/>

                <!-- Left Hair Face-Framing Curve & Shoulder Sweep -->
                <path d="M 355 110
                         C 315 148, 302 205, 316 250
                         C 324 275, 348 282, 322 292
                         C 305 298, 292 308, 318 308
                         C 342 308, 358 296, 362 272"
                      fill="none" stroke="#4a2e1e" stroke-width="6" stroke-linecap="round" filter="url(#softStrand)"/>

                <!-- Layer 1: Warm Chestnut Broad Waves -->
                <path d="M 380 105
                         C 355 180, 342 265, 340 345
                         C 338 400, 318 435, 342 475
                         C 365 510, 340 555, 368 598"
                      fill="none" stroke="url(#chestnutHighlight)" stroke-width="26" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 425 98
                         C 418 185, 412 275, 422 365
                         C 430 430, 405 475, 435 525
                         C 462 568, 438 592, 452 608"
                      fill="none" stroke="url(#chestnutHighlight)" stroke-width="32" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 472 112
                         C 492 195, 515 285, 522 375
                         C 528 435, 495 480, 518 532
                         C 535 570, 515 595, 525 608"
                      fill="none" stroke="url(#chestnutHighlight)" stroke-width="28" stroke-linecap="round" filter="url(#softStrand)"/>

                <!-- Layer 2: Glossy Sculpted Curls & Waves -->
                <path d="M 348 315
                         C 332 355, 326 392, 348 425
                         C 370 458, 322 482, 345 520
                         C 368 555, 348 582, 375 605"
                      fill="none" stroke="url(#hairShine)" stroke-width="14" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 372 340
                         C 356 382, 362 420, 385 455
                         C 408 490, 365 522, 392 562
                         C 412 590, 398 602, 410 610"
                      fill="none" stroke="#180d08" stroke-width="16" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 412 140
                         C 402 230, 404 315, 418 390
                         C 432 455, 395 495, 428 540
                         C 456 578, 432 598, 448 610"
                      fill="none" stroke="url(#hairShine)" stroke-width="16" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 452 165
                         C 462 250, 472 335, 465 415
                         C 458 475, 492 515, 470 562
                         C 455 592, 478 604, 488 610"
                      fill="none" stroke="#180d08" stroke-width="18" stroke-linecap="round" filter="url(#softStrand)"/>

                <path d="M 485 155
                         C 512 235, 532 325, 518 405
                         C 506 468, 542 505, 518 555
                         C 502 588, 522 602, 535 608"
                      fill="none" stroke="url(#hairShine)" stroke-width="15" stroke-linecap="round" filter="url(#softStrand)"/>

                <!-- Fine Individual Hair Strand Details -->
                <g stroke="#6e4630" stroke-width="1.6" opacity="0.55" fill="none">
                    <path d="M 395 95 C 365 185, 352 285, 355 375 C 358 435, 330 470, 358 515 C 382 552, 358 585, 382 606"/>
                    <path d="M 418 90 C 405 185, 398 285, 412 380 C 424 445, 392 485, 422 532 C 448 572, 428 595, 442 608"/>
                    <path d="M 442 92 C 445 185, 452 285, 458 380 C 464 445, 438 490, 465 540 C 488 580, 468 598, 480 608"/>
                    <path d="M 468 100 C 488 185, 505 280, 508 375 C 512 440, 485 485, 508 538 C 528 578, 508 598, 518 608"/>
                    <path d="M 500 120 C 532 205, 555 295, 548 385 C 542 445, 568 485, 542 542"/>
                </g>

                <!-- Crown Natural Shading -->
                <radialGradient id="crownLight" cx="0.45" cy="0.18" r="0.35">
                    <stop offset="0%" stop-color="#6b442e" stop-opacity="0.45"/>
                    <stop offset="100%" stop-color="#1e110b" stop-opacity="0"/>
                </radialGradient>
                <ellipse cx="430" cy="175" rx="95" ry="75" fill="url(#crownLight)" />
            </g>

            <!-- ====================================================
                 LEFT SIDE HAIR LENGTH LABELS & HORIZONTAL LINES
                 ==================================================== -->

            <!-- 1. قصير جداً | V. SHORT -->
            <g transform="translate(0, 228)">
                <text x="128" y="-26" text-anchor="middle" fill="#7e7c78" font-family="Tajawal, sans-serif" font-weight="500" font-size="21">قصير جداً</text>
                <text x="128" y="-8" text-anchor="middle" fill="#b39d76" font-family="Montserrat, sans-serif" font-weight="500" font-size="13" letter-spacing="1.2">V. SHORT</text>
                <line x1="42" y1="6" x2="378" y2="6" stroke="#b39d76" stroke-width="2.2" filter="url(#lineShadow)"/>
            </g>

            <!-- 2. قصير | S -->
            <g transform="translate(0, 290)">
                <text x="128" y="-26" text-anchor="middle" fill="#7e7c78" font-family="Tajawal, sans-serif" font-weight="500" font-size="21">قصير</text>
                <text x="128" y="-7" text-anchor="middle" fill="#b39d76" font-family="Montserrat, sans-serif" font-weight="500" font-size="15.5" letter-spacing="1">S</text>
                <line x1="42" y1="6" x2="378" y2="6" stroke="#b39d76" stroke-width="2.2" filter="url(#lineShadow)"/>
            </g>

            <!-- 3. وسط | M -->
            <g transform="translate(0, 392)">
                <text x="128" y="-26" text-anchor="middle" fill="#7e7c78" font-family="Tajawal, sans-serif" font-weight="500" font-size="21">وسط</text>
                <text x="128" y="-7" text-anchor="middle" fill="#b39d76" font-family="Montserrat, sans-serif" font-weight="500" font-size="15.5" letter-spacing="1">M</text>
                <line x1="42" y1="6" x2="378" y2="6" stroke="#b39d76" stroke-width="2.2" filter="url(#lineShadow)"/>
            </g>

            <!-- 4. طويل | L -->
            <g transform="translate(0, 496)">
                <text x="128" y="-26" text-anchor="middle" fill="#7e7c78" font-family="Tajawal, sans-serif" font-weight="500" font-size="21">طويل</text>
                <text x="128" y="-7" text-anchor="middle" fill="#b39d76" font-family="Montserrat, sans-serif" font-weight="500" font-size="15.5" letter-spacing="1">L</text>
                <line x1="42" y1="6" x2="378" y2="6" stroke="#b39d76" stroke-width="2.2" filter="url(#lineShadow)"/>
            </g>

            <!-- 5. طويل جداً | XL -->
            <g transform="translate(0, 598)">
                <text x="128" y="-26" text-anchor="middle" fill="#7e7c78" font-family="Tajawal, sans-serif" font-weight="500" font-size="21">طويل جداً</text>
                <text x="128" y="-7" text-anchor="middle" fill="#b39d76" font-family="Montserrat, sans-serif" font-weight="500" font-size="15" letter-spacing="1">XL</text>
                <line x1="42" y1="6" x2="378" y2="6" stroke="#b39d76" stroke-width="2.2" filter="url(#lineShadow)"/>
            </g>
        </svg>
    </div>
</div>
