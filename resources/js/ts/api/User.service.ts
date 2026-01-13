import { useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/constant.global";
import type { Filters } from "@/type.global";

export type UserFilter = Filters & {
    search?: string;
    status?: "active" | "inactive" | "all";
};

export interface UserData {
    phone_number?: string;
    name?: string;
    password?: string;
    is_active?: number;
    is_verified?: number;
}

export interface UserPayload {
    phone_number: string;
    name: string;
    password: string;
    is_active: number;
    is_verified: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.USER}`;

const useUsers = (filter: UserFilter = {}) => {
    return useFetch<UserData[]>(baseURL, filter);
};

const useUserDetail = (id: number | string) => {
    return useFetch<UserPayload>(`/${baseURL}/${id}`);
};

const useUserActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createUser = (data: UserPayload) =>
        mutate(METHODS.POST, baseURL, data);

    const updateUser = (id: number, data: Partial<UserPayload>) =>
        mutate(METHODS.PATCH, `/${baseURL}/${id}`, data);

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

export const UserServices = {
    useUsers,
    useUserDetail,
    useUserActions,
};
