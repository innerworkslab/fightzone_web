import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type RestDayVideosFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface RestDayVideoDayData {
    id: number;
    day_number: number;
    duration: string;
    url: string | null;
    created_at: string;
    updated_at: string;
}

export interface RestDayVideoData {
    id: number;
    name: string;
    price: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface RestDayVideoPayload {
    url: string | null;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.REST_DAY_VIDEO}`;

const useRestDayVideos = (filter: RestDayVideosFilter = {}) => {
    return useFetch<APIResult<RestDayVideoData[]>>(baseURL, filter);
};

const useRestDayVideoDetail = (id: string | string) => {
    return useFetch<APIResult<RestDayVideoData>>(`/${baseURL}/${id}`);
};

const useRestDayVideoActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createRestDayVideo = (data: RestDayVideoPayload) => {
        return mutate(METHODS.POST, baseURL, data);
    };

    const updateRestDayVideo = (
        id: string,
        data: Partial<RestDayVideoPayload>,
    ) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    };

    const deleteRestDayVideo = (id: string) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: string) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createRestDayVideo,
        updateRestDayVideo,
        deleteRestDayVideo,
        toggleStatus,
    };
};

export const RestDayVideosServices = {
    useRestDayVideos,
    useRestDayVideoDetail,
    useRestDayVideoActions,
};
