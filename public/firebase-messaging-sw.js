// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here. Other Firebase libraries
// are not available in the service worker.importScripts('https://www.gstatic.com/firebasejs/7.23.0/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');
/*
Initialize the Firebase app in the service worker by passing in the messagingSenderId.
*/
firebase.initializeApp({
  apiKey: 'AIzaSyB7nMffGwiOtoAjH7vD97rZsD55pYOsr3A',
  authDomain: 'field-ops-94fa0.firebaseapp.com',
  projectId: 'field-ops-94fa0',
  storageBucket: 'field-ops-94fa0.appspot.com',
  messagingSenderId: '536316176748',
  appId: '1:536316176748:web:8ab5dcc85c5d3cb0f8eb49',
  measurementId: 'G-VPR94BJVN7',
});

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function (payload) {
  console.log("Message received.", payload);
  const title = "Hello world is awesome";
  const options = {
    body: "Your notificaiton message .",
    icon: "/firebase-logo.png",
  };
  return self.registration.showNotification(
    title,
    options,
  );
});