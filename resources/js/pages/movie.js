import Alpine from "alpinejs";
import $ from "jquery";
import numeral from "numeral";

import recommendations from "./titles/recommendations";
import similar from "./titles/similar";

Alpine.data("recommendations", recommendations);
Alpine.data("similar", similar);
Alpine.store("title", { id: null, media_type: null });

Alpine.data("movie", (data) => ({
    title: {},

    movie_id: null,
    inWatchlist: null,

    loading: true,
    error: false,

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        if (!this.initialValidation()) return;
        this.initialSetup();
        this.fetchTitle();
    },

    initialValidation() {
        const { id } = data;

        if (!id) {
            return this.handleError("Invalid ID");
        }

        return true;
    },

    initialSetup() {
        const { id, media_type = "movie", inWatchlist = false } = data;

        this.movie_id = Number(id);

        const store = Alpine.store("title");
        store.id = this.movie_id;
        store.media_type = media_type;

        this.inWatchlist = inWatchlist;
    },

    fetchTitle(attempt = 1) {
        if (!this.movie_id) {
            this.loading = false;
            this.error = true;
            return;
        }

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: `/api/movie/${this.movie_id}`,
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
        console.error("Error in Movie:", message);
        return false;
    },
}));
