import "./bootstrap";
import Alpine from "alpinejs";
import registerAlpinePlugins from "./plugins";
import registerTheme from "./theme";
import registerStores from "./stores";
import registerUIComponents from "./ui";
import registerComponents from "./components";

window.Alpine = Alpine;
Alpine.start();
