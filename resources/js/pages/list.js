import Alpine from "alpinejs";
import $ from "jquery";

Alpine.data("list", (id) => ({
    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    loading: false,
    error: false,
    errorMessage: "",

    data: null,
    items: [],
    page: 1,
    totalPages: 1,
    initialized: false,

    hasMore() {
        return this.page <= this.totalPages;
    },

    init() {
        this.fetch();
    },

    fetch(attempt = 1) {
        // Guard: don't fetch if there are no more pages
        if (!this.hasMore()) return;

        // Guard: prevent duplicate in-flight requests only on fresh calls
        if (attempt === 1 && this.loading) return;

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
            this.errorMessage = "";
        }

        $.ajax({
            url: `/api/lists/${id}`,
            method: "GET",
            data: { page: this.page },

            success: (res) => {
                if (res?.data) {
                    this.data = res.data;

                    const existingIds = new Set(this.items.map((i) => i.id));
                    const filtered = (res.data?.items ?? []).filter(
                        (i) => !existingIds.has(i.id),
                    );
                    this.items.push(...filtered);

                    this.totalPages = res.data?.total_pages ?? 1;

                    // Increment page AFTER updating totalPages so hasMore() is accurate
                    this.page++;

                    this.loading = false;
                    this.initialized = true;
                } else {
                    this._retryOrFail(
                        attempt,
                        res?.message || "Unexpected response",
                    );
                }
            },

            error: (xhr) => {
                const message =
                    xhr.responseJSON?.status_message ??
                    xhr.responseJSON?.message ??
                    `Request failed (${xhr.status || "network error"})`;

                this._retryOrFail(attempt, message);
            },
        });
    },

    fetchMore() {
        this.fetch();
    },

    _retryOrFail(attempt, message) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(
                () => this.fetch(attempt + 1),
                this.RETRY_DELAY_MS * attempt,
            );
        } else {
            this.loading = false;
            this.error = true;
            this.errorMessage = message;

            console.error("List failed:", message);
        }
    },

    handleError(message) {
        console.error("List Error:", message);
    },
}));
