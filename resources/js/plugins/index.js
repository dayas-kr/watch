import anchor from "@alpinejs/anchor";
import focus from "@alpinejs/focus";

export default function registerAlpinePlugins(Alpine) {
    Alpine.plugin(anchor);
    Alpine.plugin(focus);
}
