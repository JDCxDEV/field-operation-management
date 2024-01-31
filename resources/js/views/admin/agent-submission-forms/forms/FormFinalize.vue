<template>
<!--begin::details View-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header cursor-pointer">
        <!--begin::Card title-->
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Form Finalize</h3>
        </div>
        <!--end::Card title-->
    </div>
    <!--begin::Card header-->

    <!--begin::Card body-->
    <div class="card-body p-9">
        <div 
        id="additionalFiles"
        class="row mb-5">
            <div class="col-md-12 mb-5">
                <h3 class="fw-bolder m-0">Additional Files</h3>
                <div class="mt-5">
                    <button 
                    @click="isAddingFile = true"
                    v-if="!isAddingFile && editMode" class="btn btn-primary">
                        <i class="ki-duotone ki-add-files">
                            <i class="path1"></i>
                            <i class="path2"></i>
                            <i class="path3"></i>
                        </i>
                        Add Files
                    </button>
                </div>
            </div>

            <div v-if="isAddingFile">
                <form @submit.prevent="submitFile($event)" class="form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="required fw-semibold fs-6 mb-2">ID Type</label>
                                <select
                                v-model="file.id_type"
                                :disabled="!editMode"
                                class="form-control"
                                :class="{'is-invalid': errors?.id_type}"
                                >
                                    <option selected disabled value="">Please select ID Type</option>
                                    <option v-for="idType in idList" :value="idType.value">{{ idType.label }}</option>
                                </select>
                                <div 
                                :v-if="errors?.id_type"
                                class="invalid-feedback">ID type is required</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-2">
                                <label class="required fw-semibold fs-6 mb-2">File</label>

                                <input 
                                ref="file"
                                @change="onUpload"
                                :disabled="!editMode"
                                :class="{'is-invalid': errors?.file}"
                                type="file" class="form-control" accept="image/*">

                                <div 
                                :v-if="errors?.file"
                                class="invalid-feedback">File is required</div>
                            </div>
                        </div>
                    </div>

                    <div 
                    v-if="editMode"
                    class="row pt-5">
                        <div class="col-md-12 text-right">
                            <button
                            @click="isAddingFile = false"
                            :disabled="isLoading"
                            class="btn btn-light me-3">Discard</button>

                            <button
                            type="submit"
                            :disabled="isLoading || !editMode"
                            class="btn btn-primary">Confirm Additional Files</button>
                        </div>
                    </div>
                </form>
            </div>

            <div 
            v-if="noFileUploaded"
            class="col-md-12 mt-5 mb-5">
                <div 
                class="alert alert-warning d-flex align-items-center">
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
                        <span class="text-gray-900">Please add the required files to proceed the submission.</span>
                        <!--end::Content-->
                    </div>
                    <!--end::Wrapper-->
                </div>
            </div>
            
        </div>

        <div 
        v-if="uploadedFiles.length"
        class="row mb-7">
            <div class="col-md-12 mb-5">
                <hr />
            </div>
            <div 
            v-for="uploaded in uploadedFiles"
            class="col-md-4">
                <label class="fw-semibold fs-6 mb-2"><strong>{{ renderIdType(uploaded.id_type) }}</strong></label>
                <div class="img-container">
                    <div 
                    v-if="editMode"
                    class="remove-btn-container">
                        <button
                        :disabled="isLoading" 
                        @click="removeUploadedFile(uploaded, uploaded.index)"
                        class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <img 
                    class="img-thumbnail"
                    style="width: 100%; height: auto"
                    v-if="uploaded.file" :src="uploaded.file">
                </div>
            </div>
            <div class="col-md-12 mb-5 mt-5">
                <hr />
            </div>
        </div>

        <form 
        ref="signature"
        @submit.prevent="onSaveSignature($event)" class="form">
            <div class="row mb-7">
                <div class="col-md-12">
                    <label class="fw-semibold fs-6 mb-2">Additional Notes</label>
                    <textarea 
                    v-model="additional_notes"
                    name="additional_notes"
                    :disabled="isLoading"                    
                    class="form-control mb-3 mb-lg-0"
                    style="resize: none"
                    ></textarea>                    
                </div>                
            </div>

            <div class="row">
                <div v-if="template" class="col-md-12">
                    <div class="text-center mb-4">
                        <h3 class="fw-bolder m-0">{{ template.title }}</h3>
                    </div>
                    <div class="text-container mb-7" v-html="template.content"></div>

                    <template v-if="template.children">
                        <template v-for="child in template.children">
                            <div class="text-center mb-4">
                                <h3 class="fw-bolder m-0">{{ child.title }}</h3>
                            </div>
                            <div class="text-container mb-7" v-html="child.content"></div>                        
                        </template>
                    </template>
                </div>
                
                <div class="col-md-12">
                    <VueSignaturePad 
                    ref="signaturePad"
                    v-show="!signature && !signaturePreview" 
                    :class="{'has-error is-invalid': errors?.signature, 'mb-7': !errors?.signature}"
                    class="signature-container" width="100%" height="200px" />

                    <div 
                    :v-if="errors?.signature"
                    class="mb-7 invalid-feedback">{{ errors?.signature?.[0] }}</div>                
                
                    <div 
                    v-if="signature || signaturePreview" 
                    class="mb-7 signature-container signature-image"
                    v-bind:style="{ backgroundImage: 'url(' + (signature ? signature : signaturePreview) + ')' }"></div>
                </div>
                
                <div 
                v-if="editMode"
                class="mt-4">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col text-left">
                                <button
                                v-if="nextButton" 
                                @click="back()"
                                class="btn btn-light me-3">Back</button>
                            </div>

                            <div 
                            v-if="!signature && !signaturePreview"
                            class="col text-right">
                                <button 
                                @click="clear()"
                                type="button"
                                class="btn btn-light me-3">Clear</button>
                                <button
                                type="submit"
                                class="btn btn-primary">Accept Signature</button>
                            </div>

                            <div 
                            v-if="signature || signaturePreview"
                            class="col text-right">
                                <button 
                                @click="clear()"
                                class="btn btn-light me-3">Re-write Signature</button>

                                <button
                                type="button"
                                :disabled="isLoading"
                                @click="submit()"
                                class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div 
        v-if="!editMode"
        class="row mt-4">
            <div class="col text-left">
                <button 
                @click="back()"
                type="button"
                class="btn btn-light me-3">Back</button>
            </div>
        </div>
    </div>
    <!--end::Card body-->
</div>
<!--end::details View-->
</template>
<script>
import ResponseMixins from "Mixins/response.js";

export default {
    name: "form-finalize",

    props: {
        saveSignatureUrl: {
            type: String,
            default: ""
        },

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

        coordinates: {
            type: Object,
            default: {}
        },

        template: {
            type: Object,
            default: {}
        },

        additionalFilesSubmitUrl: {
            type: String,
            default: ""
        },

        enableWatcher: {
			type: Boolean,
            default: false
		},

        nextButton: {
            type: Boolean,
            default: true
        }

    },

    watch: {
		additional_notes: {
			handler() {
				if(this.enableWatcher) {
					this.$emit("saveInput", {additional_notes: this.additional_notes});
				}
			},
    		deep: true
		},
    },

    data: () => ({
        additional_notes: "",
        signature: "",
        signaturePreview: "",
        isLoading: false,
        uploadedFiles: [],
        
        noFileUploaded: false,
        isAddingFile: false,
        file: {
            id_type: "",
            file: ""
        },
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
            { label: "Voter Registration ID", value: "voter_registration_id", default: false },
            { label: "Client With Acknowledgment Form", value: "client_with_acknowledgment_form", default: false }       
        ],
    }),

    mounted() {
        const signature = this.$refs.signature.elements;

        this.uploadedFiles = this.formData.files?.map((file) => {
            return {
                id: file.id,
                id_type: file.id_type,
                file: file.file_path
            };
        });

        signature.forEach((element) => {
            element.disabled = !this.editMode
        });

        this.additional_notes = this.formData.additional_notes;
        this.signaturePreview = this.formData?.signature_url ?? "";
    },

    mixins: [ ResponseMixins ],

    methods: {

        async submitFile(event) {
            this.resetErrors();

            this.isLoading = true;

            await axios.post(this.additionalFilesSubmitUrl, this.file)
                .then((res) => {
                    let formData = res.data.form;
                    this.$emit("updateForm", formData);
                    this.uploadedFiles = formData.files.map((file) => {
                        return {
                            id: file.id,
                            id_type: file.id_type,
                            file: file.file_path
                        };
                    });
                    this.file = {
                        file: "",
                        id_type: ""
                    };
                    this.$refs["file"].value = "";
                    this.noFileUploaded = false;
                }).catch((error) => {
                    console.log(error);
                    const displayError = error.response.status === 422 ? false : true;                    
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;
                });
        },

        async checkFiles() {
            let hasError = false;

            if(!this.uploadedFiles.length) {
                hasError = true;
                this.noFileUploaded = true;
            }

            return hasError;
        },

        onUpload(event) {
            const files = event.target.files;
            files.forEach((file) => {
                if (!file) {
                    return false;
                }
                if (!file.type.match('image.*')) {
                    return false;
                }
                
                const reader = new FileReader();
                const $this = this;
                reader.onload = function (e) {
                    $this.file.file = e.target.result;
                }
                reader.readAsDataURL(file);                
            });
        },

        removeUploadedFile(file, index) {
            if(!file.id) {
                const fileIndex = this.uploadedFiles.findIndex((f => f.index == index));
                this.uploadedFiles.splice(fileIndex, 1);
            } else {
                Swal.fire({
                    title: "Delete Uploaded File",
                    html: "Are you sure to delete the updated file?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Confirm"
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        this.isLoading = true;                        
                        axios.get(`/agent-submission-forms/delete-file/${file.id}`)
                            .then((res) => {
                                let formData = res.data.form;
                                this.$emit("updateForm", formData);
                                const fileIndex = this.uploadedFiles.findIndex((f => f.id == file.id));
                                this.uploadedFiles.splice(fileIndex, 1);
                            }).finally(() => {
                                this.isLoading = false;
                            });
                    }
                });
            }
        },

        async onSaveSignature(event) {
            await this.saveSignature();
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("signature", this.signature);
            this.isLoading = true;
            axios.post(this.saveSignatureUrl, params)
                .then((res) => {
                    this.$emit("updateForm", res.data.form);
                }).catch((error) => {
                    const displayError = error.response.status === 422 ? false : true;                    
                    this.parseError(error, null, {}, displayError);
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

        async submit() {
            if(await this.checkFiles()) {
                document.getElementById('additionalFiles').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            Swal.fire({
                title: "Submit Form",
                html: "Are you sure to submit the form?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Submit"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isLoading = true;
                    axios.post(this.submitUrl, { ...this.coordinates })
                        .then((res) => {
                            Swal.fire("Form Submitted", "Form has been successfully submitted", "success");
                            this.$emit("successSubmission", res.data.redirect);
                            this.$emit("updateForm", res.data.form);
                        }).catch((error) => {
                            this.parseError(error, null, {});
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });            
        },

        clear() {
            this.$refs.signaturePad.clearSignature();
            this.signature = "";
            this.signaturePreview = "";
        },

        saveSignature() {
            const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
            if(!isEmpty) {
                this.signature = data;
                console.log(data);
            }
        },

        back() {
            this.$emit("back");
        },

        renderIdType(idType) {
            const type = this.idList.find((a  => a.value == idType ));
            if(type) {
                return type.label;
            };
        }
    }
}
</script>
<style>
.text-container {
    border: 2px solid #000;
    border-radius: 5px;
    padding: 16px;    
}

.signature-container {
    border-radius: 5px;
    background: #c5c5c5;
}

.signature-image {
    width: 100%; 
    height: 200px; 
    background-position: center; 
    background-repeat: no-repeat; 
    background-size: cover;
}

.has-error {
    border: 2px solid #f1416c;
}

.img-container {
    position: relative;
}

.remove-btn-container {
    position: absolute;
    right: 0;
}
</style>