import Alpine from "alpinejs";
import $ from "jquery";
import numeral from "numeral";

import recommendations from "./titles/recommendations";
import similar from "./titles/similar";

Alpine.store("title", { id: null });

Alpine.data("recommendations", recommendations);
Alpine.data("similar", similar);

Alpine.data("tv", (id) => ({
    title: {},

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    loading: true,
    error: false,

    init() {
        this.fetchTitle();
    },

    initialSetup(id) {
        Alpine.store("title").id = Number(id);
        Alpine.store("title").media_type = "tv";
    },

    // Title
    fetchTitle(attempt = 1) {
        if (!id) {
            this.loading = false;
            this.error = true;
            return;
        }

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: `/api/tv/${id}`,
            method: "GET",
            data: { append_to_response: "credits" },
            success: (res) => {
                if (res.success) {
                    this.title = res.data;
                    this.loading = false;
                    console.log(res.data);
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

    // Helpers
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
    },
}));
