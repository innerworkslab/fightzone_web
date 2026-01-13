import "../../css/app.css";
import { createApp } from "vue";
import App from "../../views/components/App.vue";
import { createPinia } from "pinia";
import "vue3-toastify/dist/index.css";
import Vue3Toastify from "vue3-toastify";
import router from "./router/index.route";

const app = createApp(App);

app.use(Vue3Toastify);
app.use(router);
app.use(createPinia());

app.mount("#app");
