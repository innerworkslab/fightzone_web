import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type UsersFilter = Filters & {
    search?: string;
    name?: string;
    phone_number?: string;
    is_active?: string;
};

export interface UsersData {
    phone_number?: string;
    name?: string;
    password?: string;
    is_active?: number;
    is_verified?: number;
}

export interface UsersPayload {
    phone_number: string;
    name: string;
    password?: string;
    is_active: number;
    is_verified: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.USER}`;

const useUsers = (filter: UsersFilter = {}) => {
    return useFetch<APIResult<UsersData[]>>(baseURL, filter);
};

const useUserDetail = (id: number | string) => {
    return useFetch<APIResult<UsersPayload>>(`/${baseURL}/${id}`);
};

const useUserActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createUser = (data: UsersPayload) =>
        mutate(METHODS.POST, baseURL, data);

    const updateUser = (id: number, data: Partial<UsersPayload>) =>
        mutate(METHODS.POST, `/${baseURL}/${id}`, data);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createUser,
        updateUser,
        toggleStatus,
    };
};

export const UsersServices = {
    useUsers,
    useUserDetail,
    useUserActions,
};
