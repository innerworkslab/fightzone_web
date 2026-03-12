import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Image } from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";

export const FeaturedImageColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Image",
        key: "image",
        className: "text-center w-[80px]",
        render: (row) => {
            const imageUrl = row.image_url;
            if (imageUrl) {
                return `<img src="${imageUrl}" alt="Logo" class="max-w-18 h-10 rounded-sm object-cover mx-auto" />`;
            }
            return `<div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mx-auto">
                <Image class="h-5 w-5 text-gray-500" />
            </div>`;
        },
    },
];

export const FeaturedImageActions: ActionDef<any>[] = [
    {
        icon: Edit2,
        tooltip: "Addition",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditFeaturedImage,
                params: { id: row.id },
            });
        },
    },
];
