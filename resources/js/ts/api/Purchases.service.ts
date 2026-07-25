import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type PurchasesFilter = Filters & {
    search?: string;
    status?: "pending" | "confirmed" | "rejected";
    user_id?: string;
    purchasable_type?: string;
    purchasable_id?: string;
};

export interface PurchasesData {
    id?: number;
    user_id: number;
    purchasable_type: string;
    purchasable_id: number;
    quantity: number;
    unit_price: number;
    total_points: number;
    certificate_path?: string | null;
    certificate_url?: string | null;
    note?: string | null;
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
    admin?: {
        id: number;
        name: string;
    };
    purchasable?: {
        id: number;
        name: string;
        price?: number;
        points_required?: number;
    };
}

export interface PurchasesPayload {
    note?: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.PURCHASE}`;

const usePurchases = (filter: PurchasesFilter = {}) => {
    return useFetch<APIResult<PurchasesData[]>>(baseURL, filter);
};

const usePurchaseDetail = (id: number | string) => {
    return useFetch<APIResult<PurchasesData>>(`/${baseURL}/${id}`);
};

const usePurchaseActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const confirmPurchase = (id: number) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}/confirm`);
    };

    const rejectPurchase = (id: number, data: PurchasesPayload) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}/reject`, data);
    };

    return {
        loading,
        error,
        data,
        confirmPurchase,
        rejectPurchase,
    };
};

export const PurchasesServices = {
    usePurchases,
    usePurchaseDetail,
    usePurchaseActions,
};
