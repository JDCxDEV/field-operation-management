<template>
<!--begin::details View-->
<div class="card mb-5 mb-xl-10">
    <!--begin::Card body-->
    <div class="card-body p-9">

        <form 
        ref="additionalFiles"
        @submit.prevent="onSubmit($event)" class="form">
            <div class="row mb-7">
                <div class="col-md-12">
                    <div class="accordion" id="acceptableIds">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#idsHolder" aria-expanded="true" aria-controls="idsHolder">
                                    Acceptable Forms of ID
                                </button>
                            </h2>
                            <div id="idsHolder" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#acceptableIds">
                                <div class="accordion-body">
                                    <div class="row">
                                        <template v-for="id in idList">
                                            <div class="col-md-3 mb-3">
                                                <code class="text-gray-700">{{ id.label }}</code>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div 
            v-if="displayAlert"
            class="alert alert-warning d-flex align-items-center p-5">
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
                    <h4 class="mb-1 text-dark">Insufficient Requirements</h4>
                    <!--end::Title-->
                    <!--begin::Content-->
                    <span>Please add the required files to proceed the submission.</span>
                    <!--end::Content-->
                </div>
                <!--end::Wrapper-->
            </div>

            <!-- <template v-if="form.main_id_type"> -->
                <div class="row mb-7">
                    <div class="col-md-6">
                        <file-upload 
                        id="main_id_file"
                        :disabled="!editMode"
                        :preview="true"
                        :preview-link="formData.main_id_file_url"
                        label="Main ID"
                        label-class="required"                      
                        accept="image/*"
                        :has-error="errors?.main_id_file"
                        :error-message="errors?.main_id_file ? errors?.main_id_file?.[0] : ''" 
                        />
                    </div>
                    <div class="col-md-6">
                        <file-upload 
                        id="other_file"
                        :disabled="!editMode"
                        :preview="true"
                        :preview-link="formData.other_file_url"
                        label="Other ID"
                        accept="image/*"
                        :has-error="errors?.other_file"
                        :error-message="errors?.other_file ? errors?.other_file?.[0] : ''"                        
                        />
                    </div>
                </div>
                <div class="row mb-7">
                    <div class="col-md-6">
                        <file-upload 
                        id="image_with_form"
                        :disabled="!editMode"
                        :preview="true"
                        :preview-link="formData.image_with_form_url"                  
                        label="Clear Image of Person with Acknowledgment Form"
                        label-class="required"
                        accept="image/*"
                        :has-error="errors?.image_with_form"
                        :error-message="errors?.image_with_form ? errors?.image_with_form?.[0] : ''"                     
                        />
                    </div>
                    <div class="col-md-6">
                        <file-upload 
                        id="additional_file_one"
                        :disabled="!editMode"
                        :preview="true"
                        :preview-link="formData.additional_file_one_url"
                        label="Additional File One"
                        accept="image/*"
                        :has-error="errors?.additional_file_one"
                        :error-message="errors?.additional_file_one ? errors?.additional_file_one?.[0] : ''"                        
                        />
                    </div>
                </div>
            <!-- </template> -->
            <div 
            v-if="editMode"
            class="row pt-15">
                <div class="col-md-6 text-left">
                    <button 
                    @click="back()"
                    :disabled="isLoading"
                    class="btn btn-light me-3">Back</button>
                </div>
                <div class="col-md-6 text-right">
                    <button
                    type="submit"
                    :disabled="isLoading"
                    class="btn btn-primary">Confirm Additional Files</button>
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
import FileUpload from "Components/inputs/FileUpload.vue";
export default {
    name: "additional-files",
    
    components: { FileUpload },

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

    computed: {
        idType() {
            if(this.form.main_id_type) {
                const id = this.idList.find((a => this.form.main_id_type == a.value));
                if(id) return id.label;
            }   
            return "";
        },

        displayAlert() {
            if(this.for_submission || this.formData.current_step === 6) {
                if(!this.formData.main_id_file || !this.formData.image_with_form) {
                    return true;
                }
            } 
            return false;
        }
    },

    data: () => ({
        idList: [
            { label: "Library Card", value: "library_card", default: false },
            { label: "Jail Card", value: "jail_card", default: false },
            { label: "Bus Card", value: "bus_card", default: false },
            { label: "Employment ID", value: "employment_id", default: false },
            { label: "Military ID", value: "military_id", default: false },
            { label: "Student ID", value: "student_id", default: false },
            { label: "Retirement Center ID", value: "retirement_center_id", default: false },
            { label: "Neighborhood Association ID", value: "neighborhood_association_id", default: false },
            { label: "Public Assistance ID", value: "public_assistance_id", default: false },
            { label: "US Veteran Health ID", value: "us_veteran_health_id", default: false },
            { label: "Concealed Carry ID", value: "concealed_carry_id", default: false },
            { label: "Social Security ID", value: "social_security_id", default: false },
            { label: "US Passport", value: "us_passport", default: false },
            { label: "Immigration ID", value: "immigration_id", default: false },
            { label: "Voter Registration ID", value: "Voter Registration ID", default: false },            
        ],

        form: {
            main_id_type: "",
            main_id_file: "",
            other_file: "",
            image_with_form: "",
            additional_file_one: ""
        },

        isLoading: false,
        forSubmission: false
    }),

    mounted() {
    
        if(this.formData.current_step == 6) {
            this.forSubmission = true;
        }

        if(!this.editMode) {
            const additionalFiles = this.$refs.additionalFiles.elements;
            additionalFiles.forEach((element) => {
                element.disabled = !this.editMode;
            });
        }

        Object.entries(this.form).forEach((obj) => {
            const key = obj[0];
            if(this.formData[key]) {
                this.form[key] = this.formData[key]; 
            }
        });
    },

    mixins: [ResponseMixins],

    methods: {

        onSubmit(event) {
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("for_submission", this.forSubmission);
            this.isLoading = true;
            axios.post(this.submitUrl, params)
                .then((res) => {
                    this.proceed(res.data.form);
                }).catch((error) => {
                    const displayError = error.response.status === 422 ? false : true;
                    this.parseError(error, null, {}, displayError);
                    this.isLoading = false;          
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