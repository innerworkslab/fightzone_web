import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type AdminsFilter = Filters & {
    search?: string;
    username?: string;
    name?: string;
    is_active?: string;
};

export interface AdminsData {
    username?: string;
    name?: string;
    password?: string;
    is_active?: number;
}

export interface AdminsPayload {
    username: string;
    name: string;
    password?: string;
    confirm_password?: string;
    is_active: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.ADMIN}`;

const useAdmins = (filter: AdminsFilter = {}) => {
    return useFetch<APIResult<AdminsData[]>>(baseURL, filter);
};

const useAdminDetail = (id: number | string) => {
    return useFetch<APIResult<AdminsPayload>>(`/${baseURL}/${id}`);
};

const useAdminActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createAdmin = (data: AdminsPayload) =>
        mutate(METHODS.POST, baseURL, data);

    const updateAdmin = (id: number, data: Partial<AdminsPayload>) =>
        mutate(METHODS.POST, `/${baseURL}/${id}`, data);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createAdmin,
        updateAdmin,
        toggleStatus,
    };
};

export const AdminsServices = {
    useAdmins,
    useAdminDetail,
    useAdminActions,
};
