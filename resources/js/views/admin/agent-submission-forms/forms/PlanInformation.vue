<template>
<!--begin::details View-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Plan Information</h3>
        </div>
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->

    <!--begin::Card body-->
    <div class="card-body p-9">
        <div 
        class="alert alert-warning d-flex align-items-center mb-10">
            <!--begin::Icon-->
            <span class="svg-icon svg-icon-warning svg-icon-2hx me-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor"/>
                <rect x="11" y="17" width="7" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor"/>
                <rect x="11" y="9" width="2" height="2" rx="1" transform="rotate(-90 11 9)" fill="currentColor"/>
                </svg>
            </span>
            <!--end::Icon-->

            <!--begin::Wrapper-->
            <div class="d-flex flex-column">
                <!--begin::Title-->
                <h4 class="mb-1 text-dark">Important Note</h4>
                <!--end::Title-->
                <!--begin::Content-->
                <span class="text-gray-900">You must be an agent to use this section.</span>
                <!--end::Content-->
            </div>
            <!--end::Wrapper-->
        </div>

        <form 
        ref="planInfo"
        @submit.prevent="onSubmit($event)" class="form">
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
            <div 
            v-if="editMode && !nextButton"
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
                    class="btn btn-primary">Confirm Plan Information</button>
                </div>
            </div>
        </form>
        <div 
        v-if="!editMode || nextButton"
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
import MoneyField from "Components/inputs/MoneyField";

export default {
    name: "plan-information",

    components: {
        MoneyField
    },

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

        enableWatcher: {
			type: Boolean,
            default: false
		},

        nextButton: {
			type: Boolean,
			default: false
		},        

    },

    watch: {
		form: {
			handler() {
				if(this.enableWatcher) {
					this.$emit("saveInput", this.form);
				}
			},
    		deep: true
		},
    },

    data: () => ({
        form: {
            tax_credit_amount: "",
            plan_premium: "",
            you_pay: "",
            plan_name: "",
            plan_id: "",
            insurance: "",
        },
        isLoading: false
    }),

    mounted() {

        const planInfo = this.$refs.planInfo.elements;
        planInfo.forEach((element) => {
            element.disabled = !this.editMode
        });
            
        Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                let value = this.formData[key];
                if(["tax_credit_amount", "you_pay"].indexOf(key) >= 0) {
                    value = this.currencyFormat(value); 
                }

                this.form[key] = value;
            }
        });
    },

    mixins: [ ResponseMixins, FormatterMixins ],

    methods: {

        onSubmit(event) {
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("tax_credit_amount", this.clean(this.form.tax_credit_amount));
            params.append("you_pay", this.clean(this.form.you_pay));
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
    }
}
</script>