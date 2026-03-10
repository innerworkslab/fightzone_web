import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, CopyPlus } from "lucide-vue-next";
import { RouteNames } from "@/config/route.config";

export const WalkInsColumns: ColumnDef<any>[] = [
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
        render: (row) => row.user.name + " (" + row.user.phone_number + ")",
    },
    {
        label: "Package",
        key: "package",
        render: (row) => `$${row.package.name}`,
    },
    {
        label: "Price",
        key: "price",
        render: (row) => `$${row.package.price}`,
    },
    {
        label: "Days",
        key: "days",
        render: (row) => `${row.package.days} days`,
    },
    {
        label: "Description",
        key: "description",
        render: (row) =>
            `${row.description?.substring(0, 50) ?? ""}${row.description && row.description.length > 50 ? "..." : ""}`,
    },
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const WalkInsActions: ActionDef<any>[] = [
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
