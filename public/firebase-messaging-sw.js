importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js');
importScripts('https://www.gstatic.com/firebasejs/4.1.1/firebase.js');
// Initialize Firebase
var config = {
    apiKey: "AIzaSyDMugJB-DC7NP4cD8-6F8tDQkQV9lKT6uc",
    authDomain: "rklinic-757dc.firebaseapp.com",
    databaseURL: "https://rklinic-757dc.firebaseio.com",
    projectId: "rklinic-757dc",
    storageBucket: "rklinic-757dc.appspot.com",
    messagingSenderId: "90317623054"
};

firebase.initializeApp(config);

// Retrieve Firebase Messaging object.
const messaging = firebase.messaging();

messaging.setBackgroundMessageHandler(function(payload) {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here
    const notificationTitle = 'Background Message Title';
    const notificationOptions = {
        body: 'Background Message body.',
        icon: '/firebase-logo.png'
    };

    return self.registration.showNotification(notificationTitle,
        notificationOptions);
});
