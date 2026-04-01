import Alpine from "alpinejs";
import $ from "jquery";

Alpine.data("featuredTabs", () => ({
    activeTab: "movie",

    tabs: {
        movie: "Movies",
        tv: "TV Shows",
    },

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    movie: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },
    tv: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },

    isTab(tab) {
        return this.activeTab === tab;
    },

    onTabChange(tab) {
        this.activeTab = tab;

        if (!this[tab].initialized) {
            this.fetch(tab);
        }
    },

    init() {
        this.fetch(this.activeTab);
    },

    fetch(type, attempt = 1) {
        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
        }

        $.ajax({
            // url: `/api/${type}/popular`,
            url: `/api/trending/${type}/day`,
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this[type].results = res.data.results;
                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(type, attempt);
                }
            },

            error: () => {
                this._retryOrFail(type, attempt);
            },
        });
    },

    _retryOrFail(type, attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;

            console.error(`Featured [${type}] failed`);
        }
    },

    retry(type) {
        this.fetch(type, 1);
    },
}));

Alpine.data("continueWatching", () => ({
    init() {
        console.log("continueWatching initialized.");
    },
}));

Alpine.data("trendingTabs", () => ({
    activeTab: "day",

    timeWindows: {
        day: "Today",
        week: "This Week",
    },

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    day: { loading: false, error: false, results: [], initialized: false },
    week: { loading: false, error: false, results: [], initialized: false },

    isTab(tab) {
        return this.activeTab === tab;
    },

    onTabChange(tab) {
        this.activeTab = tab;

        if (!this[tab].initialized) {
            this.fetch(tab);
        }
    },

    init() {
        this.fetch(this.activeTab);
    },

    fetch(type, attempt = 1) {
        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
        }

        $.ajax({
            url: `/api/trending/all/${type}`,
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this[type].results = res.data.results;
                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(type, attempt);
                }
            },

            error: () => {
                this._retryOrFail(type, attempt);
            },
        });
    },

    _retryOrFail(type, attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;

            console.error(`Trending [${type}] failed`);
        }
    },

    retry(type) {
        this.fetch(type, 1);
    },
}));

Alpine.data("nowPlayingMovies", () => ({
    loading: false,
    error: false,
    data: [],

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        this.fetch();
    },

    fetch(attempt = 1) {
        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: "/api/movie/now_playing",
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this.data = res.data.results;
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
            setTimeout(() => {
                this.fetch(attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this.loading = false;
            this.error = true;

            console.error("Now Playing fetch failed");
        }
    },

    retry() {
        this.fetch(1);
    },
}));

Alpine.data("airingToday", () => ({
    loading: false,
    error: false,
    data: [],

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        this.fetch();
    },

    fetch(attempt = 1) {
        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: "/api/tv/airing_today",
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this.data = res.data.results;
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
            setTimeout(() => {
                this.fetch(attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this.loading = false;
            this.error = true;

            console.error("Airing Today fetch failed");
        }
    },

    retry() {
        this.fetch(1);
    },
}));

Alpine.data("popularTabs", () => ({
    activeTab: "movie",

    tabs: {
        movie: "Movies",
        tv: "TV Shows",
    },

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    movie: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },
    tv: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },

    isTab(tab) {
        return this.activeTab === tab;
    },

    onTabChange(tab) {
        this.activeTab = tab;

        if (!this[tab].initialized) {
            this.fetch(tab);
        }
    },

    init() {
        this.fetch(this.activeTab);
    },

    fetch(type, attempt = 1) {
        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
        }

        $.ajax({
            url: `/api/${type}/popular`,
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this[type].results = res.data.results;
                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(type, attempt);
                }
            },

            error: () => {
                this._retryOrFail(type, attempt);
            },
        });
    },

    _retryOrFail(type, attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;

            console.error(`Popular [${type}] failed`);
        }
    },

    retry(type) {
        this.fetch(type, 1);
    },
}));

Alpine.data("topRatedList", () => ({
    activeTab: "movie",

    tabs: {
        movie: "Movies",
        tv: "TV Shows",
    },

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    movie: { loading: false, error: false, data: [], initialized: false },
    tv: { loading: false, error: false, data: [], initialized: false },

    isTab(tab) {
        return this.activeTab === tab;
    },

    setTab(tab) {
        this.activeTab = tab;

        if (!this[tab].initialized) {
            this.fetch(tab);
        }
    },

    init() {
        this.fetch(this.activeTab);
    },

    fetch(type, attempt = 1) {
        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
        }

        $.ajax({
            url: `/api/${type}/top_rated`,
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this[type].data = res.data.results;
                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(type, attempt);
                }
            },

            error: () => {
                this._retryOrFail(type, attempt);
            },
        });
    },

    _retryOrFail(type, attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;

            console.error(`Top Rated [${type}] failed`);
        }
    },

    retry(type) {
        this.fetch(type, 1);
    },
}));

Alpine.data("onTheAir", () => ({
    loading: false,
    error: false,
    data: [],

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        this.fetch();
    },

    fetch(attempt = 1) {
        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: "/api/tv/on_the_air",
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this.data = res.data.results;
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
            setTimeout(() => {
                this.fetch(attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this.loading = false;
            this.error = true;

            console.error("On The Air fetch failed");
        }
    },

    retry() {
        this.fetch(1);
    },
}));

Alpine.data("trendingPeople", () => ({
    loading: false,
    error: false,
    data: [],

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    init() {
        this.fetch();
    },

    fetch(attempt = 1) {
        if (attempt === 1) {
            this.loading = true;
            this.error = false;
        }

        $.ajax({
            url: "/api/trending/person/week",
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this.data = res.data.results;
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
            setTimeout(() => {
                this.fetch(attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this.loading = false;
            this.error = true;

            console.error("Trending People fetch failed");
        }
    },

    retry() {
        this.fetch(1);
    },
}));

Alpine.data("watchlistTabs", () => ({
    activeTab: "movie",

    tabs: {
        movie: "Movies",
        tv: "TV Shows",
    },

    MAX_RETRIES: 3,
    RETRY_DELAY_MS: 1000,

    movie: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },
    tv: {
        loading: false,
        error: false,
        results: [],
        initialized: false,
    },

    isTab(tab) {
        return this.activeTab === tab;
    },

    onTabChange(tab) {
        this.activeTab = tab;

        if (!this[tab].initialized) {
            this.fetch(tab);
        }
    },

    init() {
        this.fetch(this.activeTab);
    },

    fetch(type, attempt = 1) {
        if (attempt === 1) {
            this[type].loading = true;
            this[type].error = false;
        }

        $.ajax({
            url: `/api/watchlist/${type}`,
            method: "GET",

            success: (res) => {
                if (res?.data?.results) {
                    this[type].results = res.data.results;
                    this[type].loading = false;
                    this[type].initialized = true;
                } else {
                    this._retryOrFail(type, attempt);
                }
            },

            error: () => {
                this._retryOrFail(type, attempt);
            },
        });
    },

    _retryOrFail(type, attempt) {
        if (attempt < this.MAX_RETRIES) {
            setTimeout(() => {
                this.fetch(type, attempt + 1);
            }, this.RETRY_DELAY_MS * attempt);
        } else {
            this[type].loading = false;
            this[type].error = true;

            console.error(`Watchlist [${type}] failed`);
        }
    },

    retry(type) {
        this.fetch(type, 1);
    },
}));
