<template>

<stepper
v-if="!accordionDisplay" 
:steps="tabs"
:current-step="currentStep"
:last-access-tab="lastAccessTab"
:force-mobile="forceMobile"
@jumpStep="jumpStep"
class="mb-7"
/>

<div v-if="isLoading">
    <div class="text-center my-10">
        <span class="spinner-border text-primary" style="width: 100px; height: 100px" role="status">
            <span class="visually-hidden">Loading...</span>
        </span>
    </div>
</div>

<div 
v-if="!isLoading && !accordionDisplay">
    <client-validation
    :form-data="formData" 
    :client-validation-url="clientValidationUrl"
    :client-address-validation-url="clientAddressValidationUrl"
    :employment-data-validation-url="employmentDataUrl"
    :edit-mode="editMode"
    :last-access-tab="lastAccessTab || 1"
    @proceed="proceed($event)"
    v-if="currentTab.key == 'client-validation' && currentTab.step == currentStep"/>

    <enrollment-data 
    :form-data="formData"
    :submit-enrollment-information-url="enrollmentDataUrl"
    :edit-mode="editMode"
    @back="back()"
    @proceed="proceed($event)"
    @updateForm="updateForm($event)"
    v-if="currentTab.key == 'enrollment-data' && currentTab.step == currentStep"/>

    <plan-information 
    :form-data="formData"
    :submit-url="planInformationUrl"
    :edit-mode="editMode"
    @back="back()"
    @proceed="proceed($event)"
    v-if="currentTab.key == 'plan-information' && currentTab.step == currentStep"/>

    <form-finalize
    :form-data="formData"
    :template="template"
    :additional-files-submit-url="additionalFilesUrl"
    :save-signature-url="saveSignatureUrl"
    :submitUrl="submitUrl"
    :edit-mode="editMode"
    :coordinates="coordinates"
    @back="back"
    @updateForm="updateForm($event)"
    @successSubmission="handleSuccess"
    v-if="currentTab.key == 'form-finalize' && currentTab.step == currentStep"/>

</div>

<div 
v-if="!isLoading && accordionDisplay"
class="accordion" id="steps-accordion">
    <div v-for="tab in tabs" 
    :class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" :id="`header-${tab.key}`">
            <button 
            :disabled="tab.step > lastAccessTab"
            @click="jumpStep(tab)"
            :class="{'collapsed': lastAccessTab != tab.step, 'bg-light text-muted': (lastAccessTab != tab.step && lastAccessTab < tab.step) }"
            class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" :data-bs-target="`#body-${tab.key}`" aria-expanded="true" :aria-controls="`body-${tab.key}`">
                <i v-if="lastAccessTab != tab.step && lastAccessTab < tab.step && currentTab.step != tab.step && currentTab.step < tab.step" class="ki-duotone ki-lock-2 fs-1" style="margin-right: 10px">
					<i class="path1"></i>
					<i class="path2"></i>
					<i class="path3"></i>
					<i class="path4"></i>
					<i class="path5"></i>
				</i>
                <span>{{ tab.name }}</span>
            </button>
        </h2>
        <div :id="`body-${tab.key}`" 
        :class="{'show': currentStep == tab.step}"
        class="accordion-collapse collapse" :aria-labelledby="`header-${tab.key}`" data-bs-parent="#steps-accordion">
            <div class="accordion-body" style="min-height: 300px">
                <client-validation
                :form-data="formData" 
                :client-validation-url="clientValidationUrl"
                :client-address-validation-url="clientAddressValidationUrl"
                :employment-data-validation-url="employmentDataUrl"
                :edit-mode="editMode"
                :accordion-type="false"
                :last-access-tab="lastAccessTab"
                :enable-watcher="enableWatcher"
                :next-button="nextButton"
                @saveInput="saveInput"
                @back="back()"
                @proceed="proceed($event)"
                v-if="tab.key == 'client-validation' && tab.step == currentStep"/>

                <enrollment-data 
                :form-data="formData"
                :submit-client-address-url="clientAddressValidationUrl"
                :submit-enrollment-information-url="enrollmentDataUrl"
                :edit-mode="editMode"
                :accordion-type="false"
                :enable-watcher="enableWatcher"
                :next-button="nextButton"
                @saveInput="saveInput"
                @back="back()"
                @proceed="proceed($event)"
                @updateForm="updateForm($event)"
                v-if="tab.key == 'enrollment-data' && tab.step == currentStep"/>

                <plan-information 
                :form-data="formData"
                :submit-url="planInformationUrl"
                :edit-mode="editMode"
                :enable-watcher="enableWatcher"
                @saveInput="saveInput"
                @back="back()"
                @proceed="proceed($event)"
                :next-button="nextButton"
                v-if="tab.key == 'plan-information' && tab.step == currentStep"/>

                <form-finalize
                :form-data="formData"
                :template="template"
                :additional-files-submit-url="additionalFilesUrl"
                :save-signature-url="saveSignatureUrl"
                :submitUrl="submitUrl"
                :edit-mode="editMode"
                :coordinates="coordinates"
                :enable-watcher="enableWatcher"
                @saveInput="saveInput"
                @back="back"
                @updateForm="updateForm($event)"
                @successSubmission="handleSuccess"
                v-if="tab.key == 'form-finalize' && tab.step == currentStep"/>

                <attachments
                :form-data="formData"
                v-if="tab.key == 'attachments' && tab.step == currentStep"/>

                <activity-logs-table
                    v-if="tab.key == 'interactions' && tab.step == currentStep"
                    :fetchUrl="`/activity-logs/fetch-by-subject/${formData.id}/Form`"
                    :currentMode="currentMode"
                    no-filter
                ></activity-logs-table>
            </div>
        </div>
    </div>

    <div class="row my-10">
        <div class="col text-left">
            <button 
            type="button"
            @click="closeModal()"
            class="btn btn-light me-3">Discard</button>
        </div>
        <div class="col text-right">
            <button
            :disabled="saveChangesLoading" 
            @click="saveChanges()"
            class="btn btn-primary">Save Changes</button>
        </div>
    </div>
</div>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import GeolocationMixins from "Mixins/geolocation.js";
import ClientValidation from "./forms/ClientValidation.vue";
import EnrollmentData from "./forms/EnrollmentData.vue";
import AdditionalFiles from "./forms/AdditionalFiles.vue";
import PlanInformation from "./forms/PlanInformation.vue";
import PaymentInformation from "./forms/PaymentInformation.vue";
import FormFinalize from "./forms/FormFinalize.vue";
import Attachments from "./forms/Attachments.vue";
import Stepper from "Components/steppers/Stepper.vue";

export default {
    name: "agent-submission-form",

    props: {
        fetchUrl: {
            type: String,
            default: ""
        },
        fetchTemplateUrl: {
            type: String,
            default: ""
        },
        clientValidationUrl: {
            type: String,
            default: ""
        },
        clientAddressValidationUrl: {
            type: String,
            default: ""
        },
        enrollmentDataUrl: {
            type: String,
            default: ""
        },
        additionalFilesUrl: {
            type: String,
            default: ""
        },
        employmentDataUrl: {
            type: String,
            default: ""
        },
        planInformationUrl: {
            type: String,
            default: ""
        },
        paymentInformationUrl: {
            type: String,
            default: ""
        },
        saveSignatureUrl: {
            type: String,
            default: ""
        },
        submitUrl: {
            type: String,
            default: ""
        },
        viewing: {
            type: Boolean,
            default: false
        },
        forceMobile: {
            type: Boolean,
            default: false
        },
        noRedirect: {
            type: Boolean,
            default: false
        },
        formInteractions: {
            type: Boolean,
            default: false
        },
        accordionDisplay: {
            type: Boolean,
            default: false
        },
        enableWatcher: {
            type: Boolean,
			default: true
        },

        nextButton: {
            type: Boolean,
            default: false
        } 
    },

    components: { 
        ClientValidation,
        EnrollmentData,
        AdditionalFiles,
        PlanInformation,
        PaymentInformation,
        FormFinalize,
        Attachments,
        Stepper
    },

    computed: {
        currentStep() {
            return this.currentTab.step;
        },

        currentTabStep() {
            return this.currentTab.step - 1;
        }
    },

    data: () => ({

        formData: "",

        tabs: [
            { name: "Client Validation", key: "client-validation", step: 1, icon: "fa-solid fa-person-circle-exclamation" },
            { name: "Family & Enrollment", key: "enrollment-data", step: 2, icon: "fa-solid fa-user-plus"},
            { name: "Plan Information", key: "plan-information", step: 3, icon: "fa-solid fa-list-check"},
            { name: "Finalize", key: "form-finalize", step: 4, icon: "fa-solid fa-check"},
        ],

        currentTab: {},
        lastAccessTab: "",
        status: "",
        editMode: true,
        template: {},

        isLoading: true,
        tempFormValue: "",
        saveChangesLoading: false,
        currentMode: "light",
    }),

    mounted () {
        this.currentMode = KTThemeMode.getMode();
        this.currentTab = this.tabs[0];
        if(this.fetchUrl) {
            this.fetch();
        } else {
            this.isLoading = false;
        }
        if(this.formInteractions) {
            this.tabs.push(
                {
                    name: "Attachments",
                    key: "attachments",
                    step: -1,
                    icon: "fa-list-tree"                    
                },
                {
                    name: "Interactions",
                    key: "interactions",
                    step: 0,
                    icon: "fa-list-tree"
                },
            );
        }
        this.fetchTemplate();
    },

    mixins: [GeolocationMixins, ResponseMixins, FormatterMixins],

    methods: {

        fetch() {
            this.isLoading = true;
            axios.get(this.fetchUrl)
                .then((res) => {
                    this.formData = res.data.form;
                    this.tempFormValue = res.data.form;
                    this.status = this.formData.status;
                    // status 1 === SUBMITTED
                    if(this.status === 1 && this.viewing) {
                        this.editMode = false;
                    }
                    const formTab = this.formData.current_step > this.tabs.length ? this.tabs.length : this.formData.current_step;
                    this.currentTab = this.tabs[formTab - 1];
                    this.lastAccessTab = this.formData.current_step;
                }).catch((err) => {
                    this.parseError(err, {});
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        fetchTemplate() {
            axios.get(this.fetchTemplateUrl)
                .then((res) => {
                    this.template = res.data.template;
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        back(step = null) {
            if(step) {
                this.currentTab = this.tabs[(step - 1)];
            } else {
                const tab = this.currentTabStep - 1;
                this.currentTab = this.tabs[tab];
            }

        },

        updateForm(data) {
            if(data) {
                this.formData = data;
            }
        },

        proceed(data = "") {
            if(data) {
               this.formData = data; 
            }
            let tab = "";
            if(this.formData?.current_step == this.tabs.length && this.viewing == false) {
                tab = this.formData.current_step - 1
            } else {
                tab = this.currentTabStep + 1;
            }
            this.currentTab = this.tabs[tab];
            if(this.lastAccessTab <= tab) {
                this.lastAccessTab = tab;
            }

        },

        handleSuccess(data) {
            if(!this.noRedirect) {
                setTimeout(() => {
                    window.location.href = data;
                }, 2000);                                
            } else {
                this.$emit("submitted");
                this.$emit("close");
            }
        },
        
        jumpStep(tab) {
            this.currentTab = tab;
            if(this.enableWatcher) {
                this.formData = this.tempFormValue;
            }
        },

        saveChanges() {
            const data = {
                client_first_name: this.tempFormValue?.client_first_name,
                client_last_name: this.tempFormValue?.client_last_name,
                client_dob: this.tempFormValue?.client_dob,
                client_ssn: this.clean(this.tempFormValue?.client_ssn),
                client_phone: this.clean(this.tempFormValue?.client_phone),
                client_sex: this.tempFormValue?.client_sex,
                client_phone: this.tempFormValue?.client_phone,
                client_email: this.tempFormValue?.client_email,
                client_address_line_1: this.tempFormValue?.client_address_line_1,
                client_address_line_2: this.tempFormValue?.client_address_line_2,
                client_city: this.tempFormValue?.client_city,
                client_state: this.tempFormValue?.client_state,
                client_zip: this.tempFormValue?.client_zip,
                employer_name: this.tempFormValue?.employer_name,
                employer_phone: this.clean(this.tempFormValue?.employer_phone),
                income: this.clean(this.tempFormValue?.income?.toString()),
                income_frequency: "Yearly",
                
                plan_premium: this.tempFormValue?.plan_premium,
                plan_name: this.tempFormValue?.plan_name,
                plan_id: this.tempFormValue?.plan_id,
                insurance: this.tempFormValue?.insurance,
                tax_credit_amount: this.clean(this.tempFormValue?.tax_credit_amount?.toString()),
                you_pay: this.clean(this.tempFormValue?.you_pay?.toString()),

                product_change: this.tempFormValue?.product_change,
                pregnant: this.tempFormValue?.pregnant,
                married: this.tempFormValue?.married,
                smoker: this.tempFormValue?.smoker,
                dependents: this.tempFormValue?.dependents,
                american_indian_native: this.tempFormValue?.american_indian_native,     
                
                spousal_first_name: this.tempFormValue?.spousal_first_name,
                spousal_last_name: this.tempFormValue?.spousal_last_name,
                spousal_dob: this.tempFormValue?.spousal_dob,
                spousal_employer_name: this.tempFormValue?.spousal_employer_name,
                spousal_type: this.tempFormValue?.spousal_type, 

                spousal_ssn: this.clean(this.tempFormValue?.spousal_ssn),
                spousal_income: this.clean(this.tempFormValue?.spousal_income?.toString()),
                spousal_employer_phone: this.clean(this.tempFormValue?.spousal_employer_phone),

                additional_notes: this.tempFormValue?.additional_notes,
            };
            this.saveChangesLoading = true;
            axios.post(`/agent-submission-forms/save-changes/${this.formData.id}`, data)
                .then(() => {
                    this.parseSuccess("Form has been updated");
                }).catch((err) => {
                    this.parseError(err, {});
                }).finally(() => {
                    this.saveChangesLoading = false;
                });
        },

        closeModal() {
            this.$emit("close");
        },

        saveInput(data) {
            this.tempFormValue = { ...this.tempFormValue, ...data };
		}
    }
}
</script>
<style>
.no-padding {
    padding: 0px !important;
}

</style>