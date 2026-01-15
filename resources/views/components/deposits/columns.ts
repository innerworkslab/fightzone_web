import { ActionDef, ColumnDef } from "../common/data-table/type";
import {
    Folder,
    Check,
    X,
    CreditCard,
    ShieldCheck,
    Clock,
    AlertTriangle,
} from "lucide-vue-next";
import { DepositsServices } from "@/api/Deposits.service";
import { toast } from "vue3-toastify";
import { h } from "vue";
import Gallery from "../common/gallary/Gallary.vue";

const { confirmDeposit } = DepositsServices.useDepositActions();

export const DepositColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[60px] font-mono text-muted-foreground",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "User Information",
        key: "user",
        render: (row) => {
            const user = row.user;
            if (!user)
                return '<span class="text-muted-foreground italic">Unknown System User</span>';
            return `
                <div class="flex flex-col">
                    <span class="font-black uppercase tracking-tighter text-[13px] text-foreground">${user.name}</span>
                    <span class="text-[10px] text-primary font-bold tracking-widest">${user.phone_number}</span>
                </div>
            `;
        },
    },
    {
        label: "Transaction Details",
        key: "paymentMethod",
        render: (row) => {
            const method = row.paymentMethod;
            const txId = row.transaction_id || "NO_ID";
            if (!method) return "N/A";

            return `
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-black uppercase bg-secondary px-1.5 rounded text-foreground">${method.name}</span>
                    <span class="font-mono text-[11px] text-muted-foreground opacity-70">#${txId}</span>
                </div>
                <div class="text-[10px] font-bold text-muted-foreground uppercase tracking-tight">${method.holder}</div>
            </div>`;
        },
    },
    {
        label: "Amount",
        key: "amount",
        className: "font-black text-primary",
        render: (row) => `$${row.amount.toLocaleString()}`,
    },
    {
        label: "Proof",
        key: "screenshot_url",
        className: "text-center w-[100px]",
        render: (row) =>
            h(Gallery, {
                size: "sm",
                images: [
                    {
                        id: row.id,
                        src: row.screenshot_url,
                        title: "Transaction Proof",
                        subtitle: `Amount: $${row.amount}`,
                    },
                ],
            }),
    },
    {
        label: "Status",
        key: "status",
        render: (row) => {
            const status = row.status;
            let theme = {
                color: "text-blue-500",
                bg: "bg-blue-500/10",
                border: "border-blue-500/20",
                label: "Pending",
            };

            if (status === "confirmed") {
                theme = {
                    color: "text-emerald-500",
                    bg: "bg-emerald-500/10",
                    border: "border-emerald-500/20",
                    label: "Confirmed",
                };
            }
            if (status === "rejected") {
                theme = {
                    color: "text-red-500",
                    bg: "bg-red-500/10",
                    border: "border-red-500/20",
                    label: "Rejected",
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
    },
];

export const DepositActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "deposit",
                initialValues: row,
                isReadMode: true,
            });
        },
    },
    {
        icon: Check,
        tooltip: "Approve",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: `Authorize $${row.amount} deposit for ${row.user?.name}?`,
                onApprove: async () => {
                    const response = await confirmDeposit(row.id);
                    if (response?.success) {
                        toast.success("Transaction Confirmed");
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Authorize",
            });
        },
        show: (row) => row.status === "pending",
    },
    {
        icon: X,
        tooltip: "Reject",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "deposit",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
        show: (row) => row.status === "pending",
    },
];
