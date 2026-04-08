import $ from "jquery";

export default (id, media_type) => ({
    id,
    media_type,

    results: [],

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    loading: true,
    error: false,

    initialValidation() {
        if (typeof this.id !== "number" || isNaN(this.id)) {
            this.handleError("Invalid ID");
            return false;
        }

        const allowedTypes = ["movie", "tv"];
        if (!allowedTypes.includes(this.media_type)) {
            this.handleError(
                `Invalid media_type "${this.media_type}". Expected: ${allowedTypes.join(", ")}.`,
            );
            return false;
        }

        return true;
    },

    fetch(attempt = 1) {
        if (!this.initialValidation()) return;

        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: `/api/${this.media_type}/${this.id}/recommendations`,
            method: "GET",

            success: (res) => {
                if (res.success) {
                    this.results = res.data.results.map((item) => ({
                        ...item,
                        media_type: this.media_type,
                    }));
                    this.loading = false;
                } else {
                    this._retryOrFail(attempt);
                }
            },

            error: () => {
                this._retryOrFail(attempt);
            },
        });
    },

    _retryOrFail(attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(
                () => this.fetch(attempt + 1),
                this.RETRY_DELAY_MS * attempt,
            );
        } else {
            this.loading = false;
            this.error = true;
        }
    },

    handleError(message) {
        console.error("Error in recommendations:", message);
    },
});
