import Alpine from "alpinejs";
import $ from "jquery";
import numeral from "numeral";

import recommendations from "./titles/recommendations";
import similar from "./titles/similar";

Alpine.data("recommendations", recommendations);
Alpine.data("similar", similar);

Alpine.store("title", { id: null, media_type: null });

Alpine.data("tv", (data) => ({
    title: {},

    tv_id: null,
    inWatchlist: null,
    inWatched: null,

    loading: true,
    error: false,

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        if (!this.initialValidation()) return;
        this.initialSetup();
        this.fetchTitle();

        const { watchlist, favorites, watched } = data;

        const store = Alpine.store("db");

        store.watchlist = watchlist;
        store.favorites = favorites;
        store.watched = watched;
    },

    initialValidation() {
        const { id } = data;

        if (!id) {
            return this.handleError("Invalid ID");
        }

        return true;
    },

    initialSetup() {
        const {
            id,
            media_type = "tv",
            inWatchlist = false,
            inWatched = false,
        } = data;

        this.tv_id = Number(id);
        this.inWatchlist = inWatchlist;
        this.inWatched = inWatched;

        const store = Alpine.store("title");
        store.id = this.tv_id;
        store.media_type = media_type;
    },

    fetchTitle(attempt = 1) {
        if (!this.tv_id) {
            this.loading = false;
            this.error = true;
            return;
        }

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: `/api/tv/${this.tv_id}`,
            method: "GET",
            data: { append_to_response: "credits" },
            success: (res) => {
                if (res.success) {
                    this.title = res.data;
                    this.loading = false;
                } else {
                    this._retryTitleOrFail(attempt);
                }
            },
            error: () => {
                this._retryTitleOrFail(attempt);
            },
        });
    },

    _retryTitleOrFail(attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(
                () => this.fetchTitle(attempt + 1),
                this.RETRY_DELAY_MS * attempt,
            );
        } else {
            this.loading = false;
            this.error = true;
        }
    },

    syncWatchlist(watchlist) {
        this.inWatchlist = watchlist;
    },

    syncWatched(watched) {
        this.inWatched = watched;
    },

    formatNumeral(num) {
        return numeral(num).format("0a");
    },

    formatRuntime(runtime) {
        const h = Math.floor(runtime / 60);
        const m = runtime % 60;

        if (h === 0) return `${m}m`;
        return `${h}h ${m}m`;
    },

    handleError(message) {
        console.error("Error in TV show:", message);
        return false;
    },
}));
