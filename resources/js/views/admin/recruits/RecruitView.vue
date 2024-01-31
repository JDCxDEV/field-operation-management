<template>
  <div 
  v-if="recruitDetails.deleted_at"
  class="mt-2 mb-5">
    <span 
    class="badge badge-danger">Archived Recruit</span>
  </div>
  <form @submit.prevent="onSubmit($event)" class="form">
    <div class="d-flex flex-column me-n7 pe-7">

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Company</label>
        <select
        @change="fetchMarkets()"
        :disabled="!editMode" 
        name="company_id"
        v-model="recruitDetails.company_id"
        :class="{'is-invalid': errors?.company_id}"
        class="form-select fw-bold mb-3 mb-lg-0">
          <option v-for="company in companies" :value="company.id">{{ company.company }}</option>
        </select>
        <div 
        :v-if="errors?.company_id"
        class="invalid-feedback">{{ errors?.company_id?.[0] }}</div>            
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Market</label>
        <select
        :disabled="!editMode || !markets.length"
        name="market_id"
        v-model="recruitDetails.market_id"
        :class="{'is-invalid': errors?.market_id}"
        class="form-select fw-bold mb-3 mb-lg-0">
          <option v-for="market in markets" :value="market.id">{{ market.market_name }}</option>
        </select>
        <div 
        :v-if="errors?.market_id"
        class="invalid-feedback">{{ errors?.market_id?.[0] }}</div>            
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Name</label>
        <input 
        v-model="recruitDetails.name"
        name="name"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.name}"  
        placeholder="Name"/>
        <div 
        :v-if="errors?.name"
        class="invalid-feedback">{{ errors?.name?.[0] }}</div>
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Phone Number</label>
        <input
        @input="inputContactNumber()" 
        v-model="recruitDetails.phone"
        name="phone"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.phone}"  
        placeholder="Phone Number"
        maxlength="14"/>
        <div 
        :v-if="errors?.phone"
        class="invalid-feedback">{{ errors?.phone?.[0] }}</div>
      </div>

      <div class="fv-row mb-7">
        <label class="required fw-semibold fs-6 mb-2">Email</label>
        <input 
        v-model="recruitDetails.email"
        name="email"
        :disabled="!editMode"
        class="form-control mb-3 mb-lg-0"
        :class="{'is-invalid': errors?.email}"  
        placeholder="Email"/>
        <div 
        :v-if="errors?.email"
        class="invalid-feedback">{{ errors?.email?.[0] }}</div>
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
import FormatterMixins from "Mixins/formatter.js";
export default {
  name: "recruit-view",

  props: {
    storeUrl: {
      type: String, 
      default: null
    },
    childComponent: {
      type: Boolean,
      default: false
    },

    companiesFetchUrl: {
      type: String,
      default: ""
    },

    marketsFetchUrl: {
      type: String,
      default: ""
    },

  },

  data: () => ({
    recruitDetails: {
      name: "",
      phone: "",
      email: "",
      company_id: "",
      market_id: ""
    },
    
    companies: [],
    markets: [],

    editMode: true,
    isLoading: false,
    accessType: "add",

    submitUrl: ""
  }),

  mounted() {
    this.fetchCompanies();
    this.submitUrl = this.storeUrl;
  },

  mixins: [ResponseMixins, FormatterMixins],

  methods: {
    
    fetch(data) {
      this.editMode = false;
      axios.get(data.find_url)
        .then((res) => {
          this.recruitDetails = res.data.recruit;
          this.recruitDetails.phone = this.phoneFormat(res.data.recruit.phone);
          this.submitUrl = data.update_url;
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          if(this.accessType == "edit") {
            this.editMode = true;
          }
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

    fetchMarkets() {
      if(this.marketsFetchUrl) {
        const route = `${this.marketsFetchUrl}?company_id=${this.recruitDetails.company_id}`
        axios.get(route)
        .then((res) => {
          this.markets = res.data.markets;
        }).catch((err) => {
          console.log(err);
        });
      }
    },

    setAccessType(type) {
      this.resetErrors();
      this.accessType = type;
      switch(type) {
        case "add":
          this.recruitDetails = {
            name: "",
            phone: "",
            email: "",
            company_id: "",
            market_id: ""
          },
          this.editMode = true;
          break;
        case "edit":
          this.editMode = true;
          this.fetchMarkets();
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
      params.append("phone", this.clean(this.recruitDetails.phone));

      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          const action = this.accessType == "add" ? "created" : "updated"; 
          Swal.fire(`Recruit ${action}`, `Recruit has been ${action}.`, "success");
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
      this.marketDetails = {
        market_name: "",
      }
    },

    inputContactNumber() {
      const value = this.recruitDetails.phone;
      const formattedValue = this.phoneFormat(value);
      this.recruitDetails.phone = formattedValue;
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