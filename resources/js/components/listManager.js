import Alpine from "alpinejs";
import $ from "jquery";

const DEFAULT_CREATE_FORM = () => ({
    name: "",
    description: "",
    iso_639_1: "en",
    public: false,
});

export default () => ({
    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    createListOption: false,
    createForm: DEFAULT_CREATE_FORM(),
    loadingOnCreate: false,

    open: false,
    search: "",
    activeIndex: -1,

    loading: false,
    error: false,
    errorMessage: "",
    lists: [],
    initialized: false,

    title: null,

    get filtered() {
        return this.lists.filter((l) =>
            l.name.toLowerCase().includes(this.search.toLowerCase()),
        );
    },

    get activeTitle() {
        return Alpine.store("title") || this.title;
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
                this._retryOrFail(
                    attempt,
                    `Request failed (${xhr.status || "network error"})`,
                );
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

    select(addToList = true) {
        const selected = this.filtered[this.activeIndex];
        if (this.activeIndex < 0 || !selected) return;

        const { id: list_id } = selected;
        const { id: media_id, media_type } = this.activeTitle;

        this.handleListToggle({ list_id, media_id, media_type }, addToList);
        this.$dispatch("list:update-list-option", {
            id: list_id,
            inUserList: addToList,
        });

        this.closeDialog();
    },

    onSearchInput() {
        this.activeIndex = -1;
    },

    onSearchClick(e) {
        e.stopPropagation();
    },

    openDialog(title) {
        this.title = title;
        if (Alpine.store("db").route === "lists") {
            this.createListOption = true;
        }

        this.open = true;
        this.activeIndex = -1;
        this.search = "";
        if (!this.initialized) this.fetch();

        this.$nextTick(() => this.$dispatch("list:title-changed", this.title));
    },

    closeDialog() {
        this.open = false;
        this.title = null;
        this.search = "";
        this.createForm = DEFAULT_CREATE_FORM();
        this.createListOption = false;
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

        this.loadingOnCreate = true;

        $.ajax({
            url: "/api/lists",
            method: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            success: (res) => {
                if (!res.success) {
                    this.handleError(res?.message || "Failed to create list");
                    return;
                }

                const title = this.activeTitle;

                this.addSelectedTitleToList(res.data.id, title);

                this.closeDialog();

                const data = {
                    ...res.data,
                    favorite_count: 0,
                    item_count: 0,
                    list_type: null,
                    poster_path: null,
                };

                this.$dispatch("list:add-new", data);
            },
            error: (xhr) => {
                this.handleFailedRequest(xhr);
                this.closeDialog();
            },

            complete: () => {
                this.loadingOnCreate = false;
            },
        });
    },

    addSelectedTitleToList(list_id, title = null) {
        if (!list_id) return;

        const routes = ["movie.show", "tv.show", "watchlist"];
        if (!routes.includes(Alpine.store("db").route)) return;

        const t = title || this.activeTitle;

        const media_id = t?.id;
        const media_type = t?.media_type;

        if (!media_id || !media_type) return;

        this.addToList({ list_id, media_id, media_type });
    },

    addToList(data) {
        this.handleListToggle(data, true);
    },

    removeFromList(data) {
        this.handleListToggle(data, false);
    },

    handleListToggle(data, addToList) {
        if (!this.validateToggleListData(data)) return;

        const { list_id, media_id, media_type } = data;

        $.ajax({
            url: `/api/lists/${list_id}/items`,
            method: addToList ? "POST" : "DELETE",
            contentType: "application/json",
            data: JSON.stringify({ items: [{ media_type, media_id }] }),
            success: () => {
                const userLists = Alpine.store("db").user_lists ?? [];
                const list = userLists.find((l) => l.id === list_id);
                if (list) {
                    if (addToList) {
                        list.items = [
                            ...(list.items ?? []),
                            { media_id, media_type },
                        ];
                    } else {
                        list.items = (list.items ?? []).filter(
                            (i) =>
                                !(
                                    i.media_id === media_id &&
                                    i.media_type === media_type
                                ),
                        );
                    }
                }

                this.$dispatch("toast", {
                    type: "success",
                    title: `${addToList ? "Added to" : "Removed from"} list successfully.`,
                });
            },
            error: (xhr) => {
                this.handleFailedRequest(xhr);
            },
        });
    },

    deleteList(list_id) {
        if (!Number.isInteger(list_id)) {
            return this.handleError("Invalid or missing list_id");
        }

        $.ajax({
            url: `/api/lists/${list_id}`,
            method: "DELETE",
            success: () => {
                window.location.href = "/lists";
            },
            error: (xhr) => {
                this.handleFailedRequest(xhr);
            },
        });
    },

    clearList(list_id) {
        if (!Number.isInteger(list_id)) {
            return this.handleError("Invalid or missing list_id");
        }

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
                this.handleFailedRequest(xhr);
            },
        });
    },

    validateToggleListData(data) {
        const { list_id, media_id, media_type } = data;

        if (!Number.isInteger(list_id)) {
            return this.handleError("Invalid or missing list_id");
        }
        if (!Number.isInteger(media_id)) {
            return this.handleError("Invalid or missing media_id");
        }
        if (!["movie", "tv"].includes(media_type)) {
            return this.handleError("Invalid media_type (must be movie or tv)");
        }

        return true;
    },

    handleFailedRequest(xhr) {
        this.$dispatch("toast", {
            type: "error",
            title: `Request failed (${xhr.status || "network error"})`,
        });
    },

    handleError(message) {
        this.$dispatch("toast", { type: "error", title: message });
        console.error("[List] Error:", message);
        return false;
    },
});
