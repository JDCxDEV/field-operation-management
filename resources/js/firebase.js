var firebaseConfig = {
  apiKey: 'AIzaSyB7nMffGwiOtoAjH7vD97rZsD55pYOsr3A',
  authDomain: 'field-ops-94fa0.firebaseapp.com',
  projectId: 'field-ops-94fa0',
  storageBucket: 'field-ops-94fa0.appspot.com',
  messagingSenderId: '536316176748',
  appId: '1:536316176748:web:8ab5dcc85c5d3cb0f8eb49',
  measurementId: 'G-VPR94BJVN7',
};

firebase.initializeApp(firebaseConfig);
window.messaging = firebase.messaging();