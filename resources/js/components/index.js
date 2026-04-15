import $ from "jquery";
import searchComponent from "./search";

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

    Alpine.store("db", {
        user_id: null,
        route: null,

        watchlist: [],
        favorites: [],
        watched: [],

        init() {
            this.route = this.setRoute();
            this.user_id = this.setUserId();
        },

        setRoute() {
            const pathname = new URL(window.location.href).pathname;

            const staticRoutes = {
                "/": "home",
                "/watchlist": "watchlist",
            };

            if (staticRoutes[pathname]) {
                return staticRoutes[pathname];
            }

            const match = pathname.match(/^\/(movie|tv)\/(\d+)/);

            if (match) {
                const [, type] = match;
                return `${type}.show`;
            }

            return "unknown";
        },

        setUserId() {
            return document.body.dataset.userId ?? null;
        },
    });

    Alpine.data("titleCard", (title) => ({
        title,
        inWatchlist: false,

        init() {
            const watchlist = Alpine.store("db").watchlist;

            this.inWatchlist = watchlist.includes(title.id);
        },

        updateWatchlist(event) {
            const { id, watchlist } = event.detail;

            if (id !== this.title.id) return;

            this.inWatchlist = watchlist;
        },
    }));

    Alpine.store("watchlist", {
        items: new Set(),

        key(media_id, media_type, action) {
            return `${media_id}-${media_type}-${action}`;
        },

        has(media_id, media_type, action) {
            return this.items.has(this.key(media_id, media_type, action));
        },

        add(media_id, media_type) {
            this.items.delete(this.key(media_id, media_type, "removed"));
            this.items.add(this.key(media_id, media_type, "added"));
        },

        remove(media_id, media_type) {
            this.items.delete(this.key(media_id, media_type, "added"));
            this.items.add(this.key(media_id, media_type, "removed"));
        },
    });

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
                id: media_id,
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
                id: media_id,
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
}
