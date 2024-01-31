<template>
<form
ref="employmentInfo"
@submit.prevent="onSubmitEmploymentData($event)" class="form">
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
			:required="false"
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
	<div class="row">
		<div class="col text-left">
			<button 
			@click="back()"
			type="button"
			:disabled="isLoading"
			class="btn btn-light me-3">Back</button>
		</div>
		<div class="col text-right">
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
import GeolocationMixins from "Mixins/geolocation.js";
import PhoneNumber from "Components/inputs/PhoneNumber";
import MoneyField from "Components/inputs/MoneyField";

export default {
	name: "step-three",

	props: {
		
		employmentDataValidationUrl: {
			type: String,
            default: ""
		},

		formData: {
            type: Object,
            default: {}
        },

		editMode: {
			type: Boolean,
			default: true
		},
		
		formValues: {
			type: Object,
			default: {}
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

	components: { 
		PhoneNumber,
		MoneyField,
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
			employer_name: "",
            employer_phone: "",
            income: "",
            income_frequency: "Yearly",
        },

	}),

	mounted() {
		Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                if(key == "employer_phone") { 
                    this.form[key] = this.phoneFormat(this.formData[key]);				
				} else if (key == "income") {
					this.$nextTick(() => {
						this.form[key] = this.currencyFormat(this.formData[key]);
					});
				} else {
                    this.form[key] = this.formData[key]; 
                }
            }
        });
	},

	mixins: [ GeolocationMixins, ResponseMixins, FormatterMixins ],

	methods: {
		onSubmitEmploymentData(event) {
            this.resetErrors();
			let params = {
				income: this.clean(this.form.income),
				lat: this.coordinates.lat,
				long: this.coordinates.long,
				income_frequency: this.form.income_frequency,
				employer_name: this.form.employer_name,
				employer_phone: this.clean(this.form.employer_phone),
			};
			if (Object.keys(this.formValues).length) {
				params = { 
					...this.formValues,
					...params,
					client_phone: this.clean(this.formValues.client_phone),
				};

				if(!this.formValues.client_phone) {
					delete params.client_phone;
				}
			}
	
			this.isLoading = true;
            axios.post(this.employmentDataValidationUrl, params)
                .then((res) => {
                    if(res.data.redirect) {
                        window.location.href = res.data.redirect; 
                    } else {
                        this.$emit("completed", res.data.form);
                    }
                }).catch((error) => {
                    const displayError = error.response?.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

		back() {
			this.$emit("back");
		},

		proceed() {
			this.$emit("success");
		}
	}
}
</script>