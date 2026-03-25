import "./bootstrap";
import Alpine from "alpinejs";
import registerAlpinePlugins from "./plugins";
import registerTheme from "./theme";
import registerStores from "./stores";
import registerUIComponents from "./ui";
import registerComponents from "./components";

registerAlpinePlugins(Alpine);
registerTheme(Alpine);
registerStores(Alpine);
registerUIComponents(Alpine);
registerComponents(Alpine);

window.Alpine = Alpine;
Alpine.start();
