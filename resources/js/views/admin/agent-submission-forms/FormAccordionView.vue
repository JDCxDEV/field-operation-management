<template>
<!--begin::Accordion-->
<div class="accordion" id="form_accordion">
	<div 
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="application_meta_header">
            <button class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#application_meta_body" aria-expanded="true" aria-controls="application_meta_body">
                Application Meta
            </button>
        </h2>
        <div id="application_meta_body" class="accordion-collapse collapse show" aria-labelledby="application_meta_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
				<div class="row mb-2">
					<div class="col-4">
						<label><strong>Application ID:</strong> {{ form.application_id }}</label>
					</div>
					<div class="col-4">
						<label><strong>Longitude & Latitude:</strong> {{ form.long || 'N/A' }}, {{ form.lat || 'N/A' }}</label>
					</div>
				</div>
            </div>
        </div>
    </div>
    <div 
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="client_validation_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#client_validation_body" aria-expanded="true" aria-controls="client_validation_body">
                Client Validation
            </button>
        </h2>
        <div id="client_validation_body" class="accordion-collapse collapse" aria-labelledby="client_validation_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
                <div class="row mb-7">
					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Client First Name</label>
						<input 
						@input="form.client_first_name = alphabetStringOnly($event)"
						v-model="form.client_first_name"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.client_first_name}"
						class="form-control mb-3 mb-lg-0" 
						name="client_first_name"
						placeholder="Client First Name"
						/>
						<div 
						:v-if="errors?.client_first_name"
						class="invalid-feedback">{{ errors?.client_first_name?.[0] }}</div>            
					</div>

					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Client Last Name</label>
						<input
						@input="form.client_last_name = alphabetStringOnly($event)"                     
						v-model="form.client_last_name"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.client_last_name}"
						class="form-control mb-3 mb-lg-0" 
						name="client_last_name"
						placeholder="Client Last Name"
						/>
						<div 
						:v-if="errors?.client_last_name"
						class="invalid-feedback">{{ errors?.client_last_name?.[0] }}</div>            
					</div>           
				</div>
				<div class="row mb-7">
					<div class="col-md-4">
						<phone-number
						:value="form.client_phone"
						label="Client's Phone Number"
						:disabled="isLoading || !editMode"
						:errors="errors?.client_phone ? true: false"
						:error-message="errors?.client_phone?.[0]"
						length="14"
						placeholder="Client's Phone Number"
						@update:value="newValue => form.client_phone = newValue"
						></phone-number>
					</div>

					<div class="col-md-4">
						<label class="required fw-semibold fs-6 mb-2">Client Date of Birth</label>
						<input 
						v-model="form.client_dob"
						:disabled="isLoading || !editMode"
						type="date"
						:class="{'is-invalid': errors?.client_dob}"
						:max="max"
						:min="min"
						class="form-control mb-3 mb-lg-0" name="client_dob" />
						<div 
						:v-if="errors?.client_dob"
						class="invalid-feedback">{{ errors?.client_dob?.[0] }}</div>            
					</div>

					<div class="col-md-4">
						<SSNField
						:value="form.client_ssn"
						label="Client SSN"
						name="client_ssn"
						length="11"
						input-class="form-control mb-3 mb-lg-0"
						:errors="errors?.client_ssn"
						:error-message="errors?.client_ssn?.[0]"
						:disabled="isLoading || !editMode"
						@update:value="newValue => form.client_ssn = newValue"
						></SSNField>
					</div> 
				</div>
            </div>
        </div>
    </div>

    <div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="client_details_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#client_details_body" aria-expanded="false" aria-controls="client_details_body">
            Client Details
            </button>
        </h2>
        <div id="client_details_body" class="accordion-collapse collapse" aria-labelledby="client_details_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
				<div class="row mb-7">
					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Client Sex</label>
						<select
						:disabled="isLoading || !editMode"
						name="client_sex"
						v-model="form.client_sex"
						:class="{'is-invalid': errors?.client_sex}"
						class="form-select fw-bold mb-3 mb-lg-0">
							<option selected disabled value="">Select Client Sex</option>
							<option value="Female">Female</option>
							<option value="Male">Male</option>
						</select>
						<div 
						:v-if="errors?.client_sex"
						class="invalid-feedback">{{ errors?.client_sex?.[0] }}</div>     
					</div>
					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Client's Email</label>
						<input 
						v-model="form.client_email"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.client_email}"
						placeholder="Client's Email"
						class="form-control mb-3 mb-lg-0" name="client_email" />
						<div 
						:v-if="errors?.client_email"
						class="invalid-feedback">{{ errors?.client_email?.[0] }}</div>            
					</div>
				</div>
				<div class="row mb-7">
					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Client Address Line One</label>
						<auto-complete-address 
						:address="form.client_address_line_1"
						name="client_address_line_1"
						:has-error="errors?.client_address_line_1"
						:error-message="errors?.client_address_line_1 ? errors?.client_address_line_1?.[0] : ''"
						:disabled="isLoading || !editMode"
						placeholder="Client Address Line One"
						@valueChanged="setValue($event, 'client_address_line_1')"
						@autocomplete="autocomplete($event)"
						/>
					</div>
					<div class="col-md-6">
						<label class="fw-semibold fs-6 mb-2">Client Address Line Two</label>
						<input 
						v-model="form.client_address_line_2"
						name="client_address_line_2"
						:disabled="isLoading || !editMode"
						class="form-control mb-3 mb-lg-0"
						:class="{'is-invalid': errors?.client_address_line_2}" 
						placeholder="Client Address Line Two"/>
						<div 
						:v-if="errors?.client_address_line_2"
						class="invalid-feedback">{{ errors?.client_address_line_2?.[0] }}</div>
					</div>
				</div>
				<div class="row mb-7">
					<div class="col-md-4">
						<label class="required fw-semibold fs-6 mb-2">Client City</label>
						<input 
						v-model="form.client_city"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.client_city}"
						placeholder="Client City"
						class="form-control mb-3 mb-lg-0" name="client_city" />
						<div 
						:v-if="errors?.client_city"
						class="invalid-feedback">{{ errors?.client_city?.[0] }}</div>            
					</div>
					<div class="col-md-4">
						<label class="required fw-semibold fs-6 mb-2">Client State</label>
						<select
						v-model="form.client_state"
						:disabled="isLoading || !editMode"
						name="client_state"
						:class="{'is-invalid': errors?.client_state}"
						class="form-select fw-bold">
							<option value="" selected disabled>Select State</option>
							<option v-for="state in states" :value="state.abbreviation">{{ state.name }}</option>
						</select>
						<div 
						:v-if="errors?.client_state"
						class="invalid-feedback">{{ errors?.client_state?.[0] }}</div>           
					</div>
					<div class="col-md-4">
						<label class="required fw-semibold fs-6 mb-2">Client Zip</label>
						<input 
						v-model="form.client_zip"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.client_zip}"
						placeholder="Client Zip"
						class="form-control mb-3 mb-lg-0" name="client_zip" />
						<div 
						:v-if="errors?.client_zip"
						class="invalid-feedback">{{ errors?.client_zip?.[0] }}</div>            
					</div>
				</div>
            </div>
        </div>
    </div>

    <div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="employment_data_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#employment_data_body" aria-expanded="false" aria-controls="employment_data_body">
            Employment Data
            </button>
        </h2>
        <div id="employment_data_body" class="accordion-collapse collapse" aria-labelledby="employment_data_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
               <div class="row mb-7">
					<div class="col-md-6">
						<label class="required fw-semibold fs-6 mb-2">Employer's Name</label>
						<input 
						v-model="form.employer_name"
						:disabled="isLoading || !editMode"
						:class="{'is-invalid': errors?.employer_name}"
						placeholder="Employer's Name"
						class="form-control mb-3 mb-lg-0" name="employer_name" />
						<div 
						:v-if="errors?.employer_name"
						class="invalid-feedback">{{ errors?.employer_name?.[0] }}</div>            
					</div>
					
					<div class="col-md-6">
						<phone-number
						:value="form.employer_phone"
						label="Employer's Phone Number"
						:disabled="isLoading || !editMode"
						:errors="errors?.employer_phone ? true: false"
						:error-message="errors?.employer_phone?.[0]"
						length="14"
						placeholder="Employer's Phone Number"
						@update:value="newValue => form.employer_phone = newValue"
						></phone-number>
					</div>
				</div>
				<div class="row mb-7">
					<div class="col-md-6">
						<money-field
						:value="form.income"
						label="Income"
						:disabled="isLoading || !editMode"
						:errors="errors?.income ? true: false"
						:error-message="errors?.income?.[0]"
						append="Yearly"
						placeholder="Income"
						@update:value="newValue => form.income = newValue"
						></money-field>
					</div>
				</div>
            </div>
        </div>
    </div>
	
	<div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="family_and_enrollment_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#family_and_enrollment_body" aria-expanded="false" aria-controls="family_and_enrollment_body">
            Family & Enrollment
            </button>
        </h2>
        <div id="family_and_enrollment_body" class="accordion-collapse collapse" aria-labelledby="family_and_enrollment_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
				<div class="row">
					<div v-for="toggle in toggles" class="col-md-6 mb-7">
						<div class="col-lg-8 d-flex align-items-center">
							<div class="form-check form-check-solid form-check-custom form-switch fv-row">
								<input 
								v-model="form[toggle.model]"
								:disabled="isLoading || !editMode"
								class="form-check-input w-45px h-30px" 
								:checked="form[toggle.model] ? true: false"
								type="checkbox" :id="toggle.model" :name="toggle.model" :value="true" />
								<label class="form-check-label fw-semibold text-dark" :for="toggle.model">{{ toggle.label }}</label>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>

    <div 
	v-if="form.married"
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="spousal_data_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#spousal_data_body" aria-expanded="false" aria-controls="spousal_data_body">
            Spousal Data
            </button>
        </h2>
        <div id="spousal_data_body" class="accordion-collapse collapse" aria-labelledby="spousal_data_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
               <spousal
			   	v-if="form.id"
                :form-data="form"
                :edit-mode="editMode"
                :show-next="false"
                :default-errors="errors"
                :enable-watcher="true"
				:enable-next-tick="false"
                @inputTrigger="saveInput"                
                >
				</spousal>
            </div>
        </div>
    </div>
	
    <div 
	v-if="form.dependents"
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="dependents_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dependents_body" aria-expanded="false" aria-controls="dependents_body">
            Dependents
            </button>
        </h2>
        <div id="dependents_body" class="accordion-collapse collapse" aria-labelledby="dependents_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
            	<dependents
				:form-data="form"
				:edit-mode="editMode"
				>
				</dependents>
            </div>
        </div>
    </div>		

	<div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="plan_information_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#plan_information_body" aria-expanded="false" aria-controls="plan_information_body">
            Plan Information
            </button>
        </h2>
        <div id="plan_information_body" class="accordion-collapse collapse" aria-labelledby="plan_information_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
               	<div class="row mb-7">
					<div class="col-md-4">
						<money-field
						:value="form.tax_credit_amount"
						:field-required="false"
						label="Tax Credit Amount"
						:disabled="isLoading"
						:errors="errors?.tax_credit_amount ? true: false"
						:error-message="errors?.tax_credit_amount?.[0]"
						placeholder="Tax Credit Amount"
						@update:value="newValue => form.tax_credit_amount = newValue"
						></money-field>
					</div>
					
					<div class="col-md-4">
						<label class="fw-semibold fs-6 mb-2">Plan Premium</label>
						<input 
						v-model="form.plan_premium"
						name="plan_premium"
						:disabled="isLoading"
						class="form-control mb-3 mb-lg-0"
						:class="{'is-invalid': errors?.plan_premium}"  
						placeholder="Plan Premium"/>
						<div 
						:v-if="errors?.plan_premium"
						class="invalid-feedback">{{ errors?.plan_premium?.[0] }}</div>
					</div>

					<div class="col-md-4">
						<money-field
						:value="form.you_pay"
						:field-required="false"
						label="You Pay"
						:disabled="isLoading"
						:errors="errors?.you_pay ? true: false"
						:error-message="errors?.you_pay?.[0]"
						placeholder="You Pay"
						@update:value="newValue => form.you_pay = newValue"
						></money-field>
					</div>
				</div>
				<div class="row mb-7">
					<div class="col-md-6">
						<label class="fw-semibold fs-6 mb-2">Plan Name</label>
						<input 
						v-model="form.plan_name"
						name="plan_name"
						:disabled="isLoading"
						class="form-control mb-3 mb-lg-0"
						:class="{'is-invalid': errors?.plan_name}"  
						placeholder="Plan Name"/>
						<div 
						:v-if="errors?.plan_name"
						class="invalid-feedback">{{ errors?.plan_name?.[0] }}</div>
					</div>
					<div class="col-md-6">
						<label class="fw-semibold fs-6 mb-2">Plan ID</label>
						<input 
						v-model="form.plan_id"
						name="plan_id"
						:disabled="isLoading"
						class="form-control mb-3 mb-lg-0"
						:class="{'is-invalid': errors?.plan_id}"  
						placeholder="Plan ID"
						/>
						<div 
						:v-if="errors?.plan_id"
						class="invalid-feedback">{{ errors?.plan_id?.[0] }}</div>
					</div>
				</div>
				<div class="row mb-7">
					<div class="col-md-12">
						<label class="fw-semibold fs-6 mb-2">Insurance Company</label>
						<input 
						v-model="form.insurance"
						name="insurance"
						:disabled="isLoading"
						class="form-control mb-3 mb-lg-0"
						:class="{'is-invalid': errors?.insurance}"  
						placeholder="Insurance Company"/>
						<div 
						:v-if="errors?.insurance"
						class="invalid-feedback">{{ errors?.insurance?.[0] }}</div>
					</div>
				</div>
            </div>
        </div>
    </div>

	<div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="finalize_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#finalize_body" aria-expanded="false" aria-controls="finalize_body">
            Finalize
            </button>
        </h2>
        <div id="finalize_body" class="accordion-collapse collapse" aria-labelledby="finalize_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
                <form-finalize
				v-if="form.id"
                :form-data="form"
                :template="template"
                :additional-files-submit-url="additionalFilesUrl"
                :save-signature-url="saveSignatureUrl"
                :submitUrl="submitUrl"
                :edit-mode="editMode"
                :coordinates="coordinates"
                :enable-watcher="enableWatcher"
                :next-button="false"
                @saveInput="saveInput"
				@updateForm="updateForm($event)"
                />
            </div>
        </div>
    </div>

	<div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="attachments_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#attachments_body" aria-expanded="false" aria-controls="attachments_body">
            Attachments
            </button>
        </h2>
        <div id="attachments_body" class="accordion-collapse collapse" aria-labelledby="attachments_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
               <attachments 
				 v-if="form.id"
			   :form-data="form"/>
            </div>
        </div>
    </div>

	<div
	:class="{'light': currentMode == 'light', 'dark': currentMode == 'dark'}"
    class="accordion-item accordion-custom-border">
        <h2 class="accordion-header" id="interactions_header">
            <button class="accordion-button fs-4 fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#interactions_body" aria-expanded="false" aria-controls="interactions_body">
            Interactions
            </button>
        </h2>
        <div id="interactions_body" class="accordion-collapse collapse" aria-labelledby="interactions_header" data-bs-parent="#form_accordion">
            <div class="accordion-body">
               	<activity-logs-table
					v-if="form.id"
                    :fetchUrl="`/activity-logs/fetch-by-subject/${form.id}/Form`"
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
            @click="close()"
            class="btn btn-light me-3">Discard</button>
        </div>
        <div class="col text-right">
            <button
            :disabled="isLoading" 
            @click="saveChanges()"
            class="btn btn-primary">Save Changes</button>
        </div>
    </div>
	
</div>
<!--end::Accordion-->
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import BirthdateMixins from "Mixins/birthdate.js";
import StatesMixins from "Mixins/states.js";
import SSNField from "Components/inputs/SSNField";
import PhoneNumber from "Components/inputs/PhoneNumber";
import AutoCompleteAddress from "Components/inputs/AutoCompleteAddress";
import MoneyField from "Components/inputs/MoneyField";
import Spousal from "./forms/enrollment-data/Spousal";
import Dependents from "./forms/enrollment-data/Dependents";
import FormFinalize from "./forms/FormFinalize.vue";
import Attachments from "./forms/Attachments.vue";

export default {
	name: "form-accordion-view",
	
	props: {
		fetchUrl: {
            type: String,
            default: ""
        },

		fetchTemplateUrl: {
            type: String,
            default: ""
        },

        additionalFilesUrl: {
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
	},

	components: { 
    	SSNField,
		PhoneNumber,
		AutoCompleteAddress,
		MoneyField,
		Spousal,
		Dependents,
		FormFinalize,
		Attachments,
	},

	data: () => ({
		form: {
			client_first_name: "",
			client_last_name: "",
			client_dob: "",
			client_ssn: "",
			client_phone: "",

			client_sex: "",
            client_email: "",
            client_address_line_1: "",
            client_address_line_2: "",
            client_city: "",
            client_state: "",
            client_zip: "",

			employer_name: "",
            employer_phone: "",
            income: "",
            income_frequency: "Yearly",

			product_change: false,
            pregnant: false,
            married: false,
            smoker: false,
            dependents: false,
            american_indian_native: false,

			spousal_first_name: "",
            spousal_last_name: "",
            spousal_dob: "",
            spousal_ssn: "",
            spousal_employer_name: "",
            spousal_employer_phone: "",
            spousal_income: "",
            spousal_type: "",

			tax_credit_amount: "",
            plan_premium: "",
            you_pay: "",
            plan_name: "",
            plan_id: "",
            insurance: "",

			additional_notes: "",
		},

        toggles: [
            { label: "Product Change", model: "product_change", value: false },
            { label: "Pregnant", model: "pregnant", value: false },            
            { label: "Married", model: "married", value: false },
            { label: "Smoker", model: "smoker", value: false },
            { label: "Dependents", model: "dependents", value: false },
            { label: "American Indian/Alaskan Native", model: "american_indian_native", value: false },                        
        ],

		isLoading: true,
		currentMode: "light",
		editMode: true,

		template: ""
	}),

	mounted() {
		this.currentMode = KTThemeMode.getMode();
		this.fetch();
		this.fetchTemplate();
	},

	mixins: [ResponseMixins, FormatterMixins, StatesMixins],
	
	methods: {
		fetch() {
            this.isLoading = true;
            axios.get(this.fetchUrl)
                .then((res) => {
                    this.form = res.data.form;
					this.formatStrings();
                }).catch((err) => {
                    this.parseError(err, {});
                }).finally(() => {
                    this.isLoading = false;
                });
        },

		formatStrings() {
			this.form.client_phone = this.phoneFormat(this.form.client_phone);
			this.form.client_ssn = this.ssnFormat(this.form.client_ssn);
			
			this.form.employer_phone = this.phoneFormat(this.form.employer_phone);
			this.form.income = this.currencyFormat(this.form.income);

			this.form.spousal_ssn = this.ssnFormat(this.form.spousal_ssn);
			this.form.spousal_employer_phone = this.phoneFormat(this.form.spousal_employer_phone);
			this.form.spousal_income = this.currencyFormat(this.form.spousal_income);
			this.form.domestic_partner = this.form.domestic_partner === 1 || this.form.domestic_partner == "1" ? true : false;
			this.form.spouse = this.form.domestic_partner === 1 || this.form.domestic_partner == "1" ? true : false;			

			this.form.tax_credit_amount = this.currencyFormat(this.form.tax_credit_amount);
			this.form.you_pay = this.currencyFormat(this.form.you_pay);
		},

		fetchTemplate() {
            axios.get(this.fetchTemplateUrl)
                .then((res) => {
                    this.template = res.data.template;
                }).finally(() => {
                    this.isLoading = false;
                });
        },

		saveChanges() {
			this.resetErrors();
            const data = {
                client_first_name: this.form?.client_first_name,
                client_last_name: this.form?.client_last_name,
                client_dob: this.form?.client_dob,
                client_ssn: this.clean(this.form?.client_ssn),
                client_phone: this.clean(this.form?.client_phone),
                client_sex: this.form?.client_sex,
                client_email: this.form?.client_email,
                client_address_line_1: this.form?.client_address_line_1,
                client_address_line_2: this.form?.client_address_line_2,
                client_city: this.form?.client_city,
                client_state: this.form?.client_state,
                client_zip: this.form?.client_zip,
                employer_name: this.form?.employer_name,
                employer_phone: this.clean(this.form?.employer_phone),
                income: this.clean(this.form?.income?.toString()),
                income_frequency: "Yearly",
                
                plan_premium: this.form?.plan_premium,
                plan_name: this.form?.plan_name,
                plan_id: this.form?.plan_id,
                insurance: this.form?.insurance,
                tax_credit_amount: this.clean(this.form?.tax_credit_amount?.toString()),
                you_pay: this.clean(this.form?.you_pay?.toString()),

                product_change: this.form?.product_change,
                pregnant: this.form?.pregnant,
                married: this.form?.married,
                smoker: this.form?.smoker,
                dependents: this.form?.dependents,
                american_indian_native: this.form?.american_indian_native,     
                
                spousal_first_name: this.form?.spousal_first_name,
                spousal_last_name: this.form?.spousal_last_name,
                spousal_dob: this.form?.spousal_dob,
                spousal_employer_name: this.form?.spousal_employer_name,
                spousal_type: this.form?.spousal_type, 

                spousal_ssn: this.clean(this.form?.spousal_ssn),
                spousal_income: this.clean(this.form?.spousal_income?.toString()),
                spousal_employer_phone: this.clean(this.form?.spousal_employer_phone),

                additional_notes: this.form?.additional_notes,
            };
            this.isLoading = true;
            axios.post(`/agent-submission-forms/save-changes/${this.form.id}`, data)
                .then((res) => {
					if(res.data.address_type_error) {
						this.parseInfo(res.data.message);
					} else {
						this.parseSuccess("Form has been updated");
					}					
                }).catch((err) => {
                    this.parseError(err, {});
                }).finally(() => {
                    this.isLoading = false;
                });
        },

		setValue(value, model) {
            this.form[model] = value;
        },

		autocomplete(value) {
            this.form.client_city = value.city;
            this.form.client_state = value.state;            
            this.form.client_zip = value.zip;
			setTimeout(() => {
	            this.form.client_address_line_1 = value.shortAddress;
			}, 100);
        },

		updateForm(data) {
            if(data) {
				this.isLoading = true;
				setTimeout(() => {
					this.form = data;
					this.formatStrings();
					this.isLoading = false;
				}, 200);
            }
        },

        saveInput(data) {
			this.form = { ...this.form, ...data };
			this.formatStrings();
		},

		close() {
			this.$emit("close");
		}
	}
}
</script>