import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import type { Filters } from "@/type.global";
import { objectToFormData } from "@/utils/helper";

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
    description?: string;
    image?: File[];
    course_category_id: number;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.COURSE}`;

const useCourses = (filter: CoursesFilter = {}) => {
    return useFetch<APIResult<CourseData[]>>(baseURL, filter);
};

const useCourseDetail = (id: string | string) => {
    return useFetch<APIResult<CourseData>>(`/${baseURL}/${id}`);
};

const useCourseActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createCourse = (data: CoursePayload) => {
        const formData = objectToFormData(data);
        return mutate(METHODS.POST, baseURL, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    };

    const updateCourse = (id: string, data: Partial<CoursePayload>) => {
        const formData = objectToFormData(data);
        return mutate(METHODS.POST, `/${baseURL}/${id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });
    };

    const deleteCourse = (id: string) =>
        mutate(METHODS.DELETE, `/${baseURL}/${id}`);

    const toggleStatus = (id: string) =>
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
