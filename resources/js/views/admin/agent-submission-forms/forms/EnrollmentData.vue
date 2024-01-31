<template>
<!--begin::details View-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card header-->
    <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Family & Enrollment</h3>
        </div>
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->

    <!--begin::Card body-->
    <div class="card-body p-9">
        <form 
        @submit.prevent="onSubmitEnrollmentInformation($event)" class="form">
            <div class="row mb-7">
                <div v-for="toggle in toggles" class="col-md-6 mb-7">
                    <div class="col-lg-8 d-flex align-items-center">
                        <div class="form-check form-check-solid form-check-custom form-switch fv-row">
                            <input 
                            v-model="toggleForm[toggle.model]"
                            :disabled="isLoading || !editMode"
                            class="form-check-input w-45px h-30px" 
                            :checked="toggleForm[toggle.model] ? true: false"
                            type="checkbox" :id="toggle.model" :name="toggle.model" :value="true" />
                            <label class="form-check-label fw-semibold text-dark" :for="toggle.model">{{ toggle.label }}</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row pt-15">
                <div class="col text-left">
                    <button
                    v-if="!showAccordion"
                    @click="back()"
                    :disabled="isLoading"
                    type="button"
                    class="btn btn-light me-3">Back</button>
                </div>
                <div class="col text-right">
                    <button
                    type="submit"
                    v-if="editMode && !nextButton"
                    :disabled="isLoading"
                    class="btn btn-primary">Next</button>

                    <button
                    v-if="(!editMode && !showAccordion) || nextButton"
                    @click="nextButtonProceed()"
                    type="button"
                    class="btn btn-primary">Next</button>                    
                </div>
            </div> 
        </form>
    </div>
    <!--end::Card body-->
</div>

<div 
v-if="accordionType"
class="accordion" id="enrollment-accordion">
    <div 
    v-show="showAccordion && toggleForm.married"
    class="accordion-item">
        <h2 class="accordion-header" :id="`header-spousal-data`">
            <button
            @click="currentAccordion = 'spousal-data'"
            :class="{'collapsed': currentAccordion != 'spousal-data'}"
            class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#body-spousal-data" aria-expanded="true" aria-controls="body-spousal-data">
                Spousal Data
            </button>
        </h2>
        <div 
        :class="{'show': currentAccordion == 'spousal-data'}"
        id="body-spousal-data" class="accordion-collapse collapse" aria-labelledby="header-spousal-data" data-bs-parent="#enrollment-accordion">
            <div class="accordion-body" style="min-height: 350px">
                <spousal
                v-show="currentAccordion == 'spousal-data'"
                ref="spousal"
                :submit-enrollment-information-url="submitEnrollmentInformationUrl"
                :form-data="formData"
                :edit-mode="editMode"
                :toggle-form="toggleForm"
                @success="success"
                @proceed="proceed"
                >
                </spousal>
            </div>
        </div>
    </div>
    
    <div
    v-show="showAccordion && toggleForm.dependents"
    class="accordion-item">
        <h2 class="accordion-header" :id="`header-dependents`">
            <button
            :disabled="!enableDependents && formData?.dependents_list?.length === 0" 
            @click="currentAccordion = 'dependents'"
            :class="{'collapsed': currentAccordion != 'dependents'}"
            class="accordion-button fs-4 fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#body-dependents" aria-expanded="true" aria-controls="body-dependents">
                Dependents
            </button>
        </h2>
        <div 
        :class="{'show': currentAccordion == 'dependents'}"
        id="body-dependents" class="accordion-collapse collapse" aria-labelledby="header-dependents" data-bs-parent="#enrollment-accordion">
            <div class="accordion-body" style="min-height: 350px">
                <dependents
                v-show="currentAccordion == 'dependents'"
                ref="dependents"
                :submit-enrollment-information-url="submitEnrollmentInformationUrl"
                :form-data="formData"
                :edit-mode="editMode"
                >
                </dependents>
            </div>
        </div>
    </div>
</div>

<div v-if="!accordionType && showAccordion" id="enrollment">
    <!--begin::details View-->
    <div 
    v-if="toggleForm.married"
    class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Spousal Data</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card header-->

        <!--begin::Card body-->
        <div class="card-body p-9">
            <spousal
                ref="spousal"
                :submit-enrollment-information-url="submitEnrollmentInformationUrl"
                :form-data="formData"
                :edit-mode="editMode"
                :toggle-form="toggleForm"
                :show-next="false"
                :default-errors="errors"
                :enable-watcher="enableWatcher"
                @inputTrigger="saveInput"                
                @success="success"
                @proceed="proceed"
                >
            </spousal>
        </div>
    </div>

    <!--begin::details View-->
    <div 
    v-if="toggleForm.dependents"
    id="dependentsCard"
    class="card mb-5 mb-xl-10">
        <!--begin::Card header-->
        <div class="card-header cursor-pointer">
            <!--begin::Card title-->
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">Dependents</h3>
            </div>
            <!--end::Card title-->
        </div>
        <!--begin::Card header-->

        <!--begin::Card body-->
        <div class="card-body p-9">
            <dependents
            ref="dependents"
            :submit-enrollment-information-url="submitEnrollmentInformationUrl"
            :form-data="formData"
            :edit-mode="editMode"
            >
            </dependents>
        </div>
    </div>
</div>

<div 
v-if="showAccordion && !addingDependent && (toggleForm.married || toggleForm.dependents)"
class="row pt-15">
    <div class="col text-left">
        <button
        :disabled="isLoading"
        @click="back()"
        type="button"
        class="btn btn-light me-3">Back</button>
    </div>
    <div class="col text-right">
        <button
        v-if="editMode"
        :disabled="isLoading"
        @click="confirmFamilyAndEnrollment()"
        class="btn btn-primary">Confirm Family & Enrollment</button>

        <button
        v-if="!editMode"
        @click="proceed()"
        class="btn btn-primary">Next</button>
    </div>
</div> 

<!--end::details View-->
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import Spousal from "./enrollment-data/Spousal";
import Dependents from "./enrollment-data/Dependents";

export default {
    name: "enrollment-data",

    props: {
        submitEnrollmentInformationUrl: {
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

        accordionType: { 
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

    components: {
        Spousal,
        Dependents
    },
    
    watch: {
        toggleForm: {
            handler() {
				if(this.enableWatcher) {
					this.$emit("saveInput", this.toggleForm);
				}
			},
    		deep: true
        },

        "toggleForm.dependents"(value) {
            if(!value) {
                this.addingDependent = false;
                this.selectedDependent = "";
            }
            
            if(value) {
                this.showAccordion = false;
            }
        },

        "toggleForm.married" (value) {
            if(value) {
                this.showAccordion = false;
            }
        },
    },

    data: () => ({
        isLoading: false,

        toggleForm: {
            product_change: false,
            pregnant: false,
            married: false,
            smoker: false,
            dependents: false,
            american_indian_native: false
        },

        toggles: [
            { label: "Product Change", model: "product_change", value: false },
            { label: "Pregnant", model: "pregnant", value: false },            
            { label: "Married", model: "married", value: false },
            { label: "Smoker", model: "smoker", value: false },
            { label: "Dependents", model: "dependents", value: false },
            { label: "American Indian/Alaskan Native", model: "american_indian_native", value: false },                        
        ],
    
        showAccordion: false,
        currentAccordion: "spousal-data",
        enableDependents: false
    }),

    async mounted() {
        Object.entries(this.toggleForm).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                this.toggleForm[key] = this.formData[key] === 1 || this.formData[key] == "1" ? true : false; 
            }
        });
    },

    created() {
        this.$nextTick(() => {
            if(this.toggleForm.married || this.toggleForm.dependents) {
                this.showAccordion = true;
                if(this.toggleForm.married) {
                    this.currentAccordion = "spousal-data";
                    return;
                }
                if(this.toggleForm.dependents) {
                    this.enableDependents = true;
                    this.currentAccordion = "dependents";
                    return;
                }
            }
        });
    },

    mixins: [ ResponseMixins, FormatterMixins ],

    methods: {

        onSubmitEnrollmentInformation(event) {

            this.resetErrors();
            const params = new FormData();
            Object.entries(this.toggleForm).forEach((e) => {
                params.append(e[0], e[1] == true ? 1 : 0);
            });
                
            this.isLoading = true;
            axios.post(`${this.submitEnrollmentInformationUrl}?store_toggles=true`, params)
                .then((res) => {
                    if(this.toggleForm.married || this.toggleForm.dependents) {
                        this.showAccordion = true;
                        if(this.toggleForm.married) {
                            this.currentAccordion = "spousal-data";
                        } else {
                            this.currentAccordion = "dependents";
                        }

                        let componentId = 'enrollment-accordion';
                        if(!this.accordionType) {
                            componentId = "enrollment";
                        }
                        this.$nextTick(() => {
                            document.getElementById(componentId).scrollIntoView({ behavior: 'smooth', block: 'center' });
                        });
                    } else {
                        this.proceed(res.data.form);
                    }
                }).catch((error) => {
                    document.getElementById('enrollment-accordion').scrollIntoView({ behavior: 'smooth', block: 'center' });                    
                    const displayError = error.response.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
            
        },

        confirmFamilyAndEnrollment() {
            const dependentList = this.$refs["dependents"].dependents;
            if(this.toggleForm.dependents && dependentList?.length === 0) {
                this.currentAccordion = "dependents";
                this.$refs["spousal"].onClickSubmit();
                if(this.accordionType) {
                    document.getElementById('enrollment-accordion').scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    this.parseInfo("Please add atleast 1 dependent");
                    document.getElementById('enrollment').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                this.isLoading = false;
                let params = {
                    ...this.toggleForm,
                };
                if(this.toggleForm.married) {
                    const spousal = this.$refs["spousal"].spousal;
                    params = {
                        ...params,
                        ...spousal,
                        spousal_ssn: this.clean(spousal.spousal_ssn),
                        spousal_income: this.clean(spousal.spousal_income),
                        spousal_employer_phone: this.clean(spousal.spousal_employer_phone)
                    }
                }
                axios.post(`${this.submitEnrollmentInformationUrl}?store_all=true`, params)
                .then((res) => {
                    this.proceed(res.data.form);
                }).catch((error) => {
                    if(!this.accordionType) {
                        document.getElementById('enrollment').scrollIntoView({ behavior: 'smooth', block: 'center' });                    
                    }
                    const displayError = error.response.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
            }
        },

        success() {
            if(this.currentAccordion == "spousal-data") {
                if(this.toggleForm.dependents) {
                    this.enableDependents = true;
                    this.currentAccordion = "dependents";
                }
            }
        },

        nextButtonProceed() {
            if(this.toggleForm.dependent || this.toggleForm.married) {
                this.showAccordion = true;
                this.$nextTick(() => {
                    document.getElementById("enrollment-accordion").scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            } else {
                this.proceed();
            }
        },

        proceed(data) {
            this.$emit("proceed", data);            
        },

        back() {
            this.$emit("back");
        },

        saveInput(data) {
			this.$emit("saveInput", data);
		}
    },
}
</script>