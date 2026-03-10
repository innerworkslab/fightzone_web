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
        render: (row) => row.user.name ?? "-" + " (" + row.user.phone_number + ")",
    },
    {
        label: "Package",
        key: "package",
        render: (row) => `${row.package.name}`,
    },
    {
        label: "Price",
        key: "price",
        render: (row) => `${row.package.price.toLocaleString()}`,
    },
    {
        label: "Days",
        key: "days",
        render: (row) => `${row.remaining_days}/${row.total_days} days`,
    },
    {
        label: "Last Walkin At",
        key: "created_at",
        render: (row) => new Date(row.updated_at).toLocaleDateString(),
    },
];
