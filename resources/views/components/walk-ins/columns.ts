import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Ban } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import { WalkInsServices } from "@/api/WalkIns.service";

const { revokeWalkIn } = WalkInsServices.useWalkInsActions();

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
        label: "Valid Until",
        key: "valid_until",
        render: (row) => row.valid_until ? new Date(row.valid_until).toLocaleDateString() : "-",
    },
    {
        label: "Last Walkin At",
        key: "created_at",
        render: (row) => new Date(row.updated_at).toLocaleDateString(),
    },
];

export const WalkInsActions: ActionDef<any>[] = [
    {
        icon: Ban,
        tooltip: "Revoke package purchase",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: `Are you sure you want to revoke ${row.user?.name ?? "this user's"} walk-in package ${row.package?.name ?? ""}? The user will lose access immediately, even if days remain.`,
                onApprove: async () => {
                    const response = await revokeWalkIn(row.id);

                    if (response?.success) {
                        toast.success(response.message ?? "Walk-in package revoked successfully");
                        extraArgs.refresh?.();
                    }
                },
                approveBtnText: "Revoke Package",
            });
        },
        show: (row) => !row.completed,
    },
];