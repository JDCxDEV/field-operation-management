<template>
  <div 
  v-if="addressDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Archived</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div class="d-flex flex-column me-n7 pe-7">
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Address Line 1</label>
        <auto-complete-address
        v-if="initState"
        :address="addressDetails.address_1"
        name="address_1"
        :has-error="errors?.address_1"
        :error-message="errors?.address_1 ? errors?.address_1?.[0] : ''"
        :disabled="!editMode || isLoading"
        placeholder="Address line 1"
        @valueChanged="setValue($event, 'address_1')"
        @autocomplete="autocomplete($event)"
        />
      </div>      
      <div class="fv-row mb-7">
        <label class="fw-semibold fs-6 mb-2">Address Line 2</label>
        <input 
        v-model="addressDetails.address_2"
        name="address_2"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.address_2}" 
        placeholder="Address Line 2"/>
        <div 
        :v-if="errors?.address_2"
        class="invalid-feedback">{{ errors?.address_2?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">City</label>
        <input 
        v-model="addressDetails.city"
        name="city"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.city}" 
        placeholder="City"/>
        <div 
        :v-if="errors?.city"
        class="invalid-feedback">{{ errors?.city?.[0] }}</div>
      </div>
      <div class="row mb-7">
        <div class="col-md-6">
          <label class="required fw-semibold fs-6 mb-2">State</label>
          <select
          v-model="addressDetails.state"
          :disabled="!editMode || isLoading" 
          name="state"
          :class="{'is-invalid': errors?.state}"
          class="form-select fw-bold">
            <option value="" selected disabled>Select State</option>
            <option v-for="state in states" :value="state.abbreviation">{{ state.name }}</option>
          </select>
          <div 
          :v-if="errors?.state"
          class="invalid-feedback">{{ errors?.state?.[0] }}</div>
        </div>
        <div class="col-md-6">
          <label class="required fw-semibold fs-6 mb-2">Zip</label>
          <input 
          v-model="addressDetails.zip"
          name="zip"
          :disabled="!editMode || isLoading"
          class="form-control mb-3 mb-lg-0"
          :class="{'is-invalid': errors?.zip}" 
          placeholder="Zip"/>
          <div 
          :v-if="errors?.zip"
          class="invalid-feedback">{{ errors?.zip?.[0] }}</div>
        </div>
      </div>
    </div>
    <div class="text-center pt-15">
      <button 
      :disabled="isLoading"          
      type="reset" 
      @click="cancel()"          
      class="btn btn-light me-3">Cancel</button>

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
import AutoCompleteAddress from "Components/inputs/AutoCompleteAddress.vue";

export default {
  name: "blacklisted-address-view",

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
  },

  components: {
    AutoCompleteAddress,
  },
  
  data: () => ({
    addressDetails: {
      address_1: "",
      address_2: "",
      city: "",
      state: "",
      zip: ""
    },
    
    editMode: true,
    isLoading: false,
    accessType: "add",

    submitUrl: "",
    
    initState: false
  }),

  mounted() {
    this.submitUrl = this.storeUrl;
  },

  mixins: [ResponseMixins, StatesMixins],

  methods: {

    init() {
      this.initState = false;
      this.$nextTick(() => {
        this.initState = true;
      });
    },
    
    fetch(data) {
      this.isLoading = true;
      axios.get(data.find_url)
        .then((res) => {
          this.addressDetails = res.data.address;
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.isLoading = false;
        });
    },

    setAccessType(type) {
      this.init();
      this.resetErrors();
      this.resetFields();
      this.accessType = type;
      switch(type) {
        case "add":
          this.resetFields();
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
          Swal.fire(`Blacklisted Address ${action}`, `Blacklisted Address has been ${action}.`, "success");
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
      this.resetFields();
    },

    resetFields() {
      this.addressDetails = {
        address_1: "",
        address_2: "",
        city: "",
        state: "",
        zip: ""
      };
    },

    setValue(value, model) {
      this.addressDetails[model] = value;
    },

    autocomplete(value) {
      this.addressDetails.city = value.city;
      this.addressDetails.state = value.state;
      this.addressDetails.zip = value.zip;
      setTimeout(() => {
        this.addressDetails.address_1 = value.shortAddress;
			}, 100);
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