import { formatPriceOrNumber } from "../../../js/ts/utils/helper";
import { ActionDef, ColumnDef } from "../data-table/type";
import { Edit2, Folder, Minus, Plus } from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";

export const AdminColumns: ColumnDef<any>[] = [
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
        label: "Username",
        key: "username",
        render: (row) => row.username,
    },
    {
        label: "Status",
        key: "is_active",
        render: (row) => {
            const isActive = row.is_active;
            const colorClass = isActive
                ? "text-primary"
                : "text-muted-foreground";
            const bgClass = isActive ? "bg-primary/20" : "bg-muted";
            const dotClass = isActive
                ? "bg-primary shadow-[0_0_8px_var(--primary)]"
                : "bg-muted-foreground";
            const label = isActive ? "Active" : "Inactive";

            return `
            <div class="flex items-center gap-2 w-fit px-2.5 py-1 rounded-full border border-border ${bgClass}">
                <span class="relative flex h-2 w-2">
                    ${
                        isActive
                            ? `<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>`
                            : ""
                    }
                    <span class="relative inline-flex rounded-full h-2 w-2 ${dotClass}"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-widest ${colorClass}">
                    ${label}
                </span>
            </div>
        `;
        },
    },
];

export const AdminActions: ActionDef<any>[] = [
    {
        icon: Edit2,
        tooltip: "Addition",
        onClick: (row, extraArgs) =>
            extraArgs.router.push({
                name: RouteNames.EditAdmin,
                params: { id: row.id },
            }),
    },
    {
        icon: Folder,
        tooltip: "Settlement",
        onClick: (row, extraArgs) =>
            extraArgs.router.push({
                name: RouteNames.ViewAdmin,
                params: { id: row.id },
            }),
    },
];
