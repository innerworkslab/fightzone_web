<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from "vue";
import { Bell, CheckCheck, Loader2 } from "lucide-vue-next";
import { toast } from "vue3-toastify";
import {
    NotificationsServices,
    type AdminNotification,
} from "@/api/Notifications.service";
import {
    listenForForegroundMessages,
    listenForServiceWorkerMessages,
} from "@/lib/firebase.messaging";

const isOpen = ref(false);
const loading = ref(false);
const unreadCount = ref(0);
const notifications = ref<AdminNotification[]>([]);
let unsubscribeMessages: (() => void) | null = null;
let unsubscribeServiceWorkerMessages: (() => void) | null = null;

const unreadIds = computed(() =>
    notifications.value
        .filter((notification) => !Boolean(notification.is_read))
        .map((notification) => notification.notification_id),
);

const formatDate = (value: string) => {
    if (!value) return "";
    return new Date(value).toLocaleString();
};

const refreshNotifications = async () => {
    loading.value = true;
    try {
        const [listResponse, countResponse] = await Promise.all([
            NotificationsServices.getNotifications({ limit: 10 }),
            NotificationsServices.getUnreadCount(),
        ]);
        notifications.value = listResponse.data || [];
        unreadCount.value = countResponse.data?.total_unread_noti_count || 0;
    } finally {
        loading.value = false;
    }
};

const markRead = async (notification: AdminNotification) => {
    if (notification.is_read) return;
    await NotificationsServices.markAsRead(notification.notification_id);
    await refreshNotifications();
};

const markAllRead = async () => {
    if (!unreadIds.value.length) return;
    await NotificationsServices.markAllAsRead(unreadIds.value);
    await refreshNotifications();
};

const handleVisibilityChange = () => {
    if (document.visibilityState === "visible") {
        refreshNotifications();
    }
};

onMounted(async () => {
    await refreshNotifications();
    unsubscribeMessages = await listenForForegroundMessages(async (payload) => {
        toast.info(payload.notification?.title || "New notification");
        await refreshNotifications();
    });
    unsubscribeServiceWorkerMessages = await listenForServiceWorkerMessages(async () => {
        await refreshNotifications();
    });
    window.addEventListener("focus", refreshNotifications);
    document.addEventListener("visibilitychange", handleVisibilityChange);
});

onUnmounted(() => {
    unsubscribeMessages?.();
    unsubscribeServiceWorkerMessages?.();
    window.removeEventListener("focus", refreshNotifications);
    document.removeEventListener("visibilitychange", handleVisibilityChange);
});
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-card text-muted-foreground shadow-sm transition-all hover:bg-primary hover:text-primary-foreground"
            title="Notifications"
            @click="isOpen = !isOpen"
        >
            <Bell class="h-4 w-4" />
            <span
                v-if="unreadCount"
                class="absolute -right-1 -top-1 min-w-5 rounded-full bg-primary px-1.5 py-0.5 text-[10px] font-black leading-none text-primary-foreground"
            >
                {{ unreadCount > 99 ? "99+" : unreadCount }}
            </span>
        </button>

        <div
            v-if="isOpen"
            class="absolute right-0 top-12 z-50 w-[380px] overflow-hidden rounded-lg border border-border bg-card shadow-2xl"
        >
            <div class="flex items-center justify-between border-b border-border px-4 py-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-foreground">
                        Notifications
                    </p>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground">
                        {{ unreadCount }} unread
                    </p>
                </div>
                <button
                    type="button"
                    class="inline-flex h-8 items-center gap-2 rounded-md px-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground disabled:opacity-30"
                    :disabled="!unreadIds.length"
                    @click="markAllRead"
                >
                    <CheckCheck class="h-3.5 w-3.5" />
                    Read All
                </button>
            </div>

            <div class="max-h-[420px] overflow-y-auto">
                <div v-if="loading" class="flex items-center justify-center gap-2 py-8 text-xs text-muted-foreground">
                    <Loader2 class="h-4 w-4 animate-spin" />
                    Loading
                </div>

                <button
                    v-for="notification in notifications"
                    v-else
                    :key="notification.personalized_notification_id"
                    type="button"
                    class="block w-full border-b border-border px-4 py-3 text-left transition-colors hover:bg-secondary/60"
                    :class="notification.is_read ? 'bg-card' : 'bg-primary/5'"
                    @click="markRead(notification)"
                >
                    <div class="flex items-start gap-3">
                        <span
                            class="mt-1 h-2 w-2 flex-shrink-0 rounded-full"
                            :class="notification.is_read ? 'bg-muted' : 'bg-primary'"
                        />
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-foreground">
                                {{ notification.title }}
                            </p>
                            <p class="mt-1 line-clamp-2 text-xs text-muted-foreground">
                                {{ notification.preview }}
                            </p>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-widest text-muted-foreground/70">
                                {{ formatDate(notification.date_time) }}
                            </p>
                        </div>
                    </div>
                </button>

                <div
                    v-if="!loading && !notifications.length"
                    class="px-4 py-8 text-center text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    No notifications
                </div>
            </div>
        </div>
    </div>
</template>
