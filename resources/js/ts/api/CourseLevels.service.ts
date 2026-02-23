import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import api from "@/lib/utils.axios";
import type { Filters } from "@/type.global";

export type CourseLevelsFilter = Filters & {
    search?: string;
    is_active?: "active" | "inactive";
};

export interface CourseLevelsData {
    id?: number;
    course_id: number;
    level: "Beginner" | "Intermediate" | "Expert";
    price: number;
    is_active?: boolean;
    created_at?: string;
    updated_at?: string;
}

export interface CourseLevelsPayload {
    course_id: number;
    level: "Beginner" | "Intermediate" | "Expert";
    price: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE_LEVEL}`;

const useCourseLevels = (id: string, filter: CourseLevelsFilter = {}) => {
    return useFetch<APIResult<CourseLevelsData[]>>(`${baseURL}/${id}`, filter);
};

const useCourseLevelDetail = (id: number | string) => {
    return useFetch<APIResult<CourseLevelsData>>(`${baseURL}/${id}`);
};

const useCourseLevelActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createCourseLevel = (payload: CourseLevelsPayload) => {
        return mutate(METHODS.POST, baseURL, payload);
    };

    const updateCourseLevel = (
        id: number,
        payload: Partial<CourseLevelsPayload>,
    ) => {
        return mutate(METHODS.POST, `${baseURL}/${id}`, payload);
    };

    const deleteCourseLevel = (id: number) =>
        mutate(METHODS.DELETE, `${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createCourseLevel,
        updateCourseLevel,
        deleteCourseLevel,
        toggleStatus,
    };
};

const getCourseLevelsByCourseId = async (id: number) => {
    const response = await api.get(`${baseURL}`);
    return response.data;
};

export const CourseLevelsServices = {
    useCourseLevels,
    useCourseLevelDetail,
    useCourseLevelActions,
    getCourseLevelsByCourseId,
};
