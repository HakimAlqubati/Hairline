/**
 * Hairline Beauty Lounge — Frontend JS
 *
 * Uses Alpine.js (bundled with Livewire v4).
 * We register custom Alpine.js data helpers here.
 */

/**
 * swipeToClose — Alpine.js plugin function for swipe-down-to-close.
 * Attach to a bottom sheet panel element.
 *
 * Usage (x-data): x-data="swipeToClose($refs.panel, () => { open = false; })"
 *
 * @param {HTMLElement} panel
 * @param {Function} closeCallback
 */
window.swipeToClose = function (panel, closeCallback) {
    return {
        startY: 0,
        isDragging: false,
        dragOffset: 0,

        init() {
            if (!panel) return;

            panel.addEventListener('touchstart', this.onTouchStart.bind(this), { passive: true });
            panel.addEventListener('touchmove',  this.onTouchMove.bind(this),  { passive: false });
            panel.addEventListener('touchend',   this.onTouchEnd.bind(this),   { passive: true });
        },

        onTouchStart(e) {
            this.startY    = e.touches[0].clientY;
            this.isDragging = true;
            this.dragOffset = 0;
            panel.style.transition = 'none';
        },

        onTouchMove(e) {
            if (!this.isDragging) return;
            const deltaY = e.touches[0].clientY - this.startY;
            if (deltaY < 0) return; // don't drag upward
            this.dragOffset = deltaY;
            panel.style.transform = `translateX(-50%) translateY(${deltaY}px)`;
            if (deltaY > 10) e.preventDefault();
        },

        onTouchEnd() {
            this.isDragging = false;
            panel.style.transition = '';
            panel.style.transform  = '';

            if (this.dragOffset > 100) {
                closeCallback();
            }
            this.dragOffset = 0;
        },
    };
};
