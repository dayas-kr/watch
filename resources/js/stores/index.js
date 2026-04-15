export default function registerStores(Alpine) {
    Alpine.store("toast", {
        items: [],
        counter: 0,
        MAX: 8,

        add(data) {
            const id = this.counter++;

            const toast = {
                id,
                type: data.type || "default",
                title: data.title || "",
                description: data.description || "",
                timeout: data.timeout ?? 2000,
            };

            if (this.items.length >= this.MAX) {
                this.items.shift();
            }

            this.items.push(toast);

            // auto remove
            setTimeout(() => {
                this.remove(id);
            }, toast.timeout);
        },

        remove(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });

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
}
