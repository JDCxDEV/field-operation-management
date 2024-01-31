<template>
<!--begin::details View-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card body-->
    <div class="card-body p-9">
        <form 
        ref="paymentInfo"
        @submit.prevent="onSubmit($event)" class="form">
            <div class="row mb-7">
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Plan Selected</label>
                    <input 
                    v-model="form.plan_selected"
                    name="plan_selected"
                    :disabled="isLoading"
                    class="form-control mb-3 mb-lg-0"
                    :class="{'is-invalid': errors?.plan_selected}"  
                    placeholder="Plan Selected"/>
                    <div 
                    :v-if="errors?.plan_selected"
                    class="invalid-feedback">{{ errors?.plan_selected?.[0] }}</div>
                </div>

                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Date Processed</label>
                    <input
                    v-model="form.date_processed"
                    name="date_processed"
                    :disabled="isLoading"
                    :class="{'is-invalid': errors?.date_processed}"
                    placeholder="Date Processed"
                    class="form-control mb-3 mb-lg-0" type="date"/>
                    <div 
                    :v-if="errors?.date_processed"
                    class="invalid-feedback">{{ errors?.date_processed?.[0] }}</div>            
                </div>
            </div>
            <div class="row mb-7">
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">Credit Card</label>
                    <input
                    @input="inputCC()" 
                    v-model="form.cc_number"
                    name="cc_number"
                    :disabled="isLoading"
                    class="form-control mb-3 mb-lg-0"
                    :class="{'is-invalid': errors?.cc_number}"  
                    placeholder="Credit Card"/>
                    <div 
                    :v-if="errors?.cc_number"
                    class="invalid-feedback">{{ errors?.cc_number?.[0] }}</div>
                </div>
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">Expiration Date</label>
                    <input 
                    v-model="form.cc_expiration_date"
                    name="cc_expiration_date"
                    :disabled="isLoading"
                    class="form-control mb-3 mb-lg-0"
                    :class="{'is-invalid': errors?.cc_expiration_date}"  
                    placeholder="Expiration Date" type="date"/>
                    <div 
                    :v-if="errors?.cc_expiration_date"
                    class="invalid-feedback">{{ errors?.cc_expiration_date?.[0] }}</div>
                </div>
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">CVC</label>
                    <input 
                    v-model="form.cc_cvc"
                    name="cc_cvc"
                    :disabled="isLoading"
                    class="form-control mb-3 mb-lg-0"
                    :class="{'is-invalid': errors?.cc_cvc}"  
                    placeholder="CVC"
                    />
                    <div 
                    :v-if="errors?.cc_cvc"
                    class="invalid-feedback">{{ errors?.cc_cvc?.[0] }}</div>
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
                    class="btn btn-primary">Confirm Payment Information</button>
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
export default {
    name: "payment-information",

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
        }
    },

    data: () => ({
        form: {
            plan_selected: "",
            date_processed: "",
            cc_number: "",
            cc_expiration_date: "",
            cc_cvc: "",
        },
        isLoading: false
    }),

    mounted() {

        const paymentInfo = this.$refs.paymentInfo.elements;
        paymentInfo.forEach((element) => {
            element.disabled = !this.editMode
        });

        Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                let value = this.formData[key];
                if(["cc_number"].indexOf(key) >= 0) {
                    value = this.ccFormat(value); 
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
            params.append("cc_number", this.clean(this.form.cc_number));
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

        inputCC() {
            const value = this.form.cc_number;
            const formattedValue = this.ccFormat(value);
            this.form.cc_number = formattedValue;
        }
    }
}
</script>