import { createApp } from "vue";
import App from "./App.vue"; // Le composant racine
import router from "./router"; // La configuration router
import Vue3Toastify from "vue3-toastify"; // 1. Import du plugin
import "vue3-toastify/dist/index.css"; // 2. Import du CSS des bulles

// 1. On crée l'instance de l'application
const app = createApp(App); // On nomme l'instance "app" (minuscule)

// 2. On installe le router sur l'instance
app.use(router);
app.use(Vue3Toastify, { autoClose: 3000 });

// 3. On monte l'application sur la page HTML
app.mount("#app");
