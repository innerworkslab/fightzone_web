import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import { RouteNames } from "@/config/route.config";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { formatPriceOrNumber } from "@/utils/helper";
import { RestDayVideosServices } from "@/api/RestDayVideos.service";

const { toggleStatus } = RestDayVideosServices.useRestDayVideoActions();

export const RestDayVideosColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Video Name",
        key: "name",
        render: (row) => row.name,
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
                    ? "Are you sure you want to inactivate?"
                    : "Are you sure you want to activate?",
                onApprove: async () => {
                    const response = await toggleStatus(row.id);
                    if (response?.success) {
                        toast.success(
                            response.message ??
                                (row.is_active
                                    ? SUCCESS_MESSAGE.INACTIVATED
                                    : SUCCESS_MESSAGE.ACTIVATED),
                        );
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Verify",
            });
        },
    },
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const RestDayVideosActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.ViewRestDayVideo,
                params: { id: row.id },
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditRestDayVideo,
                params: { id: row.id },
            });
        },
    },
];
