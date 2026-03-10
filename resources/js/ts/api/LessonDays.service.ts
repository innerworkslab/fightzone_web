import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type LessonDaysFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface LessonDaysData {
    id: number;
    course_level_id: number;
    day_number: number;
    name: string;
    videos_count: number;
    duration_seconds: number;
    formatted_duration: string;
    created_at: string;
    updated_at: string;
}

export interface LessonDaysPayload {
    day_number: number;
}

const baseURL = (id: string) =>
    `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE_LEVEL}/${id}/${API_URLS.LESSON_DAY}`;

const useLessonDays = (id: string, filter: LessonDaysFilter = {}) => {
    return useFetch<APIResult<LessonDaysData[]>>(baseURL(id), filter);
};

const useLessonDayDetail = (id: number | string) => {
    return useFetch<APIResult<LessonDaysData>>(
        `/${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.LESSON_DAY}/${id}`,
    );
};

const useLessonDaysActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createLessonDay = (
        courseLevelId: string,
        data: LessonDaysPayload,
    ) => {
        return mutate(METHODS.POST, baseURL(courseLevelId), data);
    };

    const updateLessonDay = (
        courseLevelId: string,
        id: number,
        data: Partial<LessonDaysPayload>,
    ) => {
        return mutate(
            METHODS.POST,
            `/${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.LESSON_DAY}/${id}`,
            data,
        );
    };

    return {
        loading,
        error,
        data,
        createLessonDay,
        updateLessonDay,
    };
};

export const LessonDaysServices = {
    useLessonDays,
    useLessonDayDetail,
    useLessonDaysActions,
};
