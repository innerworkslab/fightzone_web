import { ActionDef, ColumnDef } from "../common/data-table/type";
import {
    Edit2,
    Folder,
    Lock,
    Minus,
    Plus,
    ToggleLeft,
    ToggleRight,
} from "lucide-vue-next";
import { RouteNames } from "../../../js/ts/config/route.config";
import { UsersData, UsersServices } from "@/api/Users.service";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { toast } from "vue3-toastify";

const { updateUser, toggleStatus } = UsersServices.useUserActions();

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
            let theme = {
                color: "text-sky-500",
                bg: "bg-sky-500/10",
                border: "border-sky-500/20",
                label: "Verified",
            };

            if (!row.is_verified) {
                theme = {
                    color: "text-orange-500",
                    bg: "bg-orange-500/10",
                    border: "border-orange-500/20",
                    label: "Unverified",
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
            if (row.is_verified) return;
            extraArgs.modalStore.openConfirmModal({
                message: "Are you sure you want to verify?",
                onApprove: async () => {
                    const response = await updateUser(row.id, {
                        is_verified: 1,
                    });
                    if (response?.success) {
                        toast.success(
                            response.message ?? SUCCESS_MESSAGE.VERIFIED,
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
    {
        icon: Lock,
        tooltip: "Change Password",
        onClick: (row, extraArgs) =>
            extraArgs.modalStore.openModal({
                formIndex: "usersChangePassword",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            }),
    },
    {
        icon: (row: UsersData) => (row.is_active ? ToggleRight : ToggleLeft),
        tooltip: (row) =>
            row.is_active ? "Deactivate Level" : "Activate Level",
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
];
