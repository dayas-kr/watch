import Alpine from "alpinejs";
import $ from "jquery";

export default () => ({
    // ── State ──────────────────────────────────────────────────────────────
    open: false,
    loading: false,
    results: [],
    selectedIndex: -1,
    query: "",

    // ── Sources ────────────────────────────────────────────────────────────
    // resultType drives which card template renders in the blade:
    //   "multi"      → mixed results — branches per item.media_type
    //   "title"      → movie / TV  (poster + title + year + badge)
    //   "person"     → celebrity   (profile photo + name + department)
    //   "collection" → collection  (poster + name + backdrop hint)
    //   "company"    → company     (logo on white pill + name + country)
    //   "keyword"    → keyword     (tag chip — name only, no image)
    source: Alpine.$persist("multi").as("search-source-preference"),
    sourcesDialogOpen: false,
    sources: [
        {
            label: "All",
            value: "multi",
            icon: "fas fa-search",
            endpoint: "/api/search/multi",
            resultType: "multi", // ← fixed: was "title"
        },
        {
            label: "Movies",
            value: "movie",
            icon: "fas fa-film",
            endpoint: "/api/search/movie",
            resultType: "title",
        },
        {
            label: "TV Shows",
            value: "tv",
            icon: "fas fa-tv",
            endpoint: "/api/search/tv",
            resultType: "title",
        },
        {
            label: "Celebs",
            value: "person",
            icon: "fas fa-users",
            endpoint: "/api/search/person",
            resultType: "person",
        },
        {
            label: "Collections",
            value: "collection",
            icon: "fas fa-layer-group",
            endpoint: "/api/search/collection",
            resultType: "collection",
        },
        {
            label: "Companies",
            value: "company",
            icon: "fas fa-building",
            endpoint: "/api/search/company",
            resultType: "company",
        },
        {
            label: "Keywords",
            value: "keyword",
            icon: "fas fa-tag",
            endpoint: "/api/search/keyword",
            resultType: "keyword",
        },
    ],

    placeholderText: {
        multi: "Search movies, TV series, people...",
        movie: "Search Movies...",
        tv: "Search TV Shows...",
        person: "Search Celebrities...",
        collection: "Search Collections...",
        company: "Search Companies...",
        keyword: "Search Keywords...",
    },

    // ── Private ────────────────────────────────────────────────────────────
    _cache: {},
    _xhr: null,
    _maxRetries: 3,

    // ── Computed ───────────────────────────────────────────────────────────
    get placeholder() {
        return this.placeholderText[this.source] ?? "Search...";
    },

    get activeSourceLabel() {
        return (
            this.sources.find((s) => s.value === this.source)?.label ?? "All"
        );
    },

    get activeSource() {
        return this.sources.find((s) => s.value === this.source);
    },

    get activeResultType() {
        return this.activeSource?.resultType ?? "title";
    },

    // ── Sources dialog ─────────────────────────────────────────────────────
    toggleSourcesDialog() {
        this.open = false;
        this.sourcesDialogOpen = !this.sourcesDialogOpen;
    },

    closeSourcesDialog() {
        this.sourcesDialogOpen = false;
    },

    onSourceChange(source) {
        this.source = source;
        this.closeSourcesDialog();
        this.$nextTick(() => {
            this.$refs.searchInput.focus();
            if (this.query.trim()) {
                this.fetchResults();
            }
        });
    },

    // ── Input events ───────────────────────────────────────────────────────
    handleInputFocus() {
        this.sourcesDialogOpen = false;
        if (this.query.trim() && this.results.length) {
            this.open = true;
        }
    },

    handleInputBlur() {
        setTimeout(() => {
            this.open = false;
        }, 200);
    },

    handleEscape() {
        if (this.query) {
            this.query = "";
            this.results = [];
            this.open = false;
        } else {
            this.$refs.searchInput.blur();
        }
    },

    // ── Keyboard navigation ────────────────────────────────────────────────
    moveSelection(dir) {
        if (!this.results.length) return;
        const max = this.results.length - 1;
        this.selectedIndex = Math.max(
            0,
            Math.min(max, this.selectedIndex + dir),
        );
    },

    enterSelect() {
        const item = this.results[this.selectedIndex];
        if (item) window.location.href = this.itemHref(item);
    },

    // ── Fetch with retry + cache ───────────────────────────────────────────
    fetchResults() {
        const q = this.query.trim();

        if (!q) {
            this.results = [];
            this.open = false;
            this.loading = false;
            if (this._xhr) this._xhr.abort();
            return;
        }

        const cacheKey = `${this.source}:${q}`;

        if (this._cache[cacheKey]) {
            this.results = this._cache[cacheKey];
            this.open = true;
            return;
        }

        if (this._xhr) this._xhr.abort();

        this.open = true;
        this.loading = true;
        this.selectedIndex = -1;

        this._doFetch(this.activeSource.endpoint, q, cacheKey, 0);
    },

    _doFetch(endpoint, query, cacheKey, attempt) {
        this._xhr = $.ajax({
            url: endpoint,
            method: "GET",
            data: { query },
            success: (res) => {
                const items = res.data.results ?? [];
                this._cache[cacheKey] = items;
                this.results = items;
                this.loading = false;
            },
            error: (xhr) => {
                if (xhr.statusText === "abort") return;

                if (attempt < this._maxRetries - 1) {
                    const delay = 300 * Math.pow(2, attempt);
                    setTimeout(
                        () =>
                            this._doFetch(
                                endpoint,
                                query,
                                cacheKey,
                                attempt + 1,
                            ),
                        delay,
                    );
                } else {
                    this.loading = false;
                    this.results = [];
                }
            },
        });
    },

    // ── Template helpers ───────────────────────────────────────────────────
    itemHref(item) {
        const type = this.source === "multi" ? item.media_type : this.source;
        return `/${type}/${item.id}`;
    },

    posterUrl(path) {
        return path ? `https://image.tmdb.org/t/p/w300${path}` : null;
    },

    // Company logos are PNGs on transparent bg — w200 is plenty
    logoUrl(path) {
        return path ? `https://image.tmdb.org/t/p/w200${path}` : null;
    },

    avatarUrl(path) {
        return path ? `https://image.tmdb.org/t/p/w185${path}` : null;
    },

    releaseYear(item) {
        const date = item.release_date || item.first_air_date;
        return date ? date.substring(0, 4) : "—";
    },

    mediaTypeLabel(item) {
        if (this.source !== "multi") {
            const label = this.activeSourceLabel;
            return label.endsWith("s") ? label.slice(0, -1) : label;
        }
        switch (item.media_type) {
            case "movie":
                return "Movie";
            case "tv":
                return "TV Show";
            case "person":
                return "Person";
            default:
                return item.media_type ?? "—";
        }
    },

    init() {
        this.$watch("selectedIndex", (value) => {
            const cardType = this.source;
            const card = this.$root.querySelector(
                `[data-${cardType}-card="true"]`,
            );

            card?.scrollIntoView({ block: "nearest" });
        });
    },
});
