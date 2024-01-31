<template>
<div class="row">
	<div class="col-md-12 mb-3">
		<div class="form-group">
			<div class="form-check form-check-custom form-check-solid">
				<label style="margin-right: 10px">Do your dependent needs coverage?</label>
				<input 
				v-model="dependent.dependent_covered"
				class="form-check-input" type="radio" value="1" id="coverageYes"/>
				<label class="form-check-label fw-semibold text-dark mx-1" for="coverageYes">
					Yes
				</label>

				<input
				v-model="dependent.dependent_covered"
				class="form-check-input" type="radio" value="0" id="coverageNo"/>
				<label class="form-check-label fw-semibold text-dark mx-1" for="coverageNo">
					No
				</label>
			</div>
		</div>
	</div>

	<div 
	v-show="dependent.dependent_covered == '0'"
	class="col-md-6 mb-3">
		<div class="form-inline">
			<label style="margin-right: 10px">Select what coverage they have</label>
			<select
			v-model="dependent.dependent_coverage_type"
			name="dependent_coverage_type"
			class="form-control-sm">
				<option selected disabled :value="null">Select Coverage</option>
				<option v-for="type in coverageTypes" :value="type.value">{{ type.label }}</option>
			</select>
		</div>
	</div>

	<div 
	v-if="!addingDependent && canShowAddDependentBtn"
	class="col-md-12 mb-7">
		<div class="text-right">
			<button 
			@click="addDependent()"
			class="btn btn-info">Add Dependent</button>
		</div>
	</div>

	<div
	v-if="!addingDependent"
	class="table-responsive">
		<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
			<thead>
				<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
					<th class="min-w-125px">Name</th>
					<th class="min-w-125px">Actions</th>
				</tr>
			</thead>
			<tbody class="text-gray-600 fw-semibold">
				<tr v-for="dependent in dependents">
					<td>
						{{ dependent.dependent_first_name }} {{ dependent.dependent_last_name }}
					</td>
					<td>
						<button 
						@click="updateDependent(dependent)"
						type="button" class="btn btn-primary btn-sm me-3">Update</button>
						<button 
						@click="deleteDependent(dependent)"
						type="button" class="btn btn-danger btn-sm">Delete</button>
					</td>
				</tr>
				<tr v-if="!dependents.length" class="odd my-10">
					<td valign="top" colspan="2" class="text-center">No matching records found</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div v-if="addingDependent" class="row">
		<div 
		class="col-md-6 mb-7">
			<label class="required fw-semibold fs-6 mb-2">First Name</label>
			<input
			@input="dependent.dependent_first_name = alphabetStringOnly($event)"
			v-model="dependent.dependent_first_name"
			:disabled="isLoading"
			:class="{'is-invalid': errors?.dependent_first_name}"
			placeholder="First Name"
			class="form-control"/>
			<div 
			:v-if="errors?.dependent_first_name"
			class="invalid-feedback">{{ errors?.dependent_first_name?.[0] }}</div>            
		</div>

		<div class="col-md-6 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Last Name</label>
			<input
			@input="dependent.dependent_last_name = alphabetStringOnly($event)"
			v-model="dependent.dependent_last_name"
			:disabled="isLoading"
			:class="{'is-invalid': errors?.dependent_last_name}"
			placeholder="Last Name"
			class="form-control"/>
			<div 
			:v-if="errors?.dependent_last_name"
			class="invalid-feedback">{{ errors?.dependent_last_name?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Gender</label>
			<select
			:disabled="isLoading" 
			v-model="dependent.dependent_sex"
			:class="{'is-invalid': errors?.dependent_sex}"
			class="form-select fw-bold mb-3 mb-lg-0">
				<option selected disabled value="">Select Gender</option>
				<option value="Female">Female</option>
				<option value="Male">Male</option>
			</select>
			<div 
			:v-if="errors?.dependent_sex"
			class="invalid-feedback">{{ errors?.dependent_sex?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<label class="required fw-semibold fs-6 mb-2">Date of Birth</label>
			<input
			v-model="dependent.dependent_dob"
			:disabled="isLoading"
			:class="{'is-invalid': errors?.dependent_dob}"
			placeholder="Date of Birth"
			type="date"
			class="form-control"
			:max="current"
			/>
			<div 
			:v-if="errors?.dependent_dob"
			class="invalid-feedback">{{ errors?.dependent_dob?.[0] }}</div>            
		</div>

		<div class="col-md-4 mb-7">
			<SSNField
			:value="dependent.dependent_ssn"
			label="Dependent SSN"
			name="dependent_ssn"
			length="11"
			input-class="form-control mb-3 mb-lg-0"
			:errors="errors?.dependent_ssn"
			:error-message="errors?.dependent_ssn?.[0]"
			:disabled="isLoading || !editMode"
			@update:value="newValue => dependent.dependent_ssn = newValue"
			></SSNField>
		</div>		

		<div class="col-md-12">
			<div class="text-right">
				<button 
				@click="clearDependentFields(); this.addingDependent = false;"
				type="button" class="btn btn-light me-3">Discard</button>
				<button 
				@click="saveFormDependent(true)"
				type="button"
				class="btn btn-primary me-3">Save & Close</button>

				<button 
				@click="saveFormDependent(false)"
				type="button"
				class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import BirthdateMixins from "Mixins/birthdate.js";
import SSNField from "Components/inputs/SSNField";

export default {
	name: "dependents",

	props: {
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
	},

	components: {
		SSNField
	},

	computed: {
        canShowAddDependentBtn() {
            if(this.dependent.dependent_covered == "0" && this.dependent.dependent_coverage_type) {
                return true;
            }

            if(this.dependent.dependent_covered == "1") {
                return true;
            }
            
            return false;
        }
    },

	data: () => ({
		dependent: {
            dependent_first_name: "",
            dependent_last_name: "",
            dependent_sex: "",
            dependent_dob: "",
            dependent_ssn: "",
            dependent_covered: "",
            dependent_coverage_type: null
        },

		isLoading: false,

        dependents: [],
        selectedDependent: "",
        addingDependent: false,

        coverageTypes: [
            { label: "Through Employment", value: "employment" },
            { label: "Through Spouse", value: "spouse" },
            { label: "Through Medicaid", value: "medicaid" },
        ],		
	}),

	mounted() {
        this.dependents = this.formData.dependents_list.map((i) => {
            return { ...i, dependent_ssn:  this.ssnFormat(i.dependent_ssn) };
        });
	},

	mixins: [ ResponseMixins, FormatterMixins, BirthdateMixins ],

	methods: {
		saveFormDependent(close = false) {
            this.resetErrors();
            const data = {
                ...this.dependent,
                dependent_ssn: this.clean(this.dependent.dependent_ssn),
                form_id: this.formData.id
            };
            this.isLoading = true;
            const url = this.selectedDependent ? `/agent-submission-forms/update-dependent/${this.selectedDependent.id}` : `/agent-submission-forms/update-dependent`;
            axios.post(url, data)
                .then((res) => {
					if(close) {
                        this.addingDependent = false;
						this.clearDependentFields();
                    }
					if(!this.selectedDependent) {
						this.clearDependentFields();
	                    this.dependents = res.data.form.dependents_list;
					}
                }).catch((error) => {
                    const displayError = error.response?.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        deleteDependent(dependent) {
            Swal.fire({
                title: "Delete Depedent",
                html: "Are you sure to delete the selected dependent?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Confirm"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isLoading = true;
                    axios.get(`/agent-submission-forms/delete-dependent/${dependent.id}`)
                        .then((res) => {
                            this.dependents = res.data.form.dependents_list;
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });
        },

        clearDependentFields() {
            this.dependent = {
                dependent_first_name: "",
                dependent_last_name: "",
                dependent_sex: "",
                dependent_dob: "",
                dependent_covered: "",
                dependent_coverage_type: null 
            };
        },


		updateDependent(dependent) {
            this.resetErrors();
            this.addingDependent = true;
            this.$nextTick(() => {
                this.dependent = dependent;
                this.selectedDependent = dependent;
            });
        },

        addDependent() {
            this.selectedDependent = "";
            this.resetErrors();
            this.addingDependent = true;
        },
	}
}
</script>