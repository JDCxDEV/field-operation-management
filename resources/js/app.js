import "./bootstrap.js"
// import "./firebase.js";

import Modal from "./components/dialogs/Modal";
import ConfirmButton from "./components/buttons/ConfirmButton";
import SignOut from "./components/sign-out/SignOut";
import SetTheme from "./components/themes/SetTheme";
import ActivityLogsTable from "./components/activity-logs/ActivityLogsTable";
import RecentLogs from "./components/activity-logs/RecentLogs";
import "./views/admin/index.js";

Vue.component("modal", Modal);
Vue.component("confirm-button", ConfirmButton);
Vue.component("sign-out", SignOut);
Vue.component("set-theme", SetTheme);
Vue.component("activity-logs-table", ActivityLogsTable);
Vue.component("recent-logs", RecentLogs);

Vue.mount('#app');