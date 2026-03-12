import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";
import { cleanPayload } from "@/utils/helper";

export type FeaturedImagesFilter = Filters & {
    image?: File;
};

export interface FeaturedImagesData {
    image?: File;
}

export interface FeaturedImagesPayload {
    image: File | null;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.FEATURED_IMAGES}`;

const useFeaturedImages = (filter: FeaturedImagesFilter = {}) => {
    return useFetch<APIResult<FeaturedImagesData[]>>(baseURL, filter);
};

const useFeaturedImageDetail = (id: number | string) => {
    return useFetch<APIResult<FeaturedImagesPayload>>(`/${baseURL}/${id}`);
};

const useFeaturedImageActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createFeaturedImage = (data: FeaturedImagesPayload | FormData) => {
        return mutate(METHODS.POST, baseURL, data);
    };

    const updateFeaturedImage = (
        id: number,
        data: Partial<FeaturedImagesPayload> | FormData,
    ) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    };

    return {
        loading,
        error,
        data,
        createFeaturedImage,
        updateFeaturedImage,
    };
};

export const FeaturedImagesServices = {
    useFeaturedImages,
    useFeaturedImageDetail,
    useFeaturedImageActions,
};
