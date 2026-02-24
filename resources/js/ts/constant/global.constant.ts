import { Methods } from "@/type.global";

export const METHODS: Methods = {
    GET: "GET",
    POST: "POST",
    PUT: "PUT",
    PATCH: "PATCH",
    DELETE: "DELETE",
};

export const API_URLS = {
    VERSION: "v1",
    MANAGEMENT: "management",
    LOGIN: "login",
    TOGGLE: "toggle",
    ADMIN: "admins",
    USER: "users",
    PAYMENT: "payment-methods",
    PACKAGE: "packages",
    PURCHASE: "purchases",
    DEPOSIT: "deposits",
    COURSE: "courses",
    COURSE_CATEGORY: "course-categories",
    COURSE_LEVEL: "course-levels",
    LESSON_DAY: "lesson-days",
    LESSON_DAY_VIDEO: "lesson-day-videos",
};

export const COOKIES = {
    ACCESS_TOKEN: "ACCESS_TOKEN",
};

export const LOCALSTORAGE = {
    PERMISSIONS: "PERMISSIONS",
    AUTH_USER: "AUTH_USER",
};

export const STATUS = {
    SUCCESS: "success",
};

export const Keys = {
    AUTH: "auth",
};

export const SUCCESS_MESSAGE = {
    VERIFIED: "Verified successful.",
    ACTIVATED: "Activated successful.",
    INACTIVATED: "Inactivated successful.",
};

export const DEFAULT_PAGE_LIMIT = 20;
