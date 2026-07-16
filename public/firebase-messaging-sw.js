self.addEventListener("push", (event) => {
    const payload = event.data ? event.data.json() : {};
    const notification = payload.notification || {};
    const data = payload.data || {};
    const title = notification.title || data.title;

    if (!title) {
        return;
    }

    event.waitUntil(
        Promise.all([
            self.registration.showNotification(title, {
                body: notification.body || data.preview || data.body,
                icon: notification.icon || "/favicon.ico",
                image: notification.image,
                data,
            }),
            clients.matchAll({ type: "window", includeUncontrolled: true }).then((clientList) => {
                clientList.forEach((client) => {
                    client.postMessage({
                        type: "fcm-notification",
                        payload,
                    });
                });
            }),
        ]),
    );
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow("/auth/admins"));
});
