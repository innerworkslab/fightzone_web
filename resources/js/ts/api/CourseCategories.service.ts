import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type CourseCategoriessFilter = Filters & {
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

export interface CourseCategoriesPayload {
    name: string;
    description: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE_CATEGORY}`;

const useCourseCategoriess = (filter: CourseCategoriessFilter = {}) => {
    return useFetch<APIResult<CourseCategoriesData[]>>(baseURL, filter);
};

const useCourseCategoriesActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createCourseCategories = (data: CourseCategoriesPayload) => {
        return mutate(METHODS.POST, baseURL, data);
    };

    const updateCourseCategories = (
        id: number,
        data: Partial<CourseCategoriesPayload>,
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
    useCourseCategoriess,
    useCourseCategoriesActions,
};
