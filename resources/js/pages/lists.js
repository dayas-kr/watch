import Alpine from "alpinejs";
import $ from "jquery";

Alpine.data("lists", () => ({
    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    loading: false,
    error: false,
    errorMessage: "",

    data: [],
    page: 1,
    totalPages: 1,
    initialized: false,

    init() {
        this.fetch();
    },

    hasMore() {
        return this.page <= this.totalPages;
    },

    fetch(attempt = 1) {
        if (!this.hasMore()) return;

        if (attempt === 1 && this.loading) return;

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
            this.errorMessage = "";
        }

        $.ajax({
            url: "/api/lists",
            method: "GET",
            data: {
                page: this.page,
            },

            success: (res) => {
                if (res?.data?.results) {
                    const existingIds = new Set(this.data.map((i) => i.id));

                    const filtered = res.data.results.filter(
                        (i) => !existingIds.has(i.id),
                    );

                    this.data.push(...filtered);
                    this.totalPages = res.data.total_pages;
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

    addNewCreatedList(list) {
        if (!list || !list.id) return;

        const exists = this.data.some((i) => i.id === list.id);
        if (exists) return;

        this.data = [list, ...this.data];
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

            console.error("Lists failed:", message);
        }
    },

    handleError(message) {
        console.error("Lists Error:", message);
    },
}));
