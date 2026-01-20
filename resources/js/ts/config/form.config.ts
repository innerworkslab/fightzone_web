import PackagesForm from "../../../views/components/packages/PackagesForm.vue";
import PurchasesForm from "../../../views/components/purchases/PurchasesForm.vue";
import DepositsForm from "../../../views/components/deposits/DepositsForm.vue";
import UsersPasswordChangeForm from "../../../views/components/users/UsersPasswordChangeForm.vue";
import AdminsChangePasswordForm from "../../../views/components/admins/AdminsChangePasswordForm.vue";

export const getFormConfig = (initialValues: any, isReadMode: boolean) => ({
    usersChangePassword: {
        form: UsersPasswordChangeForm,
        header: "Change User Password",
        message: "",
    },
    adminsChangePassword: {
        form: AdminsChangePasswordForm,
        header: "Change Admin Password",
        message: "",
    },
    package: {
        form: PackagesForm,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Package`,
        message: "",
    },
    purchase: {
        form: PurchasesForm,
        header: `${isReadMode ? "Purchase Details" : "Reject Purchase"}`,
        message: isReadMode
            ? "View purchase details"
            : "Provide a reason for rejecting this purchase",
    },
    deposit: {
        form: DepositsForm,
        header: `${isReadMode ? "Deposit Details" : "Reject Deposit"}`,
        message: isReadMode
            ? "View deposit details and receipt"
            : "Provide a reason for rejecting this deposit",
    },
});
