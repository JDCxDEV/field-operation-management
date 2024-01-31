<template>
<form 
ref="spousalDataForm"
@submit.prevent="onSubmitSpousalData($event)"
class="form">
	<div class="row">
		<div class="col-md-6 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Spousal Firstname</label>
			<input
			@input="spousal.spousal_first_name = alphabetStringOnly($event)"
			v-model="spousal.spousal_first_name"
			:disabled="isLoading || !editMode"
			:class="{'is-invalid': errors?.spousal_first_name}"
			placeholder="Spousal Firstname"
			class="form-control" name="spousal_first_name" />
			<div 
			:v-if="errors?.spousal_first_name"
			class="invalid-feedback">{{ errors?.spousal_first_name?.[0] }}</div>            
		</div>

		<div class="col-md-6 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Spousal Lastname</label>
			<input
			@input="spousal.spousal_last_name = alphabetStringOnly($event)"
			v-model="spousal.spousal_last_name"
			:disabled="isLoading || !editMode"
			:class="{'is-invalid': errors?.spousal_last_name}"
			placeholder="Spousal Lastname"
			class="form-control" name="spousal_last_name" />
			<div 
			:v-if="errors?.spousal_last_name"
			class="invalid-feedback">{{ errors?.spousal_last_name?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Spousal Date of Birth</label>
			<input 
			v-model="spousal.spousal_dob"
			:disabled="isLoading || !editMode"
			:class="{'is-invalid': errors?.spousal_dob}"
			type="date"
			placeholder="Spousal Date of Birth"
			class="form-control" 
			name="spousal_dob"
			:min="min"
			:max="max"                    
			/>
			<div 
			:v-if="errors?.spousal_dob"
			class="invalid-feedback">{{ errors?.spousal_dob?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<SSNField
			:value="spousal.spousal_ssn"
			label="Spousal SSN"
			name="spousal_ssn"
			length="11"
			input-class="form-control mb-3 mb-lg-0"
			:errors="errors?.spousal_ssn"
			:error-message="errors?.spousal_ssn?.[0]"
			:disabled="isLoading || !editMode"
			:required="false"
			@update:value="newValue => spousal.spousal_ssn = newValue"
			></SSNField>
		</div>

		<div class="col-md-4 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Spousal Relationship type</label>
			<select
			:disabled="isLoading || !editMode"
			name="spousal_type"
			v-model="spousal.spousal_type"
			:class="{'is-invalid': errors?.spousal_type}"
			class="form-select fw-bold mb-3 mb-lg-0">
				<option selected disabled value="">Select Relation type</option>
				<option value="Spouse">Spouse</option>
				<option value="Domestic Partner">Domestic Partner</option>
			</select>
			<div 
			:v-if="errors?.spousal_type"
			class="invalid-feedback">{{ errors?.spousal_type?.[0] }}</div> 
		</div>

		<div class="col-md-4 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Spousal Employer's Name</label>
			<input
			@input="spousal.spousal_employer_name = alphabetStringOnly($event)"
			v-model="spousal.spousal_employer_name"
			:disabled="isLoading || !editMode"
			:class="{'is-invalid': errors?.spousal_employer_name}"
			placeholder="Spousal Employer's Name"
			class="form-control" name="spousal_employer_name" />
			<div 
			:v-if="errors?.spousal_employer_name"
			class="invalid-feedback">{{ errors?.spousal_employer_name?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<phone-number
			:value="spousal.spousal_employer_phone"
			label="Spousal Employer's Phone Number"
			:disabled="isLoading || !editMode"
			:errors="errors?.spousal_employer_phone ? true: false"
			:error-message="errors?.spousal_employer_phone?.[0]"
			:required="false"
			length="14"
			placeholder="Spousal Employer's Phone Number"
			@update:value="newValue => spousal.spousal_employer_phone = newValue"
			></phone-number>
		</div>

		<div class="col-md-4 mb-7">
			<money-field
			:value="spousal.spousal_income"
			label="Spousal Income"
			:disabled="isLoading || !editMode"
			:errors="errors?.spousal_income ? true: false"
			:error-message="errors?.spousal_income?.[0]"
			placeholder="Spousal Income"
			@update:value="newValue => spousal.spousal_income = newValue"
			></money-field>
		</div>
	</div>

	<div
	v-if="editMode && toggleForm.dependents"
	class="row pt-15">
		<div 
		v-show="showNext"
		class="col text-right">
			<button
			id="submitBtn"
			type="submit"
			:disabled="isLoading"
			class="btn btn-primary">Next</button>
		</div>
	</div> 
</form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import BirthdateMixins from "Mixins/birthdate.js";
import PhoneNumber from "Components/inputs/PhoneNumber";
import SSNField from "Components/inputs/SSNField";
import MoneyField from "Components/inputs/MoneyField";

export default {
	name: "spousal",

	props: {

        submitEnrollmentInformationUrl: {
            type: String,
            default: ""
        },		

        formData: {
            type: Object,
            default: {}
        },

		toggleForm: {
            type: Object,
            default: {}
        },

        editMode: {
            type: Boolean,
            default: true
        },
		
		showNext: {
			type: Boolean,
            default: true
		},

		defaultErrors: {
			type: Object,
            default: {}
		},

		enableWatcher: {
			type: Boolean,
            default: false
		},
		enableNextTick: {
			type: Boolean,
			default: true
		}
	},

	components: {
        PhoneNumber,
        SSNField,
        MoneyField
	},

	watch: {
		spousal: {
			handler() {
				if(this.enableWatcher) {
					this.$emit("inputTrigger", this.spousal);
				}
			},
    		deep: true
		},

		defaultErrors: {
      		handler(newValue, oldValue) {
				this.errors = newValue;
			},
    		deep: true
		}
    },

	data: () => ({

		isLoading: false,

        spousal: {
            spousal_first_name: "",
            spousal_last_name: "",
            spousal_dob: "",
            spousal_ssn: "",
            spousal_employer_name: "",
            spousal_employer_phone: "",
            spousal_income: "",
            spousal_type: "", 
        },

	}),

	mounted() {
		Object.entries(this.spousal).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                switch(key) {
                    case "spousal_ssn":
						if(this.enableNextTick) {
							this.$nextTick(() => {
								this.spousal[key] = this.ssnFormat(this.formData[key]);
							});
						} else {
							this.spousal[key] = this.ssnFormat(this.formData[key]);													
						}

                        break;
                    case "spousal_employer_phone":
						if(this.enableNextTick) {
							this.$nextTick(() => {
								this.spousal[key] = this.phoneFormat(this.formData[key]);
							});
						} else {
							this.spousal[key] = this.phoneFormat(this.formData[key]);												
						}
                        break;
                    case "spousal_income":
						if(this.enableNextTick) {
							this.$nextTick(() => {
								this.spousal[key] = this.currencyFormat(this.formData[key]);
							});
						} else {
							this.spousal[key] = this.currencyFormat(this.formData[key]);												
						}
                        break;
                    case "spouse":
                    case "domestic_partner":
                        this.spousal[key] = this.formData[key] === 1 || this.formData[key] == "1" ? true : false; 
                        break;
                    default:
                        this.spousal[key] = this.formData[key]; 
                        break;
                }
            }
        });
	},

    mixins: [ ResponseMixins, FormatterMixins, BirthdateMixins ],

	methods: {
		onSubmitSpousalData(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            Object.entries(this.toggleForm).forEach((e) => {
                params.append(e[0], e[1] == true ? 1 : 0);
            });
            params.append("spousal_ssn", this.clean(this.spousal.spousal_ssn));
            params.append("spousal_income", this.clean(this.spousal.spousal_income));
            params.append("spousal_employer_phone", this.clean(this.spousal.spousal_employer_phone));
                
            this.isLoading = true;
            axios.post(`${this.submitEnrollmentInformationUrl}?store_spousal=true`, params)
                .then((res) => {
                    if(this.toggleForm.dependents) {
                        this.$emit("success");
                    } else {
						this.$emit("proceed", res.data.form);
                    }
                }).catch((error) => {
                    const displayError = error.response.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

		onClickSubmit() {
			document.getElementById("submitBtn").click();
		}
	}
}
</script>