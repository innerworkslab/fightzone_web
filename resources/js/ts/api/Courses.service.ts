import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";

export type CoursesFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface CourseDayData {
    id: number;
    day_number: number;
    duration: string;
    type: CourseDayType;
    video_link: string | null;
    created_at: string;
    updated_at: string;
}

export interface CourseData {
    id: number;
    name: string;
    course_category_id: number;
    level: CourseLevel;
    price: number;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export type CourseLevel = "beginner" | "intermediate" | "expert";

export type CourseDayType = "Lesson" | "Rest";

export interface CourseDayPayload {
    day_number: number;
    duration: string;
    type: CourseDayType;
    video_link: string | null;
}

export interface CoursePayload {
    name: string;
    course_category_id: number;
    level: CourseLevel;
    price: number;
    course_days: CourseDayPayload[];
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE}`;

const useCourses = (filter: CoursesFilter = {}) => {
    return useFetch<APIResult<CourseData[]>>(baseURL, filter);
};

const useCourseDetail = (id: number | string) => {
    return useFetch<APIResult<CourseData>>(`/${baseURL}/${id}`);
};

const useCourseActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createCourse = (data: CoursePayload) => {
        return mutate(METHODS.POST, baseURL, data);
    };

    const updateCourse = (id: number, data: Partial<CoursePayload>) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    };

    const deleteCourse = (id: number) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: number) =>
        mutate(METHODS.POST, `/${baseURL}/${id}/${API_URLS.TOGGLE}`);

    return {
        loading,
        error,
        data,
        createCourse,
        updateCourse,
        deleteCourse,
        toggleStatus,
    };
};

export const CoursesServices = {
    useCourses,
    useCourseDetail,
    useCourseActions,
};
