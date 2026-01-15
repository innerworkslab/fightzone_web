import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type PackagesFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface PackagesData {
    id?: number;
    name?: string;
    price: number;
    days: number;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface PackagesPayload {
    name: string;
    price: number;
    days: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.PACKAGE}`;

const usePackagess = (filter: PackagesFilter = {}) => {
    return useFetch<APIResult<PackagesData[]>>(baseURL, filter);
};

const usePackagesDetail = (id: number | string) => {
    return useFetch<APIResult<PackagesData>>(`/${baseURL}/${id}`);
};

const usePackagesActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createPackages = (data: PackagesPayload) => {
        return mutate(METHODS.POST, baseURL, {
            name: data.name,
            price: data.price,
            days: data.days,
        });
    };

    const updatePackages = (id: number, data: Partial<PackagesPayload>) => {
        const payload: Partial<PackagesPayload> = {};
        if (data.name !== undefined) payload.name = data.name;
        if (data.price !== undefined) payload.price = data.price;
        if (data.days !== undefined) payload.days = data.days;

        return mutate(METHODS.POST, `/${baseURL}/${id}`, payload);
    };

    const deletePackages = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createPackages,
        updatePackages,
        deletePackages,
        toggleStatus,
    };
};

export const PackagesServices = {
    usePackagess,
    usePackagesDetail,
    usePackagesActions,
};
