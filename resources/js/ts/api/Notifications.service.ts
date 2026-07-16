import { APIResult } from "@/composable/useAPI";
import { API_URLS } from "@/constant/global.constant";
import api from "@/lib/utils.axios";

export interface AdminNotification {
    personalized_notification_id: number;
    notification_id: number;
    is_read: number | boolean;
    is_read_count: number;
    title: string;
    preview: string;
    date_time: string;
    personable_type: string;
    personable_id: number;
    notificationable_id?: number | null;
    notificationable_type?: string | null;
}

const baseURL = `/${API_URLS.VERSION}/${API_URLS.MANAGEMENT}/notifications`;

const getNotifications = async (params: { status?: "read" | "unread"; limit?: number } = {}) => {
    const response = await api.get<APIResult<AdminNotification[]>>(baseURL, {
        params,
    });
    return response.data;
};

const getUnreadCount = async () => {
    const response = await api.get<APIResult<{ total_unread_noti_count: number }>>(
        `${baseURL}/unread_count`,
    );
    return response.data;
};

const markAsRead = async (notificationId: number) => {
    const response = await api.post<APIResult<string>>(`${baseURL}/${notificationId}/mark_read`);
    return response.data;
};

const markAllAsRead = async (ids: number[]) => {
    const response = await api.post<APIResult<string>>(`${baseURL}/mark_all_read`, {
        ids,
    });
    return response.data;
};

export const NotificationsServices = {
    getNotifications,
    getUnreadCount,
    markAsRead,
    markAllAsRead,
};
