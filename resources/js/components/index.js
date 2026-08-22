import $ from "jquery";
import searchComponent from "./search";
import listManager from "./listManager";

export default function registerComponents(Alpine) {
    Alpine.data("search", searchComponent);

    Alpine.data("telegram", () => ({
        share() {
            window.location =
                "tg://msg_url?url=" +
                encodeURIComponent(this.$el.dataset.title);
            setTimeout(
                () =>
                    window.open(
                        "https://t.me/share/url?url=" +
                            encodeURIComponent(this.$el.dataset.title),
                        "_blank",
                    ),
                500,
            );
        },
    }));

    Alpine.data("titleCard", (title) => ({
        title,
        inWatchlist: false,

        init() {
            const watchlist = Alpine.store("db").watchlist;

            this.inWatchlist = watchlist[this.title.media_type].includes(
                title.id,
            );
        },

        updateWatchlist(event) {
            const { media_id, media_type, watchlist } = event.detail;
            if (
                media_id !== this.title.id ||
                media_type !== this.title.media_type
            )
                return;

            this.inWatchlist = watchlist;
        },
    }));

    Alpine.data("watchlistManager", () => ({
        loading: false,
        error: false,

        add(event) {
            if (!this.validateEventdata(event)) return;

            const { media_id, media_type } = event.detail;
            const store = Alpine.store("watchlist");

            if (store.has(media_id, media_type, "added")) {
                return this.handleError("Already tracked as added");
            }

            store.add(media_id, media_type);
            this.update(event, 1);
        },

        remove(event) {
            if (!this.validateEventdata(event)) return;

            const { media_id, media_type } = event.detail;
            const store = Alpine.store("watchlist");

            if (store.has(media_id, media_type, "removed")) {
                return this.handleError("Already tracked as removed");
            }

            store.remove(media_id, media_type);
            this.update(event, 0);
        },

        update(event, watchlist) {
            const { media_id, media_type } = event.detail;
            const store = Alpine.store("watchlist");
            const user_id = Alpine.store("db").user_id;
            const page = Alpine.store("db").route;

            this.loading = true;
            this.error = false;

            // Optimistic UI
            if (["movie.show", "tv.show"].includes(page)) {
                this.$dispatch("sync:watchlist", watchlist);
            }
            if (page === "watchlist") {
                this.$dispatch("delete:soft", { media_id, media_type });
            }
            this.$dispatch("title-card:sync-watchlist", {
                media_id: media_id,
                media_type: media_type,
                watchlist,
            });

            $.ajax({
                url: "/api/watchlist",
                method: "POST",
                data: { media_id, media_type, watchlist, user_id },
                success: (res) => {
                    this.loading = false;

                    if (!res.success) {
                        return this._rollback(
                            watchlist,
                            media_id,
                            media_type,
                            page,
                            store,
                        );
                    }

                    this.$dispatch("toast", {
                        type: "success",
                        title: "Watchlist updated successfully",
                    });

                    if (page === "watchlist") {
                        this.$dispatch("delete:permanent", {
                            media_id,
                            media_type,
                        });
                    }
                },
                error: (xhr) => {
                    this.loading = false;
                    this._rollback(
                        watchlist,
                        media_id,
                        media_type,
                        page,
                        store,
                    );
                },
            });
        },

        _rollback(watchlist, media_id, media_type, page, store) {
            this.error = true;

            if (["movie.show", "tv.show"].includes(page)) {
                this.$dispatch("sync:watchlist", !watchlist);
            }
            if (page === "watchlist") {
                this.$dispatch("delete:rollback", { media_id, media_type });
            }

            this.$dispatch("title-card:sync-watchlist", {
                media_id: media_id,
                media_type: media_type,
                watchlist: !watchlist,
            });

            watchlist === 1
                ? store.remove(media_id, media_type)
                : store.add(media_id, media_type);

            this.$dispatch("toast", {
                type: "error",
                title: "Request failed, changes rolled back",
            });
        },

        validateEventdata(event) {
            const { media_id, media_type } = event.detail;

            if (!media_id || !["movie", "tv"].includes(media_type)) {
                return this.handleError("Invalid event data");
            }

            if (!Alpine.store("db").user_id) {
                return this.handleError("User ID is required");
            }

            return true;
        },

        handleError(message, context = {}) {
            this.$dispatch("toast", {
                type: "error",
                title: message,
            });

            console.error("[Watchlist] Error:", message, context);
            return false;
        },
    }));

    Alpine.data("watchedManager", () => ({
        add(event) {
            if (!this.validateEventdata(event)) return;
            this.update(event, true);
        },

        remove(event) {
            if (!this.validateEventdata(event)) return;
            this.update(event, false);
        },

        update(event, watched) {
            const { media_id, media_type } = event.detail;

            this.handleSuccess("Watched status updated successfully");
            this.$dispatch("sync:watched", watched);

            $.ajax({
                url: "/api/watched",
                method: "POST",
                data: { media_id, media_type, watched: watched ? 1 : 0 },
                success: (res) => {
                    if (!res.success) {
                        this.handleError("Request unsuccessful", {
                            media_id,
                            media_type,
                        });
                        this.$dispatch("sync:watched", !watched);
                    }
                },
                error: () => {
                    this.handleError("Request failed");
                    this.$dispatch("sync:watched", !watched);
                },
            });
        },

        validateEventdata(event) {
            const { media_id, media_type } = event.detail;

            if (!media_id || !["movie", "tv"].includes(media_type)) {
                return this.handleError("Invalid event data");
            }

            return true;
        },

        handleSuccess(message) {
            this.$dispatch("toast", {
                type: "success",
                title: message,
            });
        },

        handleError(message) {
            this.$dispatch("toast", {
                type: "error",
                title: message,
            });
            console.error("[Watched] Error:", message);
            return false;
        },
    }));

    Alpine.data("watchlistButton", () => ({
        MAX_RETRIES: 3,
        RETRY_DELAY_MS: 1000,

        open: false,
        search: "",
        activeIndex: -1,

        loading: false,
        error: false,
        errorMessage: "",
        lists: [],
        initialized: false,

        get filtered() {
            return this.lists.filter((l) =>
                l.name.toLowerCase().includes(this.search.toLowerCase()),
            );
        },

        fetch(attempt = 1) {
            if (attempt === 1 && this.loading) return;
            if (attempt === 1) {
                this.loading = true;
                this.error = false;
                this.errorMessage = "";
            }

            $.ajax({
                url: "/api/lists",
                method: "GET",
                success: (res) => {
                    if (res?.data?.results) {
                        this.lists = res.data.results;
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
            }
        },

        navigate(dir) {
            if (!this.open) return;
            this.activeIndex = Math.max(
                -1,
                Math.min(this.activeIndex + dir, this.filtered.length - 1),
            );
            this.$nextTick(() => {
                this.$refs.listContainer
                    ?.querySelector(`[data-index='${this.activeIndex}']`)
                    ?.scrollIntoView({ block: "nearest" });
            });
        },

        select() {
            if (this.activeIndex >= 0 && this.filtered[this.activeIndex]) {
                const selected = this.filtered[this.activeIndex];

                this.$dispatch("list:add", {
                    list_id: selected.id,
                    media_id: Alpine.store("title").id,
                    media_type: Alpine.store("title").media_type,
                });

                this.open = false;
            }
        },

        openDropdown() {
            this.open = true;
            this.activeIndex = -1;
            if (!this.initialized) this.fetch();
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        onSearchInput() {
            this.activeIndex = -1;
        },

        onSearchClick(e) {
            e.stopPropagation();
        },
    }));

    Alpine.data("xlistManager", () => ({
        isCreateDialogOpen: false,
        isListDialogOpen: !false,

        createForm: {
            name: "",
            description: "",
            iso_639_1: "en",
            public: false,
        },

        openCreateDialog() {
            this.createForm = {
                name: "",
                description: "",
                iso_639_1: "en",
                public: false,
            };
            this.isCreateDialogOpen = true;
        },

        closeCreateDialog() {
            this.isCreateDialogOpen = false;
        },

        openListDialog() {
            this.isListDialogOpen = true;
        },

        closeListDialog() {
            this.isListDialogOpen = false;
        },

        add(event) {
            if (!this.validateToggleEventdata(event)) return;
            this.update(event, true);
        },

        remove(event) {
            if (!this.validateToggleEventdata(event)) return;
            this.update(event, false);
        },

        update(data, action) {
            const { list_id, media_id, media_type } = data.detail ?? data;

            const payload = {
                items: [{ media_type, media_id }],
            };

            $.ajax({
                url: `/api/lists/${list_id}/items`,
                method: action ? "POST" : "DELETE",
                contentType: "application/json",
                data: JSON.stringify(payload),
                success: () => {
                    this.$dispatch("toast", {
                        type: "success",
                        title: action ? "Added to list" : "Removed from list",
                    });
                },
                error: (xhr) => {
                    this.$dispatch("toast", {
                        type: "error",
                        title: `Request failed (${xhr.status || "network error"})`,
                    });
                },
            });
        },

        createList(data) {
            const payload = {
                name: data.name,
                iso_639_1: data.iso_639_1 ?? "en",
                description: data.description ?? "",
                public: data.public ?? false,
            };

            if (!payload.name?.trim()) {
                return this.handleError("List name is required.");
            }

            $.ajax({
                url: "/api/lists",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(payload),
                success: (res) => {
                    if (res.success) {
                        this.$dispatch("toast", {
                            type: "success",
                            title: "List created successfully",
                        });
                        this.$dispatch("list:created");
                        this.addTitleToList(res.data.id);
                    }
                },
                error: (xhr) => {
                    this.$dispatch("toast", {
                        type: "error",
                        title: `Request failed (${xhr.status || "network error"})`,
                    });
                },
            });

            this.closeCreateDialog();
        },

        clearList(list_id) {
            console.log(list_id);
            if (!this.validateListId(list_id)) return;

            $.ajax({
                url: `/api/lists/${list_id}/clear`,
                method: "POST",
                success: () => {
                    this.$dispatch("toast", {
                        type: "success",
                        title: "Cleared list successfully",
                    });
                },
                error: (xhr) => {
                    this.$dispatch("toast", {
                        type: "error",
                        title: `Request failed (${xhr.status || "network error"})`,
                    });
                },
            });
        },

        deleteList(list_id) {
            if (!this.validateListId(list_id)) return;

            $.ajax({
                url: `/api/lists/${list_id}`,
                method: "DELETE",
                success: () => {
                    window.location.href = "/lists";
                },
                error: (xhr) => {
                    this.$dispatch("toast", {
                        type: "error",
                        title: `Request failed (${xhr.status || "network error"})`,
                    });
                },
            });
        },

        validateToggleEventdata(event) {
            const { list_id, media_id, media_type } = event.detail ?? {};

            if (!Number.isInteger(list_id)) {
                return this.handleError("Invalid or missing list_id");
            }

            if (!Number.isInteger(media_id)) {
                return this.handleError("Invalid or missing media_id");
            }

            if (!["movie", "tv"].includes(media_type)) {
                return this.handleError(
                    "Invalid media_type (must be movie or tv)",
                );
            }

            return true;
        },

        addTitleToList(list_id) {
            if (!["movie.show", "tv.show"].includes(Alpine.store("db").route))
                return;

            const data = {
                list_id,
                media_id: Alpine.store("title").id,
                media_type: Alpine.store("title").media_type,
            };

            this.update(data, true);
        },

        validateListId(list_id) {
            if (!Number.isInteger(list_id)) {
                return this.handleError("Invalid or missing list_id");
            }

            return true;
        },

        handleError(message) {
            this.$dispatch("toast", { type: "error", title: message });
            console.error("[List] Error:", message);
            return false;
        },
    }));

    Alpine.data("listManager", listManager);
}
