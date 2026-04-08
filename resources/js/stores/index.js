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
}
