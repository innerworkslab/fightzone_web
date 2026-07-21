import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type TechniquesFilter = Filters & {
    search?: string;
    is_active?: string;
    technique_category_id?: string;
};

export interface TechniqueData {
    id: number;
    technique_category_id: number;
    name: string;
    description?: string;
    thumbnail_url?: string;
    url: string;
    provider?: string;
    provider_video_id?: string;
    duration?: string;
    formatted_duration?: string;
    duration_seconds?: number;
    is_active: boolean;
    category?: {
        id: number;
        name: string;
    };
    created_at: string;
    updated_at: string;
}

export interface TechniquePayload {
    technique_category_id: number;
    name?: string;
    description?: string;
    url: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.TECHNIQUE}`;

const useTechniques = (filter: TechniquesFilter = {}) => {
    return useFetch<APIResult<TechniqueData[]>>(baseURL, filter);
};

const useTechniqueActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createTechnique = (payload: TechniquePayload) => {
        return mutate(METHODS.POST, baseURL, payload);
    };

    const updateTechnique = (id: number, payload: Partial<TechniquePayload>) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, payload);
    };

    const deleteTechnique = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createTechnique,
        updateTechnique,
        deleteTechnique,
        toggleStatus,
    };
};

export const TechniquesServices = {
    useTechniques,
    useTechniqueActions,
};
