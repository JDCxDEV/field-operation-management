import { createApp } from 'vue'
import VueSignaturePad from 'vue-signature-pad';

const app = createApp({});

app.config.warnHandler = function (msg, vm, trace) {
  return null
};

app.use(VueSignaturePad);
window.Vue = app;