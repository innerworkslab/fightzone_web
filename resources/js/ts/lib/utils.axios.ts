/// <reference types="vite/client" />
import axios from "axios";
import { toast } from "vue3-toastify";
import { deleteLocalStorage } from "./utils.localStorage";
import { deleteCookie, getDecryptedCookie } from "./utils.cookies";
import { COOKIES, LOCALSTORAGE } from "@/constant/constant.global";

const apiUrl = import.meta.env.VITE_APP_URL || "";

const api = axios.create({
    baseURL: `${apiUrl}/api`,
    withCredentials: true,
});

function cleanParams(params: Record<string, any>) {
    if (!params || typeof params !== "object") return params;
    return Object.fromEntries(
        Object.entries(params).filter(
            ([, value]) => value !== null && value !== undefined && value !== ""
        )
    );
}

// Request interceptor
api.interceptors.request.use(
    (config) => {
        const token = getDecryptedCookie(COOKIES.ACCESS_TOKEN);
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        if (config.params) {
            config.params = cleanParams(config.params);
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        const { response, config } = error;
        console.log("response", response);

        if (response?.data?.response?.message) {
            toast.error(response.data.response.message);
        } else {
            toast.error("An unexpected error occurred.");
        }
        if (response?.status === 403) {
            window.location.href = "/unauthorized";
        }
        if (response?.status === 401 && config?.url !== "/management/login") {
            deleteCookie(COOKIES.ACCESS_TOKEN);
            deleteLocalStorage(LOCALSTORAGE.PERMISSIONS);
            window.location.href = "/";
        }

        return Promise.reject(error);
    }
);

export default api;
