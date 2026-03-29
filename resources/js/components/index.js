export default function registerComponents(Alpine) {
    Alpine.data("searchTitles", () => ({
        source: "tmdb",
        query: "",

        loading: false,

        get placeholder() {
            return this.source === "ai"
                ? "Describe a mood, genre, or vibe…"
                : "Search movies, TV series, anime…";
        },

        searchSource: [
            { label: "TMDB", value: "tmdb", icon: "database" },
            { label: "AI Powered", value: "ai", icon: "wand-magic-sparkles" },
        ],

        // Source: helper functions
        isSource(source) {
            return this.source === source;
        },

        onSourceChange(source) {
            this.source = source;
            this.query = "";

            this.$nextTick(() => {
                if (source === "ai") {
                    this.$refs.searchTextarea?.focus();
                } else {
                    this.$refs.searchInput?.focus();
                }
            });
        },
    }));
}
