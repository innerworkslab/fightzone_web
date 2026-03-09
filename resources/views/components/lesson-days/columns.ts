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
    { label: "Name", key: "name" },
    {
        label: "Description",
        key: "description",
        render: (row) =>
            `${row.description?.substring(0, 50) ?? ""}${row.description && row.description.length > 50 ? "..." : ""}`,
    },
    {
        label: "Thumbnail",
        key: "thumbnail_url",
        className: "text-center w-[80px]",
        render: (row) => {
            const logoUrl = row.thumbnail_url;
            if (logoUrl) {
                return `<img src="${logoUrl}" alt="Thumbnail" class="max-w-28 h-15 rounded-sm object-cover mx-auto" />`;
            }
            return `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mx-auto">
                <CreditCard class="h-5 w-5 text-gray-500" />
            </div>`;
        },
    },
    { label: "Duration", key: "duration" },
];

export const LessonDaysActions: ActionDef<any>[] = [
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
                initialValues: { lesson_day_id: row.id },
                isReadMode: false,
                refreshCallback: () =>
                    extraArgs.reloadSubTable({
                        id: row.id,
                    }),
            });
        },
    },
];

export const LessonDaySubActions: ActionDef<any>[] = [
    {
        icon: Edit2,
        tooltip: "Edit Video",
        onClick: (row, extraArgs) => {
            console.log("row", row);

            extraArgs.modalStore.openModal({
                formIndex: "lessonDayVideo",
                initialValues: row,
                isReadMode: false,
                refreshCallback: () =>
                    extraArgs.reloadSubTable({
                        id: row.lesson_day_id,
                    }),
            });
        },
    },
];
