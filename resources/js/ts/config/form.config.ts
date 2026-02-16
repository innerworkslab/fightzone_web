import PackagesForm from "../../../views/components/packages/PackagesForm.vue";
import PurchasesForm from "../../../views/components/purchases/PurchasesForm.vue";
import DepositsForm from "../../../views/components/deposits/DepositsForm.vue";
import UsersPasswordChangeForm from "../../../views/components/users/UsersPasswordChangeForm.vue";
import AdminsChangePasswordForm from "../../../views/components/admins/AdminsChangePasswordForm.vue";
import CourseCategoriesForm from "../../../views/components/course-categories/CourseCategoriesForm.vue";

export const getFormConfig = (initialValues: any, isReadMode: boolean) => ({
    // #A
    adminsChangePassword: {
        form: AdminsChangePasswordForm,
        header: "Change Admin Password",
        message: "",
    },
    // #C
    courseCategory: {
        form: CourseCategoriesForm,
        header: `${
            initialValues ? (isReadMode ? "(Read Only) " : "Update") : "Create"
        } Course Category`,
        message: "",
    },
    // #D
    deposit: {
        form: DepositsForm,
        header: `${isReadMode ? "Deposit Details" : "Reject Deposit"}`,
        message: isReadMode
            ? "View deposit details and receipt"
            : "Provide a reason for rejecting this deposit",
    },
    // #P
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
    // #U
    usersChangePassword: {
        form: UsersPasswordChangeForm,
        header: "Change User Password",
        message: "",
    },
});
