import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Check, X } from "lucide-vue-next";
import { CourseCategoriesServices } from "@/api/CourseCategories.service";
import { toast } from "vue3-toastify";

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
        className: "w-[120px]",
        render: (row) => {
            const active = row.is_active;

            const theme = active
                ? {
                      color: "text-emerald-400",
                      bg: "bg-emerald-400/10",
                      border: "border-emerald-400/20",
                      label: "Active",
                  }
                : {
                      color: "text-rose-500",
                      bg: "bg-rose-500/10",
                      border: "border-rose-500/20",
                      label: "Inactive",
                  };

            return `
            <div class="flex items-center gap-1.5 w-fit px-3 py-1 rounded-md border ${theme.bg} ${theme.border}">
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
    {
        icon: Check,
        tooltip: "Activate",
        onClick: async (row, extraArgs) => {
            if (row.is_active) return;

            const response = await toggleStatus(row.id);
            if (response?.success) {
                toast.success(
                    response.message ?? "Course categories activated",
                );
                extraArgs.refresh();
            }
        },
        show: (row) => !row.is_active,
    },
    {
        icon: X,
        tooltip: "Deactivate",
        onClick: async (row, extraArgs) => {
            if (!row.is_active) return;

            const response = await toggleStatus(row.id);
            if (response?.success) {
                toast.success(
                    response.message ?? "Course categories deactivated",
                );
                extraArgs.refresh();
            }
        },
        show: (row) => row.is_active,
    },
];
