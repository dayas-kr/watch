import anchor from "@alpinejs/anchor";
import focus from "@alpinejs/focus";
import intersect from "@alpinejs/intersect";

export default function registerAlpinePlugins(Alpine) {
    Alpine.plugin(anchor);
    Alpine.plugin(focus);
    Alpine.plugin(intersect);
}
