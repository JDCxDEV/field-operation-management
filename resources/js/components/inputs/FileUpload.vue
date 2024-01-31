<template>
<label 
    :class="labelClass"
    class="fw-semibold fs-6 mb-2">{{ label }}</label>
<input 
    @change="filePreview"
    :class="{'form-control is-invalid': hasError, 'form-control': !hasError}" 
    :id="id" :name="id" 
    :accept="accept"
    type="file"
    :disabled="disabled" 
/>
<div :v-if="hasError" class="invalid-feedback">{{ errorMessage }}</div>
<div class="row mt-2">
    <div class="col-md-12">
        <img 
        class="img-thumbnail"
        style="width: 100px; height: 100px"
        v-if="previewSrc && preview" :src="previewSrc">

        <img 
        class="img-thumbnail"
        style="width: 100px; height: 100px"
        v-if="previewLink && !previewSrc" :src="`${previewLink}`">
    </div>
</div>
</template>
<script>
export default {
    name: "file-upload",

    props: {
        id: {
            type: String,
            default: "file"
        },

        label: {
            type: String,
            default: "Upload File"            
        },

        labelClass: {
            type: String,
            default: ""            
        },

        accept: {
            type: String,
            default: ""
        },

        preview: {
            type: Boolean,
            default: false
        },
        
        previewLink: {
            type: String,
            default: ""
        },

        hasError: {
            type: Boolean,
            default: false            
        },

        errorMessage: {
            type: String,
            default: ""            
        },

        disabled: {
            type: Boolean,
            default: false
        },
    },

    data: () => ({
        previewSrc: ""
    }),

    methods: {
        filePreview(event) {
            const file = event.target.files[0]
            if (!file) {
                this.previewSrc = "";
                return false;
            }
            if (!file.type.match('image.*')) {
                return false;
            }
            
            const reader = new FileReader();
            const $this = this;
            reader.onload = function (e) {
                $this.previewSrc = e.target.result;
            }
            reader.readAsDataURL(file);
        }        
    }
}
</script>