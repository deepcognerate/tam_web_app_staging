// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in
// your app's Firebase config object.
// https://firebase.google.com/docs/web/setup#config-object
firebase.initializeApp({
    // apiKey: "AIzaSyBVwfEvl5Gtmi1u6Tq5q0pCDbfPugenQYE",
    // authDomain: "tam-app-dev.firebaseapp.com",
    // projectId: "tam-app-dev",
    // storageBucket: "tam-app-dev.appspot.com",
    // messagingSenderId: "906777746662",
    // appId: "1:906777746662:web:e4d6e511e2a1a4245d2f27",

    apiKey: "AIzaSyAADXQHgQTNgBFZyCjDsV3W6Z7oc9B1B2g",
    authDomain: "tam-app-staging.firebaseapp.com",
    projectId: "tam-app-staging",
    storageBucket: "tam-app-staging.appspot.com",
    messagingSenderId: "991731337312",
    appId: "1:991731337312:web:ea28a22d09e482f34a9b05",


    
  });

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();


console.log('messaging.onBackgroundMessage :: xxx');

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    // Customize notification here

    var title = payload.notification.title;
    var bodytitle = payload.notification.body;


    const notificationTitle = title;
    const notificationOptions = {
      body: bodytitle,
      icon: '/firebase-logo.png'
    };
    
    var thisKeys = payload.data.key;
    
    if(thisKeys == "live_user_message" || thisKeys == "user_assign_to_consellor"){
       self.registration.showNotification(notificationTitle,
      notificationOptions); 
    }

    if(thisKeys == "async_chat_escalated_alert_to_counsellor"){
       self.registration.showNotification(notificationTitle,
      notificationOptions); 
    }

    if(thisKeys == "live_counsellor_assign_to_admin"){
        self.registration.showNotification(notificationTitle,
        notificationOptions); 
    }

    if(thisKeys == "async_user_chat"){
       self.registration.showNotification(notificationTitle,
      notificationOptions); 
    }
  });
  