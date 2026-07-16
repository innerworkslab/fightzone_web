import { getMessaging, getToken, isSupported, onMessage, type MessagePayload } from "firebase/messaging";
import { firebaseApp, firebaseMessagingVapidKey } from "@/lib/firebase";

const SERVICE_WORKER_PATH = "/firebase-messaging-sw.js";

async function registerMessagingServiceWorker() {
    if (!("serviceWorker" in navigator)) {
        return undefined;
    }

    return navigator.serviceWorker.register(SERVICE_WORKER_PATH);
}

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

        const serviceWorkerRegistration = await registerMessagingServiceWorker();

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

export async function listenForServiceWorkerMessages(
    callback: (payload: unknown) => void,
): Promise<(() => void) | null> {
    if (!("serviceWorker" in navigator)) {
        return null;
    }

    await registerMessagingServiceWorker();

    const handler = (event: MessageEvent) => {
        if (event.data?.type !== "fcm-notification") {
            return;
        }

        callback(event.data.payload);
    };

    navigator.serviceWorker.addEventListener("message", handler);

    return () => {
        navigator.serviceWorker.removeEventListener("message", handler);
    };
}
