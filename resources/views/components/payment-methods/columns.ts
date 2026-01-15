import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, CreditCard, User } from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";
import { PaymentMethodsServices } from "@/api/Payments.service";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { toast } from "vue3-toastify";

const { toggleStatus } = PaymentMethodsServices.usePaymentMethodActions();

export const PaymentColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Logo",
        key: "logo",
        className: "text-center w-[80px]",
        render: (row) => {
            const logoUrl = row.logo_url;
            if (logoUrl) {
                return `<img src="${logoUrl}" alt="Logo" class="max-w-18 h-10 rounded-sm object-cover mx-auto" />`;
            }
            return `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mx-auto">
                <CreditCard class="h-5 w-5 text-gray-500" />
            </div>`;
        },
    },
    {
        label: "Name",
        key: "name",
        render: (row) => row.name || "N/A",
    },
    {
        label: "Holder",
        key: "holder",
        render: (row) => {
            return `<div class="flex items-center gap-2">
                <User class="h-4 w-4 text-blue-600" />
                ${row.holder || "N/A"}
            </div>`;
        },
    },
    {
        label: "Account Number",
        key: "account_number",
        render: (row) => {
            const accountNumber = row.account_number;
            if (!accountNumber) return "N/A";
            const masked =
                accountNumber.length > 4
                    ? `****${accountNumber.slice(-4)}`
                    : accountNumber;

            return `<span class="font-mono">${masked}</span>`;
        },
    },
    {
        label: "Status",
        key: "is_active",
        render: (row) => {
            let theme = {
                color: "text-emerald-500",
                bg: "bg-emerald-500/10",
                border: "border-emerald-500/20",
                label: "Active",
            };

            if (!row.is_active) {
                theme = {
                    color: "text-red-500",
                    bg: "bg-red-500/10",
                    border: "border-red-500/20",
                    label: "Inactive",
                };
            }

            return `
            <div class="inline-flex items-center px-2 py-0.5 rounded border ${theme.bg} ${theme.border}">
                <span class="text-[9px] font-black uppercase tracking-[0.1em] ${theme.color}">
                    ${theme.label}
                </span>
            </div>
            `;
        },
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: row.is_active
                    ? "Are you sure you want to deactivate this payment method?"
                    : "Are you sure you want to activate this payment method?",
                onApprove: async () => {
                    const response = await toggleStatus(row.id);
                    if (response?.success) {
                        toast.success(
                            response.message ??
                                (row.is_active
                                    ? SUCCESS_MESSAGE.INACTIVATED
                                    : SUCCESS_MESSAGE.ACTIVATED)
                        );
                        extraArgs.refresh();
                    }
                },
                approveBtnText: row.is_active ? "Deactivate" : "Activate",
            });
        },
    },
    {
        label: "Created Date",
        key: "created_at",
        render: (row) => {
            const date = row.created_at;
            if (!date) return "N/A";

            const formattedDate = new Date(date).toLocaleDateString("en-US", {
                year: "numeric",
                month: "short",
                day: "numeric",
            });

            return formattedDate;
        },
    },
];

export const PaymentActions: ActionDef<any>[] = [
    {
        icon: Edit2,
        tooltip: "Edit Payment",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditPayment,
                params: { id: row.id },
            });
        },
    },
    {
        icon: Folder,
        tooltip: "View Payment",
        onClick: (row, extraArgs) =>
            extraArgs.router.push({
                name: RouteNames.ViewPayment,
                params: { id: row.id },
            }),
    },
];
