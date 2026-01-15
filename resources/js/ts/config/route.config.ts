import Login from "../../../views/components/login/Login.vue";
import PackagesList from "../../../views/components/packages/PackagesList.vue";
import PackagesForm from "../../../views/components/packages/PackagesForm.vue";
import {
    UserSquare,
    CreditCard,
    Package,
    ShoppingCart,
    Banknote,
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
];
