import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type CourseCategoriesFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface CourseCategoriesData {
    id?: number;
    name?: string;
    price: number;
    days: number;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface CourseCategoryPayload {
    name: string;
    price: number;
    days: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE_CATEGORY}`;

const useCourseCategories = (filter: CourseCategoriesFilter = {}) => {
    return useFetch<APIResult<CourseCategoriesData[]>>(baseURL, filter);
};

const useCourseCategoryDetail = (id: number | string) => {
    return useFetch<APIResult<CourseCategoriesData>>(`/${baseURL}/${id}`);
};

const useCourseCategoryActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createCourseCategories = (data: CourseCategoryPayload) => {
        return mutate(METHODS.POST, baseURL, {
            name: data.name,
            price: data.price,
            days: data.days,
        });
    };

    const updateCourseCategories = (
        id: number,
        data: Partial<CourseCategoryPayload>,
    ) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    };

    const deleteCourseCategories = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createCourseCategories,
        updateCourseCategories,
        deleteCourseCategories,
        toggleStatus,
    };
};

export const CourseCategoriesServices = {
    useCourseCategories,
    useCourseCategoryDetail,
    useCourseCategoryActions,
};
