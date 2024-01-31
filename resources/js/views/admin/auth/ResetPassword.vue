<template>
    <form 
    @submit.prevent="onSubmit($event)"
    class="form w-100" novalidate="novalidate">
        <!-- Password Reset Token -->
        <input type="hidden" name="token" :value="token">

        <!--begin::Heading-->
        <div class="text-center mb-11">
            <!--begin::Title-->
            <h1 class="text-dark fw-bolder mb-3">Update Your Password</h1>
            <!--end::Title-->
            <!--begin::Subtitle-->
            <div class="text-gray-500 fw-semibold fs-6">This is a secure area of the application. Please confirm your password before continuing</div>
            <!--end::Subtitle=-->
        </div>
        <!--begin::Heading-->

        <!--begin::Input group=-->
        <div class="fv-row mb-8 fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
            <!--begin::Email-->
            <input 
            v-model="email"
            :class="{'is-invalid': errors?.email}"
            :disabled="disableEmail"
            type="text" placeholder="Email" autocomplete="off" class="form-control">
            <!--end::Email-->
        </div>
        <!--begin::Input group-->
        <div class="fv-row mb-8 fv-plugins-icon-container" data-kt-password-meter="true">
            <!--begin::Wrapper-->
            <div class="mb-1">
                <!--begin::Input wrapper-->
                <div class="position-relative mb-3">
                    <input 
                    v-model="password"
                    :class="{'is-invalid': errors?.password}"                    
                    class="form-control bg-transparent" type="password" placeholder="Password" name="password" autocomplete="off">
                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
                <!--end::Input wrapper-->
                <!--begin::Meter-->
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2 active"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div>
                <!--end::Meter-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Hint-->
            <!-- <div class="text-muted">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</div> -->
            <!--end::Hint-->
        </div>
        <!--end::Input group=-->
        <!--end::Input group=-->
        <div class="fv-row mb-8 fv-plugins-icon-container">
            <!--begin::Repeat Password-->
            <input
            v-model="password_confirmation"             
            placeholder="Repeat Password" name="password_confirmation" type="password" autocomplete="off" class="form-control bg-transparent">
            <!--end::Repeat Password-->
        </div>
        <!--end::Input group=-->
        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button 
            :disabled="isLoading"
            type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                Submit
            </button>
        </div>
        <!--end::Submit button-->
        <div></div>
    </form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";

export default {
    name: "reset-password",

    props: {
        submitUrl: {
            type: String,
            default: ""
        },

        token: {
            type: String,
            default: ""
        },

        requestEmail: {
            type: String,
            default: ""
        },
    },

    data: () => ({
        email: "",
        password: "",
        password_confirmation: "",
        isLoading: false,
        disableEmail: false
    }),

    mixins: [ ResponseMixins ],

    mounted() {
        if(this.requestEmail) {
            this.email = this.requestEmail;
            this.disableEmail = true;
        }
    },

    methods: {
        onSubmit(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("email", this.email);

            this.isLoading = true;
            axios.post(this.submitUrl, params)
                .then((res) => {
                    if(res.data.status == "passwords.reset") {
                        Swal.fire(`Password Reset`, `Password has been succesfully reset.`, "success");
                        setTimeout(() => {
                            window.location.href = res.data.redirect;
                        }, 1000);
                    } else if (res.data.status == "passwords.token") {
                        Swal.fire(`Reset Link Expired`, `Password reset link has been expired.`, "warning");                        
                    } else {
                        Swal.fire(`Something went wrong`, `Something went wrong, please try again.`, "warning");                        
                    }
                    this.resetFields();
                }).catch((error) => {
                    this.parseError(error, null, {});
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

        resetFields() {
            this.email = "";
            this.password = "";
            this.password_confirmation = "";
        }
    }
}
</script>