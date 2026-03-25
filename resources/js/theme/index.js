export default function registerTheme(Alpine) {
    Alpine.store("theme", {
        value: localStorage.getItem("theme") || "system",
        mql: matchMedia("(prefers-color-scheme: dark)"),

        resolve() {
            return this.value === "system"
                ? (this.mql.matches && "dark") || "light"
                : this.value;
        },

        apply() {
            const cls = this.resolve();
            const html = document.documentElement;

            html.className = cls;
            html.dataset.theme = cls;
        },

        set(mode) {
            this.value = mode;
            localStorage.setItem("theme", mode);
            this.apply();
        },

        init() {
            this.mql.addEventListener("change", () => {
                if (this.value === "system") this.apply();
            });

            window.addEventListener("storage", (e) => {
                if (e.key === "theme") {
                    this.value = e.newValue || "system";
                    this.apply();
                }
            });
        },
    });
}
