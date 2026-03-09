import { APIResult, useFetch, useMutation } from "@/composable/useAPI";
import { API_URLS, METHODS } from "@/constant/global.constant";
import api from "@/lib/utils.axios";
import type { Filters } from "@/type.global";

export type LessonDayVideosFilter = Filters & {
    search?: string;
    status?: "active" | "inactive";
};

export interface LessonDayVideosData {
    id: number;
    course_level_id: number;
    day_number: number;
    type: string;
    name: string;
    duration: string;
    videos_count: number;
    duration_seconds: number;
    formatted_duration: string;
    created_at: string;
    updated_at: string;
}

export interface LessonDayVideosPayload {
    lesson_day_id?: number;
    name?: string;
    description?: string;
    duration?: string;
    url?: string;
    type?: string;
}

const baseURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/${API_URLS.LESSON_DAY_VIDEO}`;
const lessonDayURL = `${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/lesson-days`;

const useLessonDayVideos = (
    lessonDayId: string,
    filter: LessonDayVideosFilter = {},
) => {
    return useFetch<APIResult<LessonDayVideosData[]>>(
        `${baseURL}?id=${lessonDayId}`,
        filter,
    );
};

const useLessonDayVideosActions = () => {
    const { mutate, loading, error, data } = useMutation();

    const createLessonDayVideo = (data: LessonDayVideosPayload) => {
        return mutate(METHODS.POST, baseURL, data);
    };

    const updateLessonDayVideo = (
        id: number,
        data: Partial<LessonDayVideosPayload>,
    ) => {
        return mutate(METHODS.POST, `/${baseURL}/${id}`, data);
    };

    return {
        loading,
        error,
        data,
        createLessonDayVideo,
        updateLessonDayVideo,
    };
};

const getLessonDayVideosByLessonDayId = async (id: number) => {
    const response = await api.get(`${lessonDayURL}/${id}`);
    return response.data;
};

export const LessonDayVideosServices = {
    getLessonDayVideosByLessonDayId,
    useLessonDayVideos,
    useLessonDayVideosActions,
};
