<template>
<div class="col-md-8 offset-md-2">
    <stepper 
    :steps="tabs"
    :current-step="currentStep"
    :last-access-tab="lastAccessTab"
    @jumpStep="jumpStep"
    class="mb-7"
    />

    <template v-if="!isLoading">
        <template v-if="currentTab?.key == 'account-information'">
            <!--begin::details View-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header cursor-pointer">
                    <!--begin::Card title-->
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Account Information</h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card header-->
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <form 
                    @submit.prevent="onVerifyAccount($event)" class="form">
                        <div class="row mb-6">
                            <!--begin::Col-->
                            <div class="col-lg-12">
                                <!--begin::Row-->
                                <div class="row">
                                    <label class="col-lg-12 col-form-label required fw-bold fs-6">Full Name</label>
                                    <div class="col-lg-6 fv-row">
                                        <input 
                                        v-model="user.first_name"
                                        :disabled="postLoad"
                                        :class="{'is-invalid': errors?.first_name}"
                                        type="text" name="first_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name"/>
                                        <div :v-if="errors?.first_name" class="invalid-feedback">{{ errors?.first_name?.[0] }}</div>
                                    </div>
                                    <!--end::Col-->

                                    <!--begin::Col-->
                                    <div class="col-lg-6 fv-row">
                                        <input 
                                        v-model="user.last_name"
                                        :disabled="postLoad"
                                        :class="{'is-invalid': errors?.last_name}"
                                        type="text" name="last_name" class="form-control form-control-lg form-control-solid" placeholder="Last name"/>
                                        <div :v-if="errors?.last_name" class="invalid-feedback">{{ errors?.last_name?.[0] }}</div>
                                    </div>
                                    <!--end::Col-->
                                </div>
                                <!--end::Row-->
                            </div>
                            <!--end::Col-->
                        </div>

                        <!--begin::Input group-->
                        <div class="row mb-6">
                            <!--begin::Label-->
                            <label class="col-lg-12 col-form-label fw-bold fs-6">
                                <span class="required">Contact Phone</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i>
                            </label>
                            <!--end::Label-->

                            <!--begin::Col-->
                            <div class="col-lg-12 fv-row">
                                <phone-number
                                :disabled="postLoad"
                                :value="user.info.phone"
                                :errors="errors?.phone ? true: false"
                                :error-message="errors?.phone?.[0]"
                                length="14"
                                input-class="form-control form-control-lg form-control-solid"
                                @update:value="newValue => user.info.phone = newValue"
                                ></phone-number>
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <div 
                        class="row pt-15">
                            <div class="offset-md-6 col-md-6 text-right">
                                <button
                                type="submit"
                                :disabled="postLoad"
                                class="btn btn-primary">Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::details View-->
        </template>
        <template v-if="currentTab?.key == 'sign-in'">
            <!--begin::details View-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header cursor-pointer">
                    <!--begin::Card title-->
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Sign-in Method</h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card header-->
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <form 
                    @submit.prevent="onVerifyEmail($event)" class="form">
                        <input type="hidden" name="current_email" :value="user.email"/>
                        <div class="row mb-6">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="fv-row mb-0">
                                    <label for="email" class="form-label fs-6 fw-bolder mb-3">Email Address</label>
                                    <input 
                                    :value="user.email"
                                    :disabled="postLoad"
                                    :class="{'is-invalid': errors?.email}"
                                    type="email" class="form-control form-control-lg form-control-solid" placeholder="Email Address" name="email" />
                                    <div :v-if="errors?.email" class="invalid-feedback">{{ errors?.email?.[0] }}</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="fv-row mb-0">
                                    <label for="current_password" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                                    <input 
                                    :class="{'is-invalid': errors?.current_password}"
                                    :disabled="postLoad"
                                    type="password" class="form-control form-control-lg form-control-solid" name="current_password" id="current_password"/>
                                    <div :v-if="errors?.current_password" class="invalid-feedback">{{ errors?.current_password?.[0] }}</div>
                                </div>
                            </div>
                        </div>

                        <div 
                        class="row pt-15">
                            <div class="col text-left">
                                <button
                                @click="back(1)"
                                :disabled="postLoad"
                                type="button"
                                class="btn btn-light me-3">Back</button>
                            </div>
                            <div class="col text-right">
                                <button
                                type="submit"
                                :disabled="postLoad"
                                class="btn btn-primary">Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::details View-->
        </template>

        <template v-if="currentTab?.key == 'authentication'">
            <!--begin::details View-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header cursor-pointer">
                    <!--begin::Card title-->
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Choose An Authentication Method</h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <form 
                    @submit.prevent="onVerifyAuthentication($event)" class="form">
                        <p class="text-muted fs-5 fw-semibold mb-10">
                            In addition to your email and password, you’ll have to enter a code (delivered via email or SMS) to log into your account.
                        </p>

                        <input 
                        v-model="user.two_auth_type"
                        type="radio" class="btn-check" name="two_auth_type" value="email" checked="checked" id="email">
                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center mb-5" for="email">
                            <!--begin::Svg Icon | path: icons/duotune/coding/cod001.svg-->
                            <span class="svg-icon svg-icon-4x me-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M21 19H3C2.4 19 2 18.6 2 18V6C2 5.4 2.4 5 3 5H21C21.6 5 22 5.4 22 6V18C22 18.6 21.6 19 21 19Z" fill="currentColor"/>
                                    <path d="M21 5H2.99999C2.69999 5 2.49999 5.10005 2.29999 5.30005L11.2 13.3C11.7 13.7 12.4 13.7 12.8 13.3L21.7 5.30005C21.5 5.10005 21.3 5 21 5Z" fill="currentColor"/>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <span class="d-block fw-semibold text-start">                            
                                <span class="text-dark fw-bold d-block fs-3">Email</span>
                                <span class="text-muted fw-semibold fs-6">
                                    We will send a code via Email if you need to use your backup login method.
                                </span>
                            </span>
                        </label>

                        <input 
                        v-model="user.two_auth_type"
                        type="radio" class="btn-check" name="two_auth_type" value="sms" id="sms">
                        <label class="btn btn-outline btn-outline-dashed btn-active-light-primary p-7 d-flex align-items-center" for="sms">
                            <!--begin::Svg Icon | path: icons/duotune/communication/com003.svg-->
                            <span class="svg-icon svg-icon-4x me-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z" fill="currentColor"></path>
                                    <path d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <span class="d-block fw-semibold text-start">                              
                                <span class="text-dark fw-bold d-block fs-3">SMS</span>
                                <span class="text-muted fw-semibold fs-6">We will send a code via SMS if you need to use your backup login method.</span>
                            </span>                           
                        </label>
                        
                        <div 
                        class="row pt-15">
                            <div class="col text-left">
                                <button
                                @click="back(2)"
                                :disabled="postLoad"
                                type="button"
                                class="btn btn-light me-3">Back</button>
                            </div>
                            <div class="col text-right">
                                <button
                                type="submit"
                                :disabled="postLoad"
                                class="btn btn-primary">Verify</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::details View-->
        </template>

        <template v-if="currentTab?.key == 'verified'">
            <!--begin::details View-->
            <div class="card mb-5 mb-xl-10">
                <!--begin::Card header-->
                <div class="card-header cursor-pointer">
                    <!--begin::Card title-->
                    <div class="card-title m-0">
                        <h3 class="fw-bolder m-0">Account Verified</h3>
                    </div>
                    <!--end::Card title-->
                </div>
                <!--begin::Card body-->
                <div class="card-body p-9">
                    <div 
                    class="alert alert-success d-flex align-items-center p-5">
                        <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/kt-products/docs/metronic/html/releases/2023-01-30-131017/core/html/src/media/icons/duotune/general/gen026.svg-->
                        <span class="svg-icon svg-icon-muted svg-icon-2hx me-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                                <path d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z" fill="currentColor"/>
                                <path d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z" fill="white"/>
                            </svg>
                        </span>
                        <!--end::Svg Icon-->

                        <!--begin::Wrapper-->
                        <div class="d-flex flex-column">
                            <!--begin::Title-->
                            <h4 class="mb-1 text-dark">You're account has been verified.</h4>
                            <!--end::Title-->
                            <!--begin::Content-->
                            <span>Refresh the page to proceed.</span>
                            <!--end::Content-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                </div>
                <!--end::Card body-->
            </div>
            <!--end::details View-->
        </template>
    </template>
</div>
</template>

<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import Stepper from "Components/steppers/Stepper.vue";
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
    name: "verify-account",

    components: { 
        Stepper,
        PhoneNumber
    },
    
    computed: {
        currentStep() {
            return this.currentTab.step;
        },

        currentTabStep() {
            return this.currentTab.step - 1;
        }
    },    

    data: () => ({
        user: {
            info: {
                phone: ""
            }
        },
        tabs: [
            { name: "Account Information", key: "account-information", step: 1, icon: "fa-solid fa-user" },
            { name: "Sign-in Method", key: "sign-in", step: 2, icon: "fa-solid fa-shield"},
            { name: "Authentication Method", key: "authentication", step: 3, icon: "fa-solid fa-lock"},
            { name: "Verified", key: "verified", step: 4, icon: "fa-solid fa-check"},
        ],
        currentTab: {},
        lastAccessTab: "",
        isLoading: false,
        postLoad: false,
        verified: false
    }),

    mounted() {
        this.init();
        this.currentTab = this.tabs[0];
        this.lastAccessTab = 1;
    },

    mixins: [ ResponseMixins, FormatterMixins ],

    methods: {
        async init() {
            this.isLoading = true;
            
            await axios.get("/users/get/user")
                .then((res) => {
                    this.user = res.data.user;
                    const formattedPhone = this.phoneFormat(res.data.user.info.phone);
                    this.user.info.phone = formattedPhone;
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        onVerifyAccount(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("phone", this.clean(this.user.info.phone));

            this.postLoad = true;
            axios.post(`/account/settings/${this.user.id}`, params)
            .then((res) => {
                this.init();
                this.next(2);
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.postLoad = false;
            });
        },

        onVerifyEmail(event) {
            this.resetErrors();
            let form = event.target;
            this.postLoad = true;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);

            this.postLoad = true;
            axios.post(`/account/settings/email/${this.user.id}`, params)
            .then((res) => {
                this.init();
                this.next(3);
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.postLoad = false;
            });
        },

        onVerifyAuthentication() {
            let form = event.target;
            this.postLoad = true;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("two_auth", true);

            this.postLoad = true;
            axios.post(`/account/settings/update-two-auth/${this.user.id}`, params)
            .then((res) => {
                this.init();
                this.verify();
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.postLoad = false;
            });
        },

        verify() {
            this.postLoad = true;
            axios.post(`/account/settings/verify/${this.user.id}`, {})
            .then((res) => {
                this.next(4);
                this.verified = true;
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.postLoad = false;
            });
        },

        back(step) {
            this.currentTab = this.tabs[step - 1];
        },        

        next(step) {
            this.lastAccessTab = step;
            this.currentTab = this.tabs[step - 1];
        },

        jumpStep(tab) {
            if(!this.verified) {
                this.currentTab = tab;
            }
        }

    }

}
</script>