<template>
  <div 
  v-if="templateDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Archived</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div class="d-flex flex-column me-n7 pe-7">
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Title</label>
        <input 
        v-model="templateDetails.title"
        name="title"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.title}" 
        placeholder="Title"/>
        <div 
        :v-if="errors?.title"
        class="invalid-feedback">{{ errors?.title?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="fw-semibold fs-6 mb-2">Description</label>
        <input 
        v-model="templateDetails.description"
        name="description"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        placeholder="Description"/>
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Content</label>
        <div v-if="!initLoading" :class="{'is-invalid': errors?.content}">
          <Editor
          v-model="templateDetails.content"        
          :disabled="!editMode"
          :api-key="tinymceApiKey"
          />
        </div>
        <div 
        :v-if="errors?.content"
        class="invalid-feedback">{{ errors?.content?.[0] }}</div>
      </div>

      <template v-for="(child, index) in formChildren">

        <div class="separator border-gray-200 mb-7"></div>
        
        <div 
        v-if="editMode"
        class="fv-row mb-7">
          <div class="text-right">
            <button 
            @click="removeChildren(index, child)"
            type="button" class="btn btn-warning">
              <span class="svg-icon svg-icon-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
                </svg>
              </span>
              Remove Template
            </button>
          </div>
        </div>

        <div class="fv-row mb-7">
          <label class="required fw-semibold fs-6 mb-2">Title</label>
          <input 
          v-model="child.title"
          :disabled="!editMode"
          class="form-control mb-3 mb-lg-0"
          :class="{'is-invalid': child.title_error}" 
          placeholder="Title"/>
          <div 
          :v-if="child.title_error"
          class="invalid-feedback">{{ child.title_error }}</div>
        </div>

        <div class="fv-row mb-7">
          <label class="fw-semibold fs-6 mb-2">Description</label>
          <input 
          v-model="child.description"
          :disabled="!editMode"
          class="form-control mb-3 mb-lg-0"
          placeholder="Description"/>
        </div>

        <div class="fv-row mb-7">
          <label class="required fw-semibold fs-6 mb-2">Content</label>
          <div v-if="!initLoading" :class="{'is-invalid': child.content_error}">
            <Editor
            v-model="child.content"        
            :disabled="!editMode"
            :api-key="tinymceApiKey"
            />
          </div>
          <div 
          :v-if="child.content_error"
          class="invalid-feedback">{{ child.content_error }}</div>
        </div>
      </template>

      <div 
      v-if="editMode"
      class="fv-row mb-7">
        <div class="text-right">
          <button 
          @click="addChildren()"
          type="button" class="btn btn-info">
            <span class="svg-icon svg-icon-2">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
              </svg>
            </span>
            Add Template
          </button>
        </div>
      </div>
    </div>
    <div class="text-right pt-15">
      <button
      v-if="!hideDiscard" 
      :disabled="isLoading"          
      type="reset" 
      @click="cancel()"          
      class="btn btn-light me-3">Discard</button>

      <button
      v-if="editMode" 
      :disabled="isLoading"
      class="btn btn-primary">Submit</button>

    </div>
  </form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import Editor from '@tinymce/tinymce-vue';

export default {
  name: "form-template-view",

  components: {
    Editor
  },

  props: {
    storeUrl: {
      type: String, 
      default: null
    },

    hideDiscard: {
      type: Boolean,
      default: false
    },
  },
  
  data: () => ({
    templateDetails: {
      title: "",
      description: "",
      content: "",
    },

    formChildren: [],
    deletedChildren: [],

    editMode: true,
    isLoading: false,
    initLoading: false,
    accessType: "add",

    tinymceApiKey: "9oesjgfwa5jjys5hvzntvehhdpgumc1ck0g7ls04zpwer550",

    submitUrl: "",
  }),

  mounted() {
    this.submitUrl = this.storeUrl;
  },

  mixins: [ResponseMixins],

  methods: {
    
    fetch(data) {
      this.editMode = false;
      this.initLoading = true;
      axios.get(data.find_url)
        .then((res) => {
          this.$nextTick(() => {
            this.templateDetails = res.data.template;
            this.formChildren = this.templateDetails?.children;
          });
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.initLoading = false;
          if(this.accessType == "edit") {
            this.editMode = true;
          }
        });
    },

    setAccessType(type) {
      this.resetErrors();
      this.accessType = type;
      switch(type) {
        case "add":
          this.templateDetails = {
            title: "",
            description: "",
            content: "",
          };
          this.editMode = true;
          break;
        case "edit":
          this.editMode = true;
          break;
        case "view":
          this.editMode = false;          
          break;
      }
    },

    onSubmit(event) {
      if(this.formChildren.length) {
        if(this.validateChildren()) {
          return;
        }
      }
      this.resetErrors();
      
      const formChildren = this.formChildren.map((i) => {
        return { id: i.id, title: i.title, description: i.description, content: i.content };
      });

      const data = {
        title: this.templateDetails.title,
        description: this.templateDetails.description,
        content: this.templateDetails.content,
        children: formChildren,
        deleted: this.deletedChildren,
      };

      this.isLoading = true;
      axios.post(this.submitUrl, data)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`${this.templateDetails.title} ${action}`, `${this.templateDetails.title} has been ${action}.`, "success");
          this.$emit("cancel");
          this.$emit("updated");
          this.cleanFields();
        }).catch((error) => {
          console.log(error);
          /* Display Error message */
          this.parseError(error, null, {});          
        }).finally(() => {
          this.isLoading = false;          
        });
    },

    setToEditMode() {
      this.editMode = true;
      this.accessType = "edit";
      this.$emit("editing");
    },  

    cancel() {
      this.$emit("cancel");
    },

    cleanFields() {
      if(this.accessType != "add") return;
      this.templateDetails = {
        title: "",
        description: "",
        content: "",
      };
    },

    addChildren() {
      if(this.formChildren.length) {
        if(this.validateChildren()) {
          return;
        }        
      } 
      this.formChildren.push({
        title: "",
        title_error: "",
        description: "",
        content: "",
        content_error: ""
      });
    },

    removeChildren(index, data) {
      if(data.id) {
        this.deletedChildren.push(data.id);
      }
      this.formChildren.splice(index, 1);
    },

    validateChildren() {
      let status = false;
      this.formChildren.forEach((child) => {
        if(child.title === "") {
          child.title_error = "The title field is required.",
          status = true;
        } else {
          child.title_error = "";
        }

        if(child.content === "") {
          child.content_error = "The content field is required.",
          status = true;
        } else {
          child.content_error = "";
        }
      });
      if(status) {
        this.parseError("Please correct the following error(s)");
      }
      return status;
    },

  }

}
</script>
<style>
.text-right {
  text-align: right;
}
.mr-s {
  margin-right: 10px;
}
</style>