import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Check, X } from "lucide-vue-next";
import { CourseCategoriesServices } from "@/api/CourseCategories.service";
import { toast } from "vue3-toastify";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";

const { toggleStatus } = CourseCategoriesServices.useCourseCategoriesActions();

export const CourseCategoriesColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Name",
        key: "name",
        render: (row) => row.name,
    },
    {
        label: "Description",
        key: "description",
        render: (row) =>
            `${row.description?.substring(0, 50) ?? ""}${row.description && row.description.length > 50 ? "..." : ""}`,
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

export const CourseCategoriesActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "courseCategory",
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
                formIndex: "courseCategory",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
];
