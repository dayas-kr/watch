import "./bootstrap";
import $ from "jquery";
import Alpine from "alpinejs";
import registerAlpinePlugins from "./plugins";
import registerTheme from "./theme";
import registerStores from "./stores";
import registerUIComponents from "./ui";
import registerComponents from "./components";

const token = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

$.ajaxSetup({ headers: { "X-CSRF-TOKEN": token } });

registerAlpinePlugins(Alpine);
registerTheme(Alpine);
registerStores(Alpine);
registerUIComponents(Alpine);
registerComponents(Alpine);

window.Alpine = Alpine;
Alpine.start();
