<template>
<form 
ref="clientAddress"
@submit.prevent="onSubmitClientAddress($event)" class="form mb-7">
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
			ref="submitBtn" 
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
import StatesMixins from "Mixins/states.js";
import AutoCompleteAddress from "Components/inputs/AutoCompleteAddress";
import PhoneNumber from "Components/inputs/PhoneNumber";
export default {
	name: "step-two",
	
	props: {
		clientAddressValidationUrl: {
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

	components: { 
		AutoCompleteAddress,
        PhoneNumber,
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
			client_sex: "",
            client_email: "",
            client_address_line_1: "",
            client_address_line_2: "",
            client_city: "",
            client_state: "",
            client_zip: "",
        },

		ignore_warning: false,
	}),

	mounted() {
		Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                this.form[key] = this.formData[key]; 
            }
        });
	},

	mixins: [ ResponseMixins, FormatterMixins, StatesMixins ],

	methods: {
		onSubmitClientAddress(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            if(this.ignore_warning) {
                params.append("ignore_warning", true);
            }         
            this.isLoading = true;
            axios.post(this.clientAddressValidationUrl, params)
                .then((res) => {
                    if(res.data.status === 422) {
						if(res.data.address_type_error) {
							this.parseInfo(res.data.message);
						} else {
							if(res.data.percent_match === 100) {
								this.disableSubmission();
								return;
							} else {
								this.runWarning();
							}
						}
                    }
                    if(res.data.status === 200) {
                        this.ignore_warning = false;
                        this.$emit("success", this.form);
                    }
                }).catch((error) => {
                    const displayError = error.response?.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
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

        disableSubmission() {
            Swal.fire("Client Address Blacklisted", "Client address is in the blacklisted address.", "warning");
        },		

		runWarning() {
            Swal.fire({
                title: "Blacklisted Address Detected",
                html: "Client address might be blacklisted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Ignore Warning"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.ignore_warning = true;
					this.$refs.submitBtn.click();
                }
            });
        },

		proceed() {
			this.$emit("success", this.form);
		},

		back() {
			this.$emit("back");
		}
	}
}
</script>