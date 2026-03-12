import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import { RouteNames } from "@/config/route.config";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { formatPriceOrNumber } from "@/utils/helper";
import { RestDayVideosServices } from "@/api/RestDayVideos.service";

const { toggleStatus } = RestDayVideosServices.useRestDayVideoActions();

export const RestDayVideosColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Video Name",
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
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const RestDayVideosActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "restDayVideo",
                initialValues: row,
                isReadMode: true,
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "restDayVideo",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
];
