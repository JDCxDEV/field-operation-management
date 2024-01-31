import UserTable from "./users/UserTable";
import UserOverview from "./users/UserOverview";
import VerifyAccount from "./users/VerifyAccount";
import Login from "./auth/Login";
import VerifyCode from "./auth/VerifyCode";
import ForgotPassword from "./auth/ForgotPassword";
import ResetPassword from "./auth/ResetPassword";
import CompanyTable from "./companies/CompanyTable";
import CompanyView from "./companies/CompanyView";
import CompanyOverview from "./companies/CompanyOverview";
import CompanyAppointCoordinator from "./companies/CompanyAppointCoordinator";
import MarketTable from "./markets/MarketTable";
import MarketView from "./markets/MarketView";
import MarketOverview from "./markets/MarketOverview";
import MarketAppointDirector from "./markets/MarketAppointDirector";
import MarketAppointPodLeader from "./markets/MarketAppointPodLeader";
import RecruitTable from "./recruits/RecruitTable";
import FormsTable from "./agent-submission-forms/FormsTable";
import AgentSubmissionForm from "./agent-submission-forms/AgentSubmissionForm";
import BlacklistedAddressTable from "./blacklisted-addresses/BlacklistedAddressTable";
import BlacklistedAddressView from "./blacklisted-addresses/BlacklistedAddressView";
import StatusTable from "./statuses/StatusTable";
import StatusView from "./statuses/StatusView";
import FormTemplateTable from "./form-templates/FormTemplateTable";
import FormTemplateView from "./form-templates/FormTemplateView";
import FormTemplateOverview from "./form-templates/FormTemplateOverview";
import TopicTable from "./topics/TopicTable";
import TopicView from "./topics/TopicView";

/** Auth components */
Vue.component("login", Login);
Vue.component("verify-code", VerifyCode);
Vue.component("forgot-password", ForgotPassword);
Vue.component("reset-password", ResetPassword);

/** User components */
Vue.component("user-table", UserTable);
Vue.component("user-overview", UserOverview);
Vue.component("verify-account", VerifyAccount);

/** Company components */
Vue.component("company-table", CompanyTable);
Vue.component("company-view", CompanyView);
Vue.component("company-appoint-coordinator", CompanyAppointCoordinator);
Vue.component("company-overview", CompanyOverview);

/** Market components */
Vue.component("market-table", MarketTable);
Vue.component("market-view", MarketView);
Vue.component("market-appoint-director", MarketAppointDirector);
Vue.component("market-appoint-pod-leader", MarketAppointPodLeader);
Vue.component("market-overview", MarketOverview);

/** Recruit component */
Vue.component("recruit-table", RecruitTable);

/** Agent Submission Form component */
Vue.component("forms-table", FormsTable);
Vue.component("agent-submission-form", AgentSubmissionForm);

/** Blacklisted Address component */
Vue.component("blacklisted-address-table", BlacklistedAddressTable);
Vue.component("blacklisted-address-view", BlacklistedAddressView);

/** Status component */
Vue.component("status-table", StatusTable);
Vue.component("status-view", StatusView);

/** Form template component */
Vue.component("form-template-overview", FormTemplateOverview);
Vue.component("form-template-table", FormTemplateTable);
Vue.component("form-template-view", FormTemplateView);

/** Topic component */
Vue.component("topic-table", TopicTable);
Vue.component("topic-view", TopicView);