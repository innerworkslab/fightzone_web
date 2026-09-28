import { ActionDef, ColumnDef } from "../common/data-table/type";
import { Edit2, Folder, ToggleLeft, ToggleRight, Trash2 } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import { SUCCESS_MESSAGE } from "@/constant/global.constant";
import { ContactsServices, type ContactData } from "@/api/Contacts.service";

const { deleteContact, toggleStatus } = ContactsServices.useContactActions();

const typeLabel = (type: string) => (type === "phone_number" ? "Phone Number" : "URL");

export const ContactsColumns: ColumnDef<any>[] = [
    {
        label: "#",
        key: "index",
        className: "text-center w-[50px]",
        render: (row, rowIndex, extraArgs) =>
            (extraArgs?.startIndex ?? 0) + rowIndex + 1,
    },
    {
        label: "Type",
        key: "type",
        render: (row) => typeLabel(row.type),
    },
    { label: "Name", key: "name" },
    {
        label: "Contact",
        key: "contact",
        render: (row) => {
            const value = row.contact ?? "";
            return value.length > 48 ? `${value.slice(0, 48)}...` : value;
        },
    },
    {
        label: "Status",
        key: "is_active",
        render: (row) => {
            const active = row.is_active;
            return `
                <div class="inline-flex items-center px-2 py-0.5 rounded border ${active ? "bg-emerald-500/10 border-emerald-500/20" : "bg-red-500/10 border-red-500/20"}">
                    <span class="text-[9px] font-black uppercase tracking-[0.1em] ${active ? "text-emerald-500" : "text-red-500"}">
                        ${active ? "Active" : "Inactive"}
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
                            row.is_active
                                ? SUCCESS_MESSAGE.INACTIVATED
                                : SUCCESS_MESSAGE.ACTIVATED,
                        );
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Confirm",
            });
        },
    },
    {
        label: "Created",
        key: "created_at",
        render: (row) => new Date(row.created_at).toLocaleDateString(),
    },
];

export const ContactsActions: ActionDef<any>[] = [
    {
        icon: Folder,
        tooltip: "View Details",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "contact",
                initialValues: row,
                isReadMode: true,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        icon: Edit2,
        tooltip: "Edit",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openModal({
                formIndex: "contact",
                initialValues: row,
                isReadMode: false,
                refreshCallback: extraArgs.refresh,
            });
        },
    },
    {
        icon: (row: ContactData) => (row.is_active ? ToggleRight : ToggleLeft),
        tooltip: (row) => (row.is_active ? "Deactivate Contact" : "Activate Contact"),
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
                approveBtnText: "Confirm",
            });
        },
    },
    {
        icon: Trash2,
        tooltip: "Delete",
        onClick: (row, extraArgs) => {
            extraArgs.modalStore.openConfirmModal({
                message: `Delete "${row.name}"?`,
                onApprove: async () => {
                    const response = await deleteContact(row.id);
                    if (response?.success) {
                        toast.success(response.message ?? "Contact deleted");
                        extraArgs.refresh();
                    }
                },
                approveBtnText: "Delete",
            });
        },
    },
];
