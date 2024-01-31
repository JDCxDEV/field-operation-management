<template>
<!--begin::Forgot Password Form-->
<form 
@submit.prevent="onSubmit($event)" class="form w-100" novalidate="novalidate">
    <!--begin::Heading-->
    <div class="text-center mb-10">
        <!--begin::Title-->
        <h1 class="text-dark fw-bolder mb-3">Forgot Password ?</h1>
        <!--end::Title-->
        <!--begin::Link-->
        <div class="text-gray-500 fw-semibold fs-6">Enter your email to reset your password.</div>
        <!--end::Link-->
    </div>
    <!--begin::Heading-->
    <!--begin::Input group=-->
    <div class="fv-row mb-8 fv-plugins-icon-container">
        <!--begin::Email-->
        <input 
        v-model="email"
        :class="{'is-invalid': errors?.email}"        
        type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent">
        <!--end::Email-->
        <div 
        :v-if="errors?.email"
        class="invalid-feedback">{{ errors?.email?.[0] }}</div>        
    </div>
    <!--begin::Actions-->
    <div class="d-flex flex-wrap justify-content-center pb-lg-0">
        <button 
        :disabled="isLoading"
        class="btn btn-primary me-4">
            Submit
        </button>
        <a href="/login" class="btn btn-light">Cancel</a>
    </div>
    <!--end::Actions-->
    <div></div>
</form>
<!--end::Forgot Password Form-->
</template>
<script>
import ResponseMixins from "Mixins/response.js";
export default {
    name: "forgot-password",
    
    props: {
        submitUrl: {
            type: String,
            default: ""
        }
    },

    data: () => ({
        isLoading: false,
        email: ""
    }),

    mixins: [ ResponseMixins ],

    methods: {
        onSubmit(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);

            this.isLoading = true;
            axios.post(this.submitUrl, params)
                .then((res) => {
                    if(res.data.status == "passwords.sent") {
                        Swal.fire(`Password Reset`, `Password Reset link has been sent. Please check your mail inbox.`, "success");
                        this.email = "";
                    } else if (res.data.status == "passwords.user") {
                        Swal.fire(`Password Reset`, `No user found with the email provided.`, "warning");                        
                    } else {
                        Swal.fire(`Something went wrong`, `Something went wrong, please try again.`, "warning");                        
                    }
                }).catch((error) => {
                    this.parseError(error, null, {});
                }).finally(() => {
                    this.isLoading = false;          
                });
        },
    }
}
</script>