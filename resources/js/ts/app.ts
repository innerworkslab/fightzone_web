import "../../css/app.css";
import { createApp } from "vue";
import App from "../../views/components/App.vue";
import { createPinia } from "pinia";
import "vue3-toastify/dist/index.css";
import Vue3Toastify from "vue3-toastify";
import router from "./router/index.route";
import { VueFire } from "vuefire";
import { firebaseApp } from "@/lib/firebase";

const app = createApp(App);

app.use(Vue3Toastify);
app.use(router);
app.use(createPinia());
if (firebaseApp) {
    app.use(VueFire, { firebaseApp });
}

app.mount("#app");
