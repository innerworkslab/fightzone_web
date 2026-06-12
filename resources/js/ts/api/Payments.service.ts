import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type PaymentMethodsFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
    is_active?: "1" | "0";
};

export interface PaymentMethodsData {
    id?: number;
    name?: string;
    holder?: string;
    account_number?: string;
    logo?: string;
    logo_url?: string;
    qr?: string;
    qr_url?: string;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface PaymentMethodsPayload {
    name: string;
    holder: string;
    account_number: string;
    logo?: (File | string)[];
    is_active?: boolean;
    qr?: (File | string)[];
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.PAYMENT}`;

const usePaymentMethods = (filter: PaymentMethodsFilter = {}) => {
    return useFetch<APIResult<PaymentMethodsData[]>>(baseURL, filter);
};

const usePaymentMethodDetail = (id: number | string) => {
    return useFetch<APIResult<PaymentMethodsData>>(`/${baseURL}/${id}`);
};

const usePaymentMethodActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createPaymentMethod = (data: PaymentMethodsPayload) => {
        const formData = new FormData();
        formData.append("name", data.name);
        formData.append("holder", data.holder);
        formData.append("account_number", data.account_number);
        if (data.logo && data.logo.length > 0) {
            const logoFile = data.logo[0];
            if (logoFile instanceof File) {
                formData.append("logo", logoFile);
            }
        }
        if (data.qr && data.qr.length > 0) {
            const qrFile = data.qr[0];
            if (qrFile instanceof File) {
                formData.append("qr", qrFile);
            }
        }

        return mutate(METHODS.POST, baseURL, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    };

    const updatePaymentMethod = (
        id: number,
        data: Partial<PaymentMethodsPayload>
    ) => {
        const formData = new FormData();
        if (data.name) formData.append("name", data.name);
        if (data.holder) formData.append("holder", data.holder);
        if (data.account_number)
            formData.append("account_number", data.account_number);
        if (data.logo && data.logo.length > 0) {
            const logoFile = data.logo[0];
            if (logoFile instanceof File) {
                formData.append("logo", logoFile);
            }
        }

        if (data.qr && data.qr.length > 0) {
            const qrFile = data.qr[0];
            if (qrFile instanceof File) {
                formData.append("qr", qrFile);
            }
        }

        return mutate(METHODS.POST, `/${baseURL}/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    };

    const deletePaymentMethod = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createPaymentMethod,
        updatePaymentMethod,
        deletePaymentMethod,
        toggleStatus,
    };
};

export const PaymentMethodsServices = {
    usePaymentMethods,
    usePaymentMethodDetail,
    usePaymentMethodActions,
};
