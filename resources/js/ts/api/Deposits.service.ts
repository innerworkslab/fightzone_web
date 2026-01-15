import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type DepositsFilter = Filters & {
    search?: string;
    status?: "pending" | "confirmed" | "rejected";
    user_id?: string;
    payment_method_id?: string;
    transaction_id?: string;
    amount?: string;
};

export interface DepositsData {
    id?: number;
    user_id: number;
    payment_method_id: number;
    transaction_id: string;
    amount: number;
    screenshot_path?: string;
    screenshot_url?: string;
    status: "pending" | "confirmed" | "rejected";
    admin_id?: number;
    admin_note?: string;
    confirmed_at?: string;
    created_at?: string;
    updated_at?: string;
    user?: {
        id: number;
        name: string;
        phone_number: string;
    };
    paymentMethod?: {
        id: number;
        name: string;
        holder: string;
    };
    admin?: {
        id: number;
        name: string;
    };
}

export interface DepositsPayload {
    note?: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.DEPOSIT}`;

const useDeposits = (filter: DepositsFilter = {}) => {
    return useFetch<APIResult<DepositsData[]>>(baseURL, filter);
};

const useDepositDetail = (id: number | string) => {
    return useFetch<APIResult<DepositsData>>(`/${baseURL}/${id}`);
};

const useDepositActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const confirmDeposit = (id: number) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}/confirm`);
    };

    const rejectDeposit = (id: number, data: DepositsPayload) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}/reject`, data);
    };

    return {
        loading,
        error,
        data,
        confirmDeposit,
        rejectDeposit,
    };
};

export const DepositsServices = {
    useDeposits,
    useDepositDetail,
    useDepositActions,
};
