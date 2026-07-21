import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type TechniqueCategoriesFilter = Filters & {
    search?: string;
    is_active?: string;
};

export interface TechniqueCategoryData {
    id: number;
    name: string;
    is_active: boolean;
    techniques_count?: number;
    created_at: string;
    updated_at: string;
}

export interface TechniqueCategoryPayload {
    name: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.TECHNIQUE_CATEGORY}`;

const useTechniqueCategories = (filter: TechniqueCategoriesFilter = {}) => {
    return useFetch<APIResult<TechniqueCategoryData[]>>(baseURL, filter);
};

const useTechniqueCategoryActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createTechniqueCategory = (payload: TechniqueCategoryPayload) => {
        return mutate(METHODS.POST, baseURL, payload);
    };

    const updateTechniqueCategory = (
        id: number,
        payload: Partial<TechniqueCategoryPayload>,
    ) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, payload);
    };

    const deleteTechniqueCategory = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createTechniqueCategory,
        updateTechniqueCategory,
        deleteTechniqueCategory,
        toggleStatus,
    };
};

export const TechniqueCategoriesServices = {
    useTechniqueCategories,
    useTechniqueCategoryActions,
};
