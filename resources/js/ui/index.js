export default function registerUIComponents(Alpine) {
    Alpine.data("horizontalSlider", () => ({
        slider: null,
        canScrollLeft: false,
        canScrollRight: false,

        init() {
            this.slider = this.$el.querySelector("[data-slider]");

            if (!this.slider) return;

            this.updateScrollState();

            this.slider.addEventListener(
                "scroll",
                () => this.updateScrollState(),
                {
                    passive: true,
                },
            );

            new ResizeObserver(() => this.updateScrollState()).observe(
                this.slider,
            );
        },

        updateScrollState() {
            if (!this.slider) return;
            this.canScrollLeft = this.slider.scrollLeft > 0;
            this.canScrollRight =
                this.slider.scrollLeft + this.slider.clientWidth <
                this.slider.scrollWidth - 1;
        },

        scrollLeft() {
            this.slider?.scrollBy({
                left: -(this.slider.clientWidth * 0.75),
                behavior: "smooth",
            });
        },

        scrollRight() {
            this.slider?.scrollBy({
                left: this.slider.clientWidth * 0.75,
                behavior: "smooth",
            });
        },
    }));
}
