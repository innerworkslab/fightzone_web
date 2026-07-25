import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Check, X } from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";
import { PurchasesServices } from "@/api/Purchases.service";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { toast } from "vue3-toastify";
import { formatPriceOrNumber } from "@/utils/helper";

const { confirmPurchase, rejectPurchase } =
    PurchasesServices.usePurchaseActions();

const trimText = (value?: string | null, limit = 40) => {
    if (!value) return "-";

    const normalized = value.replace(/\s+/g, " ").trim();
    const trimmed =
        normalized.length > limit
            ? `${normalized.substring(0, limit).trimEnd()}...`
            : normalized;

    return trimmed
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
};

export const PurchaseColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "User",
        key: "user",
        render: (row) => {
            const user = row.user;
            return user
                ? `${user.name} (${user.phone_number})`
                : "Unknown User";
        },
    },
    {
        label: "Item",
        key: "purchasable",
        render: (row) => {
            const item = row.purchasable;
            if (!item) return "Unknown Item";

            const itemType =
                row.purchasable_type?.split("\\").pop()?.toLowerCase() ||
                "item";
            return `${item.name} (${itemType})`;
        },
    },
    {
        label: "Quantity",
        key: "quantity",
        className: "text-center",
        render: (row) => row.quantity,
    },
    {
        label: "Unit Price",
        key: "unit_price",
        render: (row) => formatPriceOrNumber(row.unit_price),
    },
    {
        label: "Total Points",
        key: "total_points",
        render: (row) => `${row.total_points.toLocaleString()} pts`,
    },
    {
        label: "Note",
        key: "note",
        render: (row) =>
            `<span class="block max-w-[220px] truncate" title="${trimText(row.note, 180)}">${trimText(row.note)}</span>`,
    },
    {
        label: "Certificate",
        key: "certificate_url",
        className: "cursor-pointer",
        render: (row) => {
            if (!row.certificate_url) return "-";

            return `
                <div class="inline-flex items-center">
                    <img src="${row.certificate_url}" alt="Certificate" class="h-10 w-14 rounded-sm object-cover border border-border" />
                </div>
            `;
        },
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "purchase",
                initialValues: row,
                isReadMode: true,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        label: "Status",
        key: "status",
        className: "w-[120px]",
        render: (row) => {
            const status = row.status;

            const statusMap: Record<
                string,
                { color: string; bg: string; border: string; label: string }
            > = {
                pending: {
                    color: "text-amber-400",
                    bg: "bg-amber-400/10",
                    border: "border-amber-400/20",
                    label: "Pending",
                },
                confirmed: {
                    color: "text-emerald-400",
                    bg: "bg-emerald-400/10",
                    border: "border-emerald-400/20",
                    label: "Confirmed",
                },
                rejected: {
                    color: "text-rose-500",
                    bg: "bg-rose-500/10",
                    border: "border-rose-500/20",
                    label: "Rejected",
                },
            };

            const theme = statusMap[status] || {
                color: "text-slate-400",
                bg: "bg-slate-400/10",
                border: "border-slate-400/20",
                label: status?.toUpperCase() || "UNKNOWN",
            };

            return `
        <div class="flex items-center gap-1.5 w-fit px-3 py-1 rounded-md border backdrop-blur-sm ${theme.bg} ${theme.border}">

            <span class="text-[9px] font-black uppercase tracking-[0.15em] ${theme.color}">
                ${theme.label}
            </span>
        </div>
        `;
        },
    },
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const PurchaseActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "purchase",
                initialValues: row,
                isReadMode: true,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        icon: Check,
        tooltip: "Confirm",
        onClick: (row, extraArgs) => {
            if (row.status !== "pending") return;

            extraArgs.modalStore.openConfirmModal({
                message: `Are you sure you want to confirm this purchase of ${
                    row.purchasable?.name || "item"
                } for ${
                    row.user?.name || "user"
                }? This will deduct ${row.total_points.toLocaleString()} points from their balance.`,
                onApprove: async () => {
                    const response = await confirmPurchase(row.id);
                    if (response?.success) {
                        toast.success(
                            response.message ??
                                "Purchase confirmed successfully"
                        );
                        extraArgs.modalStore.triggerRefresh();
                    }
                },
                approveBtnText: "Confirm Purchase",
            });
        },
        show: (row) => row.status === "pending",
    },
    {
        icon: X,
        tooltip: "Reject",
        onClick: (row, extraArgs) => {
            if (row.status !== "pending") return;

            extraArgs.modalStore.openModal({
                formIndex: "purchase",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
        show: (row) => row.status === "pending",
    },
];
