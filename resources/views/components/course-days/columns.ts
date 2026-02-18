import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Check, X } from "lucide-vue-next";
import { CoursesServices } from "@/api/Courses.service";
import { toast } from "vue3-toastify";

const { deleteCourse, toggleStatus } = CoursesServices.useCourseActions();

export const CourseDaysColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Course Name",
        key: "name",
        render: (row) => row.name,
    },
    {
        label: "Level",
        key: "level",
        className: "capitalize",
        render: (row) => row.level,
    },
    {
        label: "Price",
        key: "price",
        className: "text-right",
        render: (row) => `${row.price.toLocaleString()} pts`,
    },
    {
        label: "Days",
        key: "course_days",
        className: "text-center",
        render: (row) => row.course_days?.length ?? 0,
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

export const CourseDaysActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "course",
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
                formIndex: "course",
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
                toast.success(response.message ?? "Course activated");
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
                toast.success(response.message ?? "Course deactivated");
                extraArgs.refresh();
            }
        },
        show: (row) => row.is_active,
    },
];
