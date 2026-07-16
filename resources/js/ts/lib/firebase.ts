import { initializeApp, type FirebaseApp } from "firebase/app";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_GOOGLE_API_KEY,
    authDomain: import.meta.env.VITE_GOOGLE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_GOOGLE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_GOOGLE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_GOOGLE_MESSAGING_SENDER_ID,
    appId: import.meta.env.VITE_GOOGLE_APP_ID,
    measurementId: import.meta.env.VITE_GOOGLE_MEASUREMENT_ID,
};

const hasFirebaseConfig = Boolean(
    firebaseConfig.apiKey &&
        firebaseConfig.projectId &&
        firebaseConfig.messagingSenderId &&
        firebaseConfig.appId,
);

export const firebaseApp: FirebaseApp | null = hasFirebaseConfig
    ? initializeApp(firebaseConfig)
    : null;

export const firebaseMessagingVapidKey = import.meta.env.VITE_FIREBASE_VAPID_KEY;
