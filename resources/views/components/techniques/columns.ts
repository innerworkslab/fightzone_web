import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Trash2 } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { TechniquesServices } from "@/api/Techniques.service";

const { deleteTechnique, toggleStatus } = TechniquesServices.useTechniqueActions();

export const TechniquesColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    { label: "Name", key: "name" },
    {
        label: "Category",
        key: "category",
        render: (row) => row.category?.name ?? "-",
    },
    {
        label: "Thumbnail",
        key: "thumbnail_url",
        className: "text-center w-[100px]",
        render: (row) =>
            row.thumbnail_url
                ? `<img src="${row.thumbnail_url}" alt="Technique thumbnail" class="max-w-28 h-15 rounded-sm object-cover mx-auto" />`
                : "-",
    },
    {
        label: "Provider",
        key: "provider",
        render: (row) => row.provider ?? "-",
    },
    {
        label: "Duration",
        key: "duration",
        render: (row) => row.formatted_duration ?? row.duration ?? "-",
    },
    {
        label: "Status",
        key: "is_active",
        render: (row) => {
            const active = row.is_active;
            return `
                <div class="inline-flex items-center px-2 py-0.5 rounded border ${active ? "bg-emerald-500/10 border-emerald-500/20" : "bg-red-500/10 border-red-500/20"}">
                    <span class="text-[9px] font-black uppercase tracking-[0.1em] ${active ? "text-emerald-500" : "text-red-500"}">
                        ${active ? "Active" : "Inactive"}
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
                            row.is_active
                                ? SUCCESS_MESSAGE.INACTIVATED
                                : SUCCESS_MESSAGE.ACTIVATED,
                        );
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Confirm",
            });
        },
    },
];

export const TechniquesActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "technique",
                initialValues: row,
                isReadMode: true,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "technique",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        icon: Trash2,
        tooltip: "Delete",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: `Delete "${row.name}"?`,
                onApprove: async () => {
                    const response = await deleteTechnique(row.id);
                    if (response?.success) {
                        toast.success(response.message ?? "Technique deleted");
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Delete",
            });
        },
    },
];
