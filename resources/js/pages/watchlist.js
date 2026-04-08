import Alpine from "alpinejs";
import $ from "jquery";
import numeral from "numeral";

Alpine.data("watchlist", () => ({
    source: "movie",
    sources: { movie: "Movies", tv: "TV Shows" },

    gridView: Alpine.$persist(true).as("watchlist-view-preference"),

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    filterBy: {
        open: Alpine.$persist(false).as("watchlist-filter-preference"),
        value: "added",
        options: {
            added: "Date Added",
            popularity: "Popularity",
            release_date: "Release Date",
            vote_average: "User Rating",
            vote_count: "Number of ratings",
        },
    },

    movie: {
        loading: false,
        error: false,
        errorMessage: "",
        data: [],
        page: 1,
        totalPages: 1,
        initialized: false,
        softDeleteItems: [],
        orderByCreatedAsc: Alpine.$persist(false).as("movie-sort-preference"), // ← renamed
    },

    tv: {
        loading: false,
        error: false,
        errorMessage: "",
        data: [],
        page: 1,
        totalPages: 1,
        initialized: false,
        softDeleteItems: [],
        orderByCreatedAsc: Alpine.$persist(false).as("tv-sort-preference"), // ← renamed
    },

    isSourceActive(type) {
        return this.source === type;
    },

    onSourceChange(source) {
        this.source = source;
    },

    toggleOrderBy() {
        const type = this.source;
        this[type].orderByCreatedAsc = !this[type].orderByCreatedAsc;

        if (this.filterBy.value !== "added") return;

        if (!this.hasMore(type)) {
            this[type].data = [...this[type].data].reverse();
            return;
        }

        this.resetList(type);
        this.fetch(type);
    },

    toggleFilterBy() {
        this.filterBy.open = !this.filterBy.open;

        if (this.filterBy.open) {
            this.$nextTick(() => {
                const el = this.$refs.filterByContent?.querySelector(
                    `[data-key="${this.filterBy.value}"]`,
                );
                el?.focus();
            });
        }
    },

    closeFilterBy() {
        this.filterBy.open = false;
    },

    get filterByLabel() {
        return this.filterBy.options[this.filterBy.value];
    },

    resetList(type) {
        this[type].loading = false;
        this[type].error = false;
        this[type].errorMessage = "";
        this[type].data = [];
        this[type].page = 1;
        this[type].totalPages = 1;
        this[type].initialized = false;
    },

    getItems(type) {
        if (!this[type]) return [];

        const items = this[type].data.filter(
            (item) => !this[type].softDeleteItems.includes(item.id),
        );

        if (this.filterBy.value === "added") {
            return items;
        }

        if (this.filterBy.value === "popularity") {
            return this[type].orderByCreatedAsc
                ? [...items].sort((a, b) => a.popularity - b.popularity)
                : [...items].sort((a, b) => b.popularity - a.popularity);
        }

        if (this.filterBy.value === "release_date") {
            return this[type].orderByCreatedAsc
                ? [...items].sort((a, b) =>
                      (a.release_date || "").localeCompare(
                          b.release_date || "",
                      ),
                  )
                : [...items].sort((a, b) =>
                      (b.release_date || "").localeCompare(
                          a.release_date || "",
                      ),
                  );
        }

        if (this.filterBy.value === "vote_average") {
            return this[type].orderByCreatedAsc
                ? [...items].sort((a, b) => a.vote_average - b.vote_average)
                : [...items].sort((a, b) => b.vote_average - a.vote_average);
        }

        if (this.filterBy.value === "vote_count") {
            return this[type].orderByCreatedAsc
                ? [...items].sort((a, b) => a.vote_count - b.vote_count)
                : [...items].sort((a, b) => b.vote_count - a.vote_count);
        }

        return items;
    },

    init() {
        this.$watch("source", (type) => {
            if (!this[type].initialized) {
                this.fetch(type);
            }
        });

        this.fetch(this.source);
    },

    hasMore(type) {
        return this[type].page <= this[type].totalPages;
    },

    fetch(type, attempt = 1) {
        if (!this.hasMore(type)) return;

        if (attempt === 1 && this[type].loading) return;

        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
            this[type].errorMessage = "";
        }

        $.ajax({
            url: `/api/watchlist/${type}`,
            method: "GET",
            data: {
                page: this[type].page,
                sort_by: this[type].orderByCreatedAsc
                    ? "created_at.asc"
                    : "created_at.desc",
            },

            success: (res) => {
                if (res?.data?.results) {
                    const existingIds = new Set(
                        this[type].data.map((i) => i.id),
                    );

                    const filtered = res.data.results.filter(
                        (i) => !existingIds.has(i.id),
                    );

                    const newItems = filtered.map((item) => ({
                        ...item,
                        media_type: type,
                    }));

                    this[type].data.push(...newItems);
                    this[type].totalPages = res.data.total_pages;
                    this[type].page++;

                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(
                        type,
                        attempt,
                        res?.message || "Unexpected response",
                    );
                }
            },

            error: (xhr) => {
                const message =
                    xhr.responseJSON?.message ||
                    `Request failed (${xhr.status || "network error"})`;

                this._retryOrFail(type, attempt, message);
            },
        });
    },

    _retryOrFail(type, attempt, message) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;
            this[type].errorMessage = message;

            console.error(`Watchlist [${type}] failed`, message);
        }
    },

    softDelete(event) {
        const { media_id, media_type } = event.detail;
        if (!media_id || !["movie", "tv"].includes(media_type)) {
            return this.handleError("Invalid event data");
        }
        this[media_type].softDeleteItems.push(media_id);
    },

    rollbackDelete(event) {
        const { media_id, media_type } = event.detail;
        if (!media_id || !["movie", "tv"].includes(media_type)) {
            return this.handleError("Invalid event data");
        }
        this[media_type].softDeleteItems = this[
            media_type
        ].softDeleteItems.filter((id) => id !== media_id);
    },

    delete(event) {
        const { media_id, media_type } = event.detail;
        if (!media_id || !["movie", "tv"].includes(media_type)) {
            return this.handleError("Invalid event data");
        }
        this[media_type].data = this[media_type].data.filter(
            (item) => item.id !== media_id,
        );
        this[media_type].softDeleteItems = this[
            media_type
        ].softDeleteItems.filter((id) => id !== media_id);
    },

    handleError(message) {
        console.error("Watchlist Error:", message);
    },

    formatDate(date) {
        return dayjs(date).format("YYYY");
    },

    formatNumeral(num) {
        return numeral(num).format("0a");
    },
}));
