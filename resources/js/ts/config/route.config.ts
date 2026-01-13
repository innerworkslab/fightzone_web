import Login from "../../../views/components/login/Login.vue";
import { UserSquare, CreditCard } from "lucide-vue-next";

export const RouteNames = {
    Login: "login",
    AdminList: "admin-list",
    AddAdmin: "add-admin",
    ViewAdmin: "view-admin",
    EditAdmin: "edit-admin",
    UserList: "user-list",
    AddUser: "add-user",
    ViewUser: "view-user",
    EditUser: "edit-user",
    PaymentList: "payment-list",
    AddPayment: "add-payment",
    ViewPayment: "view-payment",
    EditPayment: "edit-payment",
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
        route: "/auth/admin",
        children: [
            {
                label: "Admin List",
                name: RouteNames.ViewAdmin,
                header: "Admin Management",
                route: "/auth/admin",
            },
            {
                label: "",
                name: RouteNames.AddAdmin,
                header: "Add Admin",
                route: "/auth/admin/add",
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
        route: "/auth/user",
        children: [
            {
                label: "User List",
                name: RouteNames.UserList,
                header: "User Management",
                route: "/auth/user",
            },
            {
                label: "",
                name: RouteNames.AddUser,
                header: "Add User",
                route: "/auth/user/add",
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
        route: "/auth/payment",
        children: [
            {
                label: "Payment Methods",
                name: RouteNames.PaymentList,
                header: "Payment Method Management",
                route: "/auth/payment",
            },
            {
                label: "",
                name: RouteNames.AddPayment,
                header: "Add Payment Method",
                route: "/auth/payment/add",
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
];
