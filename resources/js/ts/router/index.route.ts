import { RouteNames } from "../config/route.config";
import { createRouter, createWebHistory } from "vue-router";
import Login from "../../../views/components/login/Login.vue";
import Unauthorized from "../../../views/components/unauthorized/Unauthorized.vue";
import AdminList from "../../../views/components/admin/AdminList.vue";
import AdminForm from "../../../views/components/admin/AdminForm.vue";
import UserList from "../../../views/components/user/UserList.vue";
import UserForm from "../../../views/components/user/UserForm.vue";
import PaymentList from "../../../views/components/payment/PaymentList.vue";
import PaymentForm from "../../../views/components/payment/PaymentForm.vue";
import { getDecryptedCookie } from "@/lib/utils.cookies";
import { COOKIES, LOCALSTORAGE } from "@/constant/constant.global";
import { getDecryptedLocalStorage } from "@/lib/utils.localStorage";

const routes = [
    {
        path: "/",
        name: RouteNames.Login,
        component: Login,
    },
    {
        path: "/unauthorized",
        name: RouteNames.Unauthorized,
        component: Unauthorized,
    },
    {
        path: "/auth",
        component: () =>
            import("../../../views/components/layouts/RootLayout.vue"),
        meta: { requiresAuth: true },
        children: [
            {
                path: "admin",
                name: RouteNames.AdminList,
                component: AdminList,
            },
            {
                path: "admin/add",
                name: RouteNames.AddAdmin,
                component: AdminForm,
                meta: { permissions: ["admin", "all"] },
            },
            {
                path: "admin/edit/:id",
                name: RouteNames.EditAdmin,
                component: AdminForm,
                meta: { permissions: ["admin", "all"] },
            },
            {
                path: "admin/view/:id",
                name: RouteNames.ViewAdmin,
                component: AdminForm,
                meta: { permissions: ["admin", "all"] },
            },
            {
                path: "user",
                name: RouteNames.UserList,
                component: UserList,
                meta: { permissions: ["user", "all"] },
            },
            {
                path: "user/add",
                name: RouteNames.AddUser,
                component: UserForm,
                meta: { permissions: ["user", "all"] },
            },
            {
                path: "user/edit/:id",
                name: RouteNames.EditUser,
                component: UserForm,
                meta: { permissions: ["user", "all"] },
            },
            {
                path: "user/view/:id",
                name: RouteNames.ViewUser,
                component: UserForm,
                meta: { permissions: ["user", "all"] },
            },
            {
                path: "payment",
                name: RouteNames.PaymentList,
                component: PaymentList,
                meta: { permissions: ["payment", "all"] },
            },
            {
                path: "payment/add",
                name: RouteNames.AddPayment,
                component: PaymentForm,
                meta: { permissions: ["payment", "all"] },
            },
            {
                path: "payment/edit/:id",
                name: RouteNames.EditPayment,
                component: PaymentForm,
                meta: { permissions: ["payment", "all"] },
            },
            {
                path: "payment/view/:id",
                name: RouteNames.ViewPayment,
                component: PaymentForm,
                meta: { permissions: ["payment", "all"] },
            },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const requiresAuth = to.meta.requiresAuth;
    const accessToken = getDecryptedCookie(COOKIES.ACCESS_TOKEN);
    const isAuthenticated = !!accessToken;
    const userPermissions =
        getDecryptedLocalStorage(LOCALSTORAGE.PERMISSIONS) || [];

    const hasRequiredPermission = (requiredPermissions: string[]): boolean => {
        // console.log("User Permissions:", userPermissions);
        // console.log("Required Permissions:", requiredPermissions);

        const hasFullAccess = userPermissions.some(
            (p: any) => p.permission_type && p.permission_type.name === "all"
        );

        if (hasFullAccess) {
            return true;
        }

        if (!requiredPermissions || requiredPermissions.length === 0) {
            return true;
        }

        return requiredPermissions.some((requiredPerm) =>
            userPermissions.some(
                (userPerm: any) =>
                    userPerm.permission_type_name &&
                    userPerm.permission_type_name === requiredPerm
            )
        );
    };

    if (requiresAuth) {
        if (!isAuthenticated) {
            next({ name: RouteNames.Login });
        } else {
            const requiredPermissions = to.meta.permissions as
                | string[]
                | undefined;

            if (
                requiredPermissions &&
                !hasRequiredPermission(requiredPermissions)
            ) {
                next({ name: RouteNames.Unauthorized });
            } else {
                next();
            }
        }
    } else {
        if (to.name === RouteNames.Login && isAuthenticated) {
            next({ name: RouteNames.AdminList });
        } else {
            next();
        }
    }
});

export default router;
