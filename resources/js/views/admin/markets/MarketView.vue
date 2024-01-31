<template>
  <div 
  v-if="marketDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Deactivated Market</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div 
    style="height: 60vh"
    class="d-flex flex-column scroll-y me-n7 pe-7">
      <div 
      v-if="!companyId"
      class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Company</label>
        <select
        :disabled="!editMode || marketDetails.id || isLoading" 
        name="company_id"
        v-model="marketDetails.company_id"
        :class="{'is-invalid': errors?.company_id}"
        class="form-select fw-bold mb-3 mb-lg-0">
          <option v-for="company in companies" :value="company.id" :selected="company.selected">{{ company.company }}</option>
        </select>
        <div 
        :v-if="errors?.company_id"
        class="invalid-feedback">{{ errors?.company_id?.[0] }}</div>  
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Name</label>
        <input 
        v-model="marketDetails.market_name"
        name="market_name"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.market_name}"  
        placeholder="Name"/>
        <div 
        :v-if="errors?.market_name"
        class="invalid-feedback">{{ errors?.market_name?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Email</label>
        <input 
        v-model="marketDetails.email"
        name="email"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.email}" 
        placeholder="Email address"/>
        <div 
        :v-if="errors?.email"
        class="invalid-feedback">{{ errors?.email?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <phone-number
        :value="marketDetails.phone"
        label="Phone Number"
        :disabled="!editMode || isLoading"
        :errors="errors?.phone ? true: false"
        :error-message="errors?.phone?.[0]"
        length="14"
        placeholder="Market phone number"
        @update:value="newValue => marketDetails.phone = newValue"
        ></phone-number>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Address Line 1</label>
        <auto-complete-address
        v-if="initState"
        :address="marketDetails.address_1"
        name="address_1"
        :has-error="errors?.address_1"
        :error-message="errors?.address_1 ? errors?.address_1?.[0] : ''"
        :disabled="!editMode || isLoading"
        placeholder="Market address line 1"
        @valueChanged="setValue($event, 'address_1')"
        @autocomplete="autocomplete($event)"
        />
      </div>      
      <div class="fv-row mb-7">
        <label class="fw-semibold fs-6 mb-2">Address Line 2</label>
        <input 
        v-model="marketDetails.address_2"
        name="address_2"
        :disabled="!editMode || isLoading"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.address_2}" 
        placeholder="Market address line 2"/>
        <div 
        :v-if="errors?.address_2"
        class="invalid-feedback">{{ errors?.address_2?.[0] }}</div>
      </div>
      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">City</label>
        <input 
        v-model="marketDetails.city"
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
          v-model="marketDetails.state"
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
          v-model="marketDetails.zip"
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
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
  name: "market-view",

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
    
    companiesFetchUrl: {
      type: String,
      default: ""
    },
    
    childComponent: {
      type: Boolean,
      default: false
    },

    companyId: {
      type: [String, Number],
      default: ""      
    }
  },

  components: {
    AutoCompleteAddress,
    PhoneNumber
  },

  data: () => ({
    marketDetails: {
      company_id: "",
      market_name: "",
      email: "",
      phone: "",
      address_1: "",
      address_2: "",
      city: "",
      state: "",
      zip: ""
    },
    companies: [],
    editMode: true,
    isLoading: false,
    accessType: "add",

    submitUrl: "",

    initState: false
  }),

  mounted() {
    this.submitUrl = this.storeUrl;
    this.fetchCompanies();
  },

  mixins: [ResponseMixins, FormatterMixins, StatesMixins],

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
          this.marketDetails = res.data.market;
          this.marketDetails.phone = this.phoneFormat(res.data.market.phone);
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.isLoading = false;
        });
    },

    fetchCompanies() {
      if(this.companiesFetchUrl) {
        axios.get(this.companiesFetchUrl)
        .then((res) => {
          this.companies = res.data.companies;
        }).catch((err) => {
          console.log(err);
        });
      }
    },

    setAccessType(type) {
      this.resetErrors();
      this.resetFields();
      this.init();
      this.accessType = type;
      switch(type) {
        case "add":
          
          this.submitUrl = this.storeUrl;
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
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);
      params.append("company_id", this.companyId ? this.companyId : this.marketDetails.company_id);
      params.append("phone", this.clean(this.marketDetails.phone));

      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`Market ${action}`, `Market has been ${action}.`, "success");
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
        this.$emit("cancel");
      }
    },

    cleanFields() {
      if(this.accessType != "add") return;
      this.resetFields();
    },
    
    resetFields() {
      this.marketDetails = {
        company_id: "",
        market_name: "",
        email: "",
        phone: "",
        address_1: "",
        address_2: "",
        city: "",
        state: "",
        zip: ""
      };
    },

    setValue(value, model) {
      this.marketDetails[model] = value;
    },

    autocomplete(value) {
      this.marketDetails.city = value.city;
      this.marketDetails.state = value.state;
      this.marketDetails.zip = value.zip;
      setTimeout(() => {
        this.marketDetails.address_1 = value.shortAddress;
      }, 100);  
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