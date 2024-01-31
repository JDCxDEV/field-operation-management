<template>
<form 
ref="clientValidationForm"
@submit.prevent="onSubmitClientValidation($event)" class="form">
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

	<div 
	class="row">
		<div class="offset-md-6 col-md-6 text-right">
			<button
			v-if="editMode && !nextButton"
			type="submit"
			:disabled="isLoading"
			class="btn btn-primary">Next</button>

			<button
			v-if="!editMode || nextButton"
			@click="proceed()"
			type="button"
			class="btn btn-primary">Next</button>
		</div>
	</div>
</form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import BirthdateMixins from "Mixins/birthdate.js";
import SSNField from "Components/inputs/SSNField";
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
	name: "step-one",

	components: { 
        SSNField,
		PhoneNumber
	},

	props: {
		clientValidationUrl: {
			type: String,
			default: "",
		},

		formData: {
            type: Object,
            default: {}
        },

		editMode: {
			type: Boolean,
			default: true
		},

		enableWatcher: {
			type: Boolean,
			default: false			
		},

		nextButton: {
			type: Boolean,
			default: false
		}
	},

	watch: {
		form: {
			handler() {
				if(this.enableWatcher) {
					this.$emit("inputTrigger", this.form)
				}
			},
    		deep: true
		}
	},

	data: () => ({
		isLoading: false,
		form: {
            client_first_name: "",
            client_last_name: "",
            client_dob: "",
            client_ssn: "",
			client_phone: "",
        },
	}),

	mounted() {
		Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                if(key == "client_ssn") {
                    this.form[key] = this.ssnFormat(this.formData[key]);
				} else if (key == "client_phone") {
                    this.form[key] = this.phoneFormat(this.formData[key]);					
				} else {
                    this.form[key] = this.formData[key]; 
                }
            }
        });
	},

	mixins: [ ResponseMixins, FormatterMixins, BirthdateMixins ],
	
	methods: {
		onSubmitClientValidation(event) {
			this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("client_ssn", this.clean(this.form.client_ssn));
            params.append("client_phone", this.clean(this.form.client_phone));
			this.isLoading = true;
            axios.post(this.clientValidationUrl, params)
                .then((res) => {
                    this.$emit("success", this.form);
                }).catch((error) => {
                    const displayError = error.response?.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
					console.log(error);
                }).finally(() => {
                    this.isLoading = false;          
                });  
        },

		proceed() {
			this.$emit("success", this.form);
		}
	}

}
</script>