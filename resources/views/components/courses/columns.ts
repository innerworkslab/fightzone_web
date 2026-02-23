import { ActionDef, ColumnDef } from "../common/data-table/type";
import {
    Edit2,
    Folder,
    Check,
    X,
    CopyPlus,
    CalendarDays,
} from "lucide-vue-next";
import { CoursesServices } from "@/api/Courses.service";
import { toast } from "vue3-toastify";
import { RouteNames } from "@/config/route.config";

const { toggleStatus } = CoursesServices.useCourseActions();

export const CoursesColumns: ColumnDef<any>[] = [
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
        label: "Category",
        key: "name",
        render: (row) => row.category.name,
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

export const CoursesSubColumns: ColumnDef<any>[] = [
    { key: "level", label: "Level" },
    { key: "price", label: "Price" },
    {
        key: "is_active",
        label: "Status",
        render: (row) => (row.is_active ? "Active" : "Inactive"),
    },
];

export const CoursesActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.ViewCourse,
                params: { id: row.id },
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditCourse,
                params: { id: row.id },
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
        icon: CopyPlus,
        tooltip: "Add Levels",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.AddCourseLevel,
                params: { courseId: row.id },
            });
        },
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

export const CoursesSubActions: ActionDef<any>[] = [
    {
        icon: CalendarDays,
        tooltip: "View Days",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.LessonDaysList,
                params: { courseLevelId: row.id },
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit Level",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditCourseLevel,
                params: { courseId: row.course_id, id: row.id },
            });
        },
    },
];
