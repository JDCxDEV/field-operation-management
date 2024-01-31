<template>
<!--begin::details View-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card body-->
    <div class="card-body p-9">
        <form 
        ref="employmentInfo"
        @submit.prevent="onSubmit($event)" class="form">
            <div class="row mb-7">
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Employer's Name</label>
                    <input 
                    v-model="form.employer_name"
                    :disabled="isLoading"
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
                    :disabled="isLoading"
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
                    :disabled="isLoading"
                    :errors="errors?.income ? true: false"
                    :error-message="errors?.income?.[0]"
                    placeholder="Income"
                    @update:value="newValue => form.income = newValue"
                    ></money-field>
                </div>
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Income Frequency</label>
                    <select
                    v-model="form.income_frequency"
                    :disabled="isLoading" 
                    name="income_frequency"
                    :class="{'is-invalid': errors?.income_frequency}"
                    class="form-select fw-bold mb-3 mb-lg-0">
                        <option selected disabled value="">Please select Income Frequency</option>
                        <option value="Monthly">Monthly</option>
                        <option value="Quarterly">Quarterly</option>
                        <option value="Yearly">Yearly</option>
                    </select>
                    <div 
                    :v-if="errors?.income_frequency"
                    class="invalid-feedback">{{ errors?.income_frequency?.[0] }}</div>     
                </div>
            </div>
            <div 
            v-if="editMode"
            class="row pt-15">
                <div class="col text-left">
                    <button 
                    @click="back()"
                    :disabled="isLoading"
                    class="btn btn-light me-3">Back</button>
                </div>
                <div class="col text-right">
                    <button
                    type="submit"
                    :disabled="isLoading"
                    class="btn btn-primary">Confirm Employment Information</button>
                </div>
            </div>
        </form>
        <div 
        v-if="!editMode"
        class="row pt-15">
            <div class="col text-left">
                <button 
                @click="back()"
                type="button"
                class="btn btn-light me-3">Back</button>
            </div>
            <div class="col text-right">
                <button
                @click="proceed()"
                class="btn btn-primary">Next</button>
            </div>
        </div>
    </div>
    <!--end::Card body-->
</div>
<!--end::details View-->
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import PhoneNumber from "Components/inputs/PhoneNumber";
import MoneyField from "Components/inputs/MoneyField";

export default {
    name: "employment-data",
    
    props: {
        submitUrl: {
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
    },

    components: {
        PhoneNumber,
        MoneyField
    },

    data: () => ({
        form: {
            employer_name: "",
            employer_phone: "",
            income: "",
            income_frequency: "",
        },
        isLoading: false
    }),

    mounted() {
        
        const employmentInfo = this.$refs.employmentInfo.elements;
        employmentInfo.forEach((element) => {
            element.disabled = !this.editMode
        });

        Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                let value = this.formData[key];
                if(key == "income") {
                    value = this.currencyFormat(value); 
                } else if(key == "employer_phone")  {
                    value = this.phoneFormat(value);
                }

                this.form[key] = value;
            }
        });
    },

    mixins: [ ResponseMixins, FormatterMixins ],

    methods: {
        onSubmit(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("employer_phone", this.clean(this.form.employer_phone));
            params.append("income", this.clean(this.form.income));            
            this.isLoading = true;
            axios.post(this.submitUrl, params)
                .then((res) => {
                    this.proceed(res.data.form);
                }).catch((error) => {
                    const displayError = error.response.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

        proceed(data) {
            this.$emit("proceed", data);            
        },

        back() {
            this.$emit("back");
        },

        inputContactNumber() {
            const value = this.form.employer_phone;
            const formattedValue = this.phoneFormat(value);
            this.form.employer_phone = formattedValue;
        },
    },
}
</script>