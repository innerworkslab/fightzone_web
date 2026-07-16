self.addEventListener("push", (event) => {
    const payload = event.data ? event.data.json() : {};
    const notification = payload.notification || {};
    const data = payload.data || {};
    const title = notification.title || data.title;

    if (!title) {
        return;
    }

    event.waitUntil(
        self.registration.showNotification(title, {
            body: notification.body || data.preview || data.body,
            icon: notification.icon || "/favicon.ico",
            image: notification.image,
            data,
        }),
    );
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow("/auth/admins"));
});
