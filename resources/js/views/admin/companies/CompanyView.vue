<template>
  <div 
  v-if="companyDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Deactivated company</span>
  </div>
  <form 
  ref="address"
  @submit.prevent="onSubmit($event)" class="form">
    <div 
    style="height: 60vh"
    class="d-flex scroll-y flex-column me-n7 pe-7">
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Name</label>
        <input 
        v-model="companyDetails.company"
        name="company"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.company}"  
        placeholder="Name"/>
        <div 
        :v-if="errors?.company"
        class="invalid-feedback">{{ errors?.company?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="fw-semibold fs-6 mb-2">Company Coordinators</label>
        <search-select
        ref="searchSelect"
        :fetch-url="fetchUserUrl"
        label="name"
        value="id"
        :disabled="!editMode || isLoading"
        :preselected="companyDetails.coordinators"
        placeholder="Search user"
        @change="updateCoordinators"
        />
      </div>
      <div class="fv-row mb-7">
        <phone-number
        :value="companyDetails.phone"
        label="Phone Number"
        :disabled="!editMode || isLoading"
        :errors="errors?.phone ? true: false"
        :error-message="errors?.phone?.[0]"
        length="14"
        placeholder="Company phone number"
        @update:value="newValue => companyDetails.phone = newValue"
        ></phone-number>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Address Line 1</label>
        <auto-complete-address
        v-if="initState" 
        :address="companyDetails.address_1"
        name="address_1"
        :has-error="errors?.address_1"
        :error-message="errors?.address_1 ? errors?.address_1?.[0] : ''"
        :disabled="!editMode || isLoading"
        placeholder="Company address line 1"
        @valueChanged="setValue($event, 'address_1')"
        @autocomplete="autocomplete($event)"
        />
      </div>      
      <div class="fv-row mb-7">
        <label class="fw-semibold fs-6 mb-2">Address Line 2</label>
        <input 
        v-model="companyDetails.address_2"
        name="address_2"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.address_2}" 
        placeholder="Company address line 2"/>
        <div 
        :v-if="errors?.address_2"
        class="invalid-feedback">{{ errors?.address_2?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">City</label>
        <input 
        v-model="companyDetails.city"
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
          v-model="companyDetails.state"
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
          v-model="companyDetails.zip"
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
      class="btn btn-light me-3">Discard</button>

      <button
      v-if="editMode" 
      :disabled="isLoading"
      class="btn btn-primary">Submit</button>

      <button
      v-if="!editMode"
      type="button"
      @click="setToEditMode()"
      class="btn btn-warning">Edit</button>
    </div>    
  </form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import StatesMixins from "Mixins/states.js";
import AutoCompleteAddress from "Components/inputs/AutoCompleteAddress.vue";
import SearchSelect from "Components/inputs/SearchSelect";
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
  name: "company-view",

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
    childComponent: {
      type: Boolean,
      default: false
    }
  },

  components: {
    AutoCompleteAddress,
    SearchSelect,
    PhoneNumber
  },

  data: () => ({
    companyDetails: {
      company: "",
      phone: "",
      address_1: "",
      address_2: "",
      city: "",
      state: "",
      zip: "",
      coordinators: []
    },
    coordinators: [],

    editMode: true,
    isLoading: false,
    accessType: "add",

    submitUrl: "",
    users: [],
    fetchUsersLoad: false,

    fetchUserUrl: "",

    initState: false    
  }),

  mounted() {
    this.submitUrl = this.storeUrl;
    this.setFetchUsersUrl();
  },

  mixins: [ResponseMixins, FormatterMixins, StatesMixins],

  methods: {
    
    init() {
      this.initState = false;
      this.$nextTick(() => {
        this.initState  =true;
      });
    },    

    setFetchUsersUrl() {
      this.fetchUserUrl = this.companyDetails.id ? `users/fetch?nopaginate=true&status=active&company=${this.companyDetails.id}` : "users/fetch?nopaginate=true&status=active&nocompany=true";
    },

    fetch(data) {
      this.isLoading = true;
      axios.get(data.find_url)
        .then((res) => {
          this.companyDetails = res.data.company;
          this.companyDetails.phone = this.phoneFormat(res.data.company.phone);
          this.companyDetails.coordinators = res.data.company.coordinators.map((e) => {
            return { name: e.name, id: e.id };
          });
          this.coordinators = this.companyDetails.coordinators;
          this.submitUrl = data.update_url;
          this.setFetchUsersUrl();
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.isLoading = false;
        });
    },

    setAccessType(type) {
      this.resetErrors();
      this.resetFields();
      this.init();
      this.accessType = type;
      this.users = [];
      this.coordinators = [];
      this.$refs.searchSelect.refresh();
      switch(type) {
        case "add":
          this.resetFields();
          this.editMode = true;
          this.submitUrl = this.storeUrl;
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
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);
      const ids = this.coordinators.map((item) => { return item.id });
      params.append("coordinators", ids);
      params.append("phone", this.clean(this.companyDetails.phone));

      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`Company ${action}`, `Company has been ${action}.`, "success");
          if(this.childComponent) {
            this.$emit("cancel");
            this.$emit("updated");
          }
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
      if(this.accessType == "add" && !this.childComponent) {
        window.history.back();
      } else {
        this.$emit("cancel")
      }
    },

    cleanFields() {
      if(this.accessType != "add") return;
      this.resetFields();
    },

    resetFields() {
      this.companyDetails = {
        company: "",
        phone: "",
        address_1: "",        
        address_2: "",
        city: "",
        state: "",
        zip: "",
        coordinators: []
      }
    },

    setValue(value, model) {
      this.companyDetails[model] = value;
    },

    autocomplete(value) {
      this.companyDetails.city = value.city;
      this.companyDetails.state = value.state;
      this.companyDetails.zip = value.zip;
      setTimeout(() => {
        this.companyDetails.address_1 = value.shortAddress;
      }, 100);
    },

    updateCoordinators(data) {
      this.coordinators = data;
    }
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