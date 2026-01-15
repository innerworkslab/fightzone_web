import { defineStore } from "pinia";

export interface ConfirmState {
    isOpen: boolean;
    message: string;
    onApprove: () => void;
    onReject?: () => void;
    approveBtnText: string;
    rejectBtnText?: string;
}

export interface State {
    isOpen: boolean;
    formIndex: string;
    initialValues?: any | null;
    isReadMode?: boolean;
    refreshCallback?: () => void;
    confirm: ConfirmState;
}

export interface Actions {
    openModal(state: {
        formIndex: string;
        initialValues?: any | null;
        isReadMode?: boolean;
        refreshCallback?: () => void;
    }): void;
    setInitialValues(state: {
        initialValues?: any | null;
        isReadMode?: boolean;
    }): void;
    clearInitialValues(): void;
    closeModal(): void;
    openConfirmModal(state: {
        message: string;
        onApprove: () => void;
        onReject?: () => void;
        approveBtnText: string;
        rejectBtnText?: string;
    }): void;
    closeConfirmModal(): void;
    triggerRefresh(): void;
}

export const useModalStore = defineStore<"modal", State, {}, Actions>("modal", {
    state: (): State => ({
        isOpen: false,
        formIndex: "",
        initialValues: null,
        isReadMode: false,
        refreshCallback: undefined,
        confirm: {
            isOpen: false,
            message: "",
            onApprove: () => {},
            onReject: undefined,
            approveBtnText: "",
            rejectBtnText: undefined,
        },
    }),
    actions: {
        openModal({
            formIndex,
            initialValues = null,
            isReadMode = false,
            refreshCallback,
        }: {
            formIndex: string;
            initialValues?: any | null;
            isReadMode?: boolean;
            refreshCallback?: () => void;
        }) {
            this.formIndex = formIndex;
            this.initialValues = initialValues;
            this.isReadMode = isReadMode;
            this.refreshCallback = refreshCallback;
            this.isOpen = true;
        },
        setInitialValues({
            initialValues = null,
            isReadMode = false,
        }: {
            initialValues?: any | null;
            isReadMode?: boolean;
        }) {
            this.initialValues = initialValues;
            this.isReadMode = isReadMode;
        },
        closeModal() {
            this.formIndex = "";
            this.isOpen = false;
            this.isReadMode = false;
            this.refreshCallback = undefined;
        },

        triggerRefresh() {
            if (this.refreshCallback) {
                this.refreshCallback();
            }
        },
        clearInitialValues() {
            this.initialValues = null;
            this.isReadMode = false;
        },
        openConfirmModal({
            message,
            onApprove,
            onReject,
            approveBtnText,
            rejectBtnText,
        }: {
            message: string;
            onApprove: () => void;
            onReject?: () => void;
            approveBtnText: string;
            rejectBtnText?: string;
        }) {
            this.confirm.message = message;
            this.confirm.onApprove = onApprove;
            this.confirm.onReject = onReject;
            this.confirm.approveBtnText = approveBtnText;
            this.confirm.rejectBtnText = rejectBtnText;
            this.confirm.isOpen = true;
        },
        closeConfirmModal() {
            this.confirm.isOpen = false;
            this.confirm.message = "";
            this.confirm.onApprove = () => {};
            this.confirm.onReject = undefined;
            this.confirm.approveBtnText = "";
            this.confirm.rejectBtnText = undefined;
        },
    },
});
