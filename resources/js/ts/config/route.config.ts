import Login from "../../../views/components/login/Login.vue";
import {
    UserSquare,
    CreditCard,
    Package,
    ShoppingCart,
    Banknote,
    BookAudio,
} from "lucide-vue-next";

export const RouteNames = {
    Login: "login",
    AdminsList: "admin-list",
    AddAdmin: "add-admin",
    ViewAdmin: "view-admin",
    EditAdmin: "edit-admin",
    UsersList: "user-list",
    AddUser: "add-user",
    ViewUser: "view-user",
    EditUser: "edit-user",
    PaymentMethodsList: "payment-methods-list",
    AddPayment: "add-payment",
    ViewPayment: "view-payment",
    EditPayment: "edit-payment",
    PackagesList: "packages-list",
    PurchasesList: "purchases-list",
    DepositsList: "deposits-list",
    CoursesList: "courses-list",
    AddCourse: "add-course",
    ViewCourse: "view-course",
    EditCourse: "edit-course",
    CourseCategoriesList: "course-categories-list",
    CourseDaysList: "course-days-list",
    AddCourseDay: "add-course-day",
    ViewCourseDay: "view-course-day",
    EditCourseDay: "edit-course-day",
    Unauthorized: "unauthorized",
};

export const routes = [
    {
        path: "/",
        name: RouteNames.Login,
        component: Login,
    },
    {
        label: "Admin",
        name: "admin_group",
        icon: UserSquare,
        route: "/auth/admins",
        children: [
            {
                label: "Admin List",
                name: RouteNames.AdminsList,
                header: "Admin Management",
                route: "/auth/admins",
            },
            {
                label: "",
                name: RouteNames.AddAdmin,
                header: "Add Admin",
                route: "/auth/admins/add",
            },
            {
                label: "",
                name: RouteNames.EditAdmin,
                header: "Edit Admin",
                route: "",
            },
            {
                label: "",
                name: RouteNames.ViewAdmin,
                header: "View Admin",
                route: "",
            },
        ],
    },
    {
        label: "User",
        name: "user_group",
        icon: UserSquare,
        route: "/auth/users",
        children: [
            {
                label: "User List",
                name: RouteNames.UsersList,
                header: "User Management",
                route: "/auth/users",
            },
            {
                label: "",
                name: RouteNames.AddUser,
                header: "Add User",
                route: "/auth/users/add",
            },
            {
                label: "",
                name: RouteNames.EditUser,
                header: "Edit User",
                route: "",
            },
            {
                label: "",
                name: RouteNames.ViewUser,
                header: "View User",
                route: "",
            },
        ],
    },
    {
        label: "Payment Method",
        name: "payment_method_group",
        icon: CreditCard,
        route: "/auth/payment-methods",
        children: [
            {
                label: "Payment Methods",
                name: RouteNames.PaymentMethodsList,
                header: "Payment Method Management",
                route: "/auth/payment-methods",
            },
            {
                label: "",
                name: RouteNames.AddPayment,
                header: "Add Payment Method",
                route: "/auth/payment-methods/add",
            },
            {
                label: "",
                name: RouteNames.EditPayment,
                header: "Edit Payment Method",
                route: "",
            },
            {
                label: "",
                name: RouteNames.ViewPayment,
                header: "View Payment Method",
                route: "",
            },
        ],
    },
    {
        label: "Package",
        name: "package_group",
        icon: Package,
        route: "/auth/packages",
        children: [
            {
                label: "Package List",
                name: RouteNames.PackagesList,
                header: "Package Management",
                route: "/auth/packages",
            },
        ],
    },
    {
        label: "Purchase",
        name: "purchase_group",
        icon: ShoppingCart,
        route: "/auth/purchases",
        children: [
            {
                label: "Purchase List",
                name: RouteNames.PurchasesList,
                header: "Purchase Management",
                route: "/auth/purchases",
            },
        ],
    },
    {
        label: "Deposit Transaction",
        name: "deposit_group",
        icon: Banknote,
        route: "/auth/deposits",
        children: [
            {
                label: "Deposit Transaction List",
                name: RouteNames.DepositsList,
                header: "Deposit Transaction Management",
                route: "/auth/deposits",
            },
        ],
    },
    {
        label: "Course",
        name: "course_group",
        icon: BookAudio,
        route: "/auth/courses",
        children: [
            {
                label: "Course List",
                name: RouteNames.CoursesList,
                header: "Course Management",
                route: "/auth/courses",
            },
            {
                label: "",
                name: RouteNames.AddCourse,
                header: "Add Course",
                route: "/auth/courses/add",
            },
            {
                label: "",
                name: RouteNames.EditCourse,
                header: "Edit Course",
                route: "",
            },
            {
                label: "",
                name: RouteNames.ViewCourse,
                header: "View Course",
                route: "",
            },
        ],
    },
    {
        label: "Category",
        name: "category_group",
        icon: BookAudio,
        route: "/auth/categories",
        children: [
            {
                label: "Category List",
                name: RouteNames.CourseCategoriesList,
                header: "Categories Management",
                route: "/auth/categories",
            },
        ],
    },
    {
        label: "Course Day",
        name: "course_day_group",
        icon: BookAudio,
        route: "/auth/course-days",
        children: [
            {
                label: "Course List",
                name: RouteNames.CourseDaysList,
                header: "Course Days Management",
                route: "/auth/course-days",
            },
            {
                label: "",
                name: RouteNames.AddCourseDay,
                header: "Add Course Day",
                route: "/auth/course-days/add",
            },
            {
                label: "",
                name: RouteNames.EditCourseDay,
                header: "Edit Course Day",
                route: "",
            },
            {
                label: "",
                name: RouteNames.ViewCourseDay,
                header: "View Course Day",
                route: "",
            },
        ],
    },
];
