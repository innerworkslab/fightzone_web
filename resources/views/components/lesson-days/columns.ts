import { RouteNames } from "@/config/route.config";
import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, Upload } from "lucide-vue-next";

export const LessonDaysColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Day",
        key: "day_number",
        className: "text-center",
        render: (row) => `Day ${row.day_number}`,
    },
    {
        label: "Type",
        key: "type",
        className: "capitalize",
        render: (row) => row.type,
    },
    {
        label: "Videos",
        key: "videos_count",
        className: "text-center",
        render: (row) => row.videos_count ?? 0,
    },
    {
        label: "Duration",
        key: "formatted_duration",
        className: "text-right",
        render: (row) => row.formatted_duration,
    },
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const LessonDaySubColumns: ColumnDef<any>[] = [
    { key: "name", label: "Name" },
    { key: "description", label: "Description" },
    { key: "duration", label: "Duration" },
];

export const LessonDaysActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.ViewLessonDay,
                params: { courseLevelId: row.course_level_id, id: row.id },
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditLessonDay,
                params: { courseLevelId: row.course_level_id, id: row.id },
            });
        },
    },
    {
        icon: Upload,
        tooltip: "Upload Video",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "lessonDayVideo",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
];

export const LessonDaySubActions: ActionDef<any>[] = [
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
