<template>
  <div 
  v-if="statusDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Archived</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div class="d-flex flex-column me-n7 pe-7">
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Status</label>
        <input 
        v-model="statusDetails.status"
        name="status"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.status}" 
        placeholder="Status"/>
        <div 
        :v-if="errors?.status"
        class="invalid-feedback">{{ errors?.status?.[0] }}</div>
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Definition</label>
        <textarea
        v-model="statusDetails.definition" 
        name="definition" 
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.definition}"
        placeholder="Definition"
        rows="3"
        ></textarea>
        <div 
        :v-if="errors?.definition"
        class="invalid-feedback">{{ errors?.definition?.[0] }}</div>
      </div>
      
    </div>
    <div class="text-center pt-15">
      <button 
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
import StatesMixins from "Mixins/states.js";

export default {
  name: "status-view",

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
  },
  
  data: () => ({
    statusDetails: {
      status: "",
      definition: "",
    },
    
    editMode: true,
    isLoading: false,
    accessType: "add",

    submitUrl: ""
  }),

  mounted() {
    this.submitUrl = this.storeUrl;
  },

  mixins: [ResponseMixins, StatesMixins],

  methods: {
    
    fetch(data) {
      this.editMode = false;
      axios.get(data.find_url)
        .then((res) => {
          this.statusDetails = res.data.status_details;
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
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
          this.statusDetails = {
            status: "",
            definition: ""
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
      this.resetErrors();
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);

      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`Status ${action}`, `Status has been ${action}.`, "success");
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
      this.$emit("cancel")
    },

    cleanFields() {
      if(this.accessType != "add") return;
      this.statusDetails = {
        status: "",
        definition: "",
      };
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