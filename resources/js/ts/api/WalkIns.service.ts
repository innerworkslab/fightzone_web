import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type WalkInsFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface CourseCategoriesData {
    id: number;
    name: string;
    description: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface WalkInsPayload {
    qr_payload: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.WALK_IN}`;

const useWalkIns = (filter: WalkInsFilter = {}) => {
    return useFetch<APIResult<CourseCategoriesData[]>>(baseURL, filter);
};

const useWalkInsActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const confirmWalkIns = (data: WalkInsPayload) => {
        return mutate(METHODS.POST, `/${baseURL}/confirm`, data);
    };

    // const updateWalkIns = (id: number, data: Partial<WalkInsPayload>) => {
    //     return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    // };

    // const deleteWalkIns = (id: number) =>
    //     mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    // const toggleStatus = (id: number) =>
    //     mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        confirmWalkIns,
        // updateWalkIns,
        // deleteWalkIns,
        // toggleStatus,
    };
};

export const WalkInsServices = {
    useWalkIns,
    useWalkInsActions,
};
