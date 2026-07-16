import { getMessaging, getToken, isSupported, onMessage, type MessagePayload } from "firebase/messaging";
import { firebaseApp, firebaseMessagingVapidKey } from "@/lib/firebase";

const SERVICE_WORKER_PATH = "/firebase-messaging-sw.js";

export async function getAdminFcmToken(): Promise<string | null> {
    try {
        if (!firebaseApp || !firebaseMessagingVapidKey || !("Notification" in window)) {
            return null;
        }

        const supported = await isSupported();
        if (!supported) {
            return null;
        }

        const permission = await Notification.requestPermission();
        if (permission !== "granted") {
            return null;
        }

        const serviceWorkerRegistration = "serviceWorker" in navigator
            ? await navigator.serviceWorker.register(SERVICE_WORKER_PATH)
            : undefined;

        return getToken(getMessaging(firebaseApp), {
            vapidKey: firebaseMessagingVapidKey,
            serviceWorkerRegistration,
        });
    } catch {
        return null;
    }
}

export async function listenForForegroundMessages(
    callback: (payload: MessagePayload) => void,
): Promise<(() => void) | null> {
    if (!firebaseApp) {
        return null;
    }

    const supported = await isSupported();
    if (!supported) {
        return null;
    }

    return onMessage(getMessaging(firebaseApp), callback);
}
