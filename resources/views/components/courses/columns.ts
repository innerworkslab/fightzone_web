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
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { CourseLevelsServices } from "@/api/CourseLevels.service";
import { formatPriceOrNumber } from "@/utils/helper";

const { toggleStatus } = CoursesServices.useCourseActions();
const { toggleStatus: toggleCourseLevelStatus } =
    CourseLevelsServices.useCourseLevelActions();

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

export const CoursesSubColumns: ColumnDef<any>[] = [
    { label: "Level", key: "level" },
    {
        label: "Price",
        key: "price",
        render: (row) => `${formatPriceOrNumber(row.price)}`,
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
                    const response = await toggleCourseLevelStatus(row.id);
                    if (response?.success) {
                        toast.success(
                            response.message ??
                                (row.is_active
                                    ? SUCCESS_MESSAGE.INACTIVATED
                                    : SUCCESS_MESSAGE.ACTIVATED),
                        );
                        extraArgs.reloadSubTable(row.__parentRow);
                    }
                },
                approveBtnText: "Verify",
            });
        },
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
        icon: CopyPlus,
        tooltip: "Add Levels",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.AddCourseLevel,
                params: { courseId: row.id },
            });
        },
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
