import PackagesForm from "../../../views/components/packages/PackagesForm.vue";
import PurchasesForm from "../../../views/components/purchases/PurchasesForm.vue";
import DepositsForm from "../../../views/components/deposits/DepositsForm.vue";

export const getFormConfig = (initialValues: any, isReadMode: boolean) => ({
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
