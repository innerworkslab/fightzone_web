import { ActionDef, ColumnDef } from "../data-table/type";
import { Edit2, Folder, Minus, Plus } from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";
import { UserServices } from "@/api/User.service";
import { SUCCESS_MESSAGE } from "@/constant/constant.global";
import { toast } from "vue3-toastify";

const { updateUser, toggleStatus } = UserServices.useUserActions();

export const UserColumns: ColumnDef<any>[] = [
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
        label: "Phone Number",
        key: "phone_number",
        render: (row) => row.phone_number,
    },
    {
        label: "Verification",
        key: "is_verified",
        render: (row) => {
            const isVerified = row.is_verified;
            const colorClass = isVerified
                ? "text-green-600"
                : "text-orange-500";
            const bgClass = isVerified ? "bg-green-100" : "bg-orange-100";
            const label = isVerified ? "Verified" : "Unverified";

            return `
            <div class="flex items-center gap-2 w-fit px-2.5 py-1 rounded-full border ${bgClass}">
                <span class="text-[10px] font-black uppercase tracking-widest ${colorClass}">
                    ${label}
                </span>
            </div>
        `;
        },
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: "Are you sure you want to verify?",
                onApprove: async () => {
                    const response = await updateUser(row.id, {
                        is_verified: 1,
                    });
                    if (response?.success) {
                        toast.success(
                            response.message ?? SUCCESS_MESSAGE.VERIFIED
                        );
                    }
                    extraArgs.refresh();
                },
                approveBtnText: "Verify",
            });
        },
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
                                    : SUCCESS_MESSAGE.ACTIVATED)
                        );
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Verify",
            });
        },
    },
];

export const UserActions: ActionDef<any>[] = [
    {
        icon: Edit2,
        tooltip: "Addition",
        onClick: (row, extraArgs) => {
            extraArgs.router.push({
                name: RouteNames.EditUser,
                params: { id: row.id },
            });
        },
    },
    {
        icon: Folder,
        tooltip: "Settlement",
        onClick: (row, extraArgs) =>
            extraArgs.router.push({
                name: RouteNames.ViewUser,
                params: { id: row.id },
            }),
    },
];
