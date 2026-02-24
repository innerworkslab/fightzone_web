import { RouteNames } from "../config/route.config";
import { createRouter, createWebHistory } from "vue-router";
import Login from "../../../views/components/login/Login.vue";
import Unauthorized from "../../../views/components/unauthorized/Unauthorized.vue";
import AdminsList from "../../../views/components/admins/AdminsList.vue";
import AdminForm from "../../../views/components/admins/AdminsForm.vue";
import UsersList from "../../../views/components/users/UsersList.vue";
import UserForm from "../../../views/components/users/UsersForm.vue";
import PaymentMethodsList from "../../../views/components/payment-methods/PaymentMethodsList.vue";
import PaymentForm from "../../../views/components/payment-methods/PaymentMethodsForm.vue";
import PackagesList from "../../../views/components/packages/PackagesList.vue";
import PurchasesList from "../../../views/components/purchases/PurchasesList.vue";
import DepositsList from "../../../views/components/deposits/DepositsList.vue";
import { getDecryptedCookie } from "@/lib/utils.cookies";
import { COOKIES, LOCALSTORAGE } from "@/constant/global.constant";
import { getDecryptedLocalStorage } from "@/lib/utils.localStorage";
import CoursesForm from "../../../views/components/courses/CoursesForm.vue";
import CoursesList from "../../../views/components/courses/CoursesList.vue";
import CourseCategoriesList from "../../../views/components/course-categories/CourseCategoriesList.vue";
import CourseLevelsForm from "../../../views/components/course-levels/CourseLevelsForm.vue";
import LessonDaysList from "../../../views/components/lesson-days/LessonDaysList.vue";
import LessonDayForm from "../../../views/components/lesson-days/LessonDayForm.vue";

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
                path: "admins",
                name: RouteNames.AdminsList,
                component: AdminsList,
            },
            {
                path: "admins/add",
                name: RouteNames.AddAdmin,
                component: AdminForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "admins/edit/:id",
                name: RouteNames.EditAdmin,
                component: AdminForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "admins/view/:id",
                name: RouteNames.ViewAdmin,
                component: AdminForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "users",
                name: RouteNames.UsersList,
                component: UsersList,
                meta: { permissions: ["all"] },
            },
            {
                path: "users/add",
                name: RouteNames.AddUser,
                component: UserForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "users/edit/:id",
                name: RouteNames.EditUser,
                component: UserForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "users/view/:id",
                name: RouteNames.ViewUser,
                component: UserForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "payment-methods",
                name: RouteNames.PaymentMethodsList,
                component: PaymentMethodsList,
                meta: { permissions: ["all"] },
            },
            {
                path: "payment-methods/add",
                name: RouteNames.AddPayment,
                component: PaymentForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "payment-methods/edit/:id",
                name: RouteNames.EditPayment,
                component: PaymentForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "payment-methods/view/:id",
                name: RouteNames.ViewPayment,
                component: PaymentForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "packages",
                name: RouteNames.PackagesList,
                component: PackagesList,
                meta: { permissions: ["all"] },
            },
            {
                path: "purchases",
                name: RouteNames.PurchasesList,
                component: PurchasesList,
                meta: { permissions: ["all"] },
            },
            {
                path: "deposits",
                name: RouteNames.DepositsList,
                component: DepositsList,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses",
                name: RouteNames.CoursesList,
                component: CoursesList,
            },
            {
                path: "courses/add",
                name: RouteNames.AddCourse,
                component: CoursesForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses/edit/:id",
                name: RouteNames.EditCourse,
                component: CoursesForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses/view/:id",
                name: RouteNames.ViewCourse,
                component: CoursesForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses/:courseId/add",
                name: RouteNames.AddCourseLevel,
                component: CourseLevelsForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses/:courseId/edit/:id",
                name: RouteNames.EditCourseLevel,
                component: CourseLevelsForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "courses/:courseId/view/:id",
                name: RouteNames.ViewCourseLevel,
                component: CourseLevelsForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "categories",
                name: RouteNames.CourseCategoriesList,
                component: CourseCategoriesList,
            },
            {
                path: "course-level/:courseLevelId/lesson-days",
                name: RouteNames.LessonDaysList,
                component: LessonDaysList,
            },
            {
                path: "course-level/:courseLevelId/lesson-days/add",
                name: RouteNames.AddLessonDay,
                component: LessonDayForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "course-level/:courseLevelId/lesson-days/edit/:id",
                name: RouteNames.EditLessonDay,
                component: LessonDayForm,
                meta: { permissions: ["all"] },
            },
            {
                path: "course-level/:courseLevelId/lesson-days/view/:id",
                name: RouteNames.ViewLessonDay,
                component: LessonDayForm,
                meta: { permissions: ["all"] },
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
            (p: any) => p.permission_type && p.permission_type.name === "all",
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
                    userPerm.permission_type_name === requiredPerm,
            ),
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
            next({ name: RouteNames.AdminsList });
        } else {
            next();
        }
    }
});

export default router;
