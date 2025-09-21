importScripts('https://www.gstatic.com/firebasejs/11.5.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.5.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyBNGAsSVJwHANv1s5ycDHEV7dOPPlnfSLw",
    authDomain: "disciplinary-dee3b.firebaseapp.com",
    projectId: "disciplinary-dee3b",
    storageBucket: "disciplinary-dee3b.firebasestorage.app",
    messagingSenderId: "598587357589",
    appId: "1:598587357589:web:1eed8ca760763585179e9c"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification.title || 'Background Message';
    const notificationOptions = {
        body: payload.notification.body || 'Background Message body.',
        icon: '/notification-icon.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
