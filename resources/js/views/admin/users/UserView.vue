<template>  
  <modal 
  :value="showModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event)"
  v-model="showModal">
    <template v-slot:title>
      {{ accessType }} User
    </template>

    <template v-slot:body>
      <form @submit.prevent="onSubmit($event)" class="form">
        <div 
        style="height: 63vh"
        class="d-flex flex-column scroll-y me-n2 pe-2">
          <div class="fv-row mb-7 text-center mt-5">
            <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
              <div id="imagePreview" class="image-input-wrapper w-100px h-100px" :style="{'background-image': 'url(' + renderPreviewUrl + ')'}"></div>
              <label 
              v-if="editMode"
              class="btn btn-icon btn-circle btn-active-color-primary w-20px h-20px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                <i class="bi bi-pencil-fill fs-7"></i>
                <input 
                @change="avatarUploaded($event)"
                type="file" name="avatar" accept=".png, .jpg, .jpeg" />
              </label>
            </div>
            <div 
            v-if="editMode"
            class="form-text">Allowed file types: png, jpg, jpeg.</div>
          </div>

          <div class="row mb-5">
            <div class="col-md-6">
              <label class="fw-semibold fs-6 mb-2">Company</label>
              <select
              @change="fetchMarkets()"
              :disabled="!editMode || companyCoordinator != '' || companyId != ''" 
              name="company_id"
              v-model="user.info.company_id"
              :class="{'is-invalid': errors?.company_id}"
              class="form-select fw-bold mb-3 mb-lg-0">
                <option v-for="company in companies" :value="company.id">{{ company.company }}</option>
              </select>
              <div 
              :v-if="errors?.company_id"
              class="invalid-feedback">{{ errors?.company_id?.[0] }}</div>
            </div>
            <div class="col-md-6">
              <label class="required fw-semibold fs-6 mb-2">Market</label>
              <select
              :disabled="(!editMode || marketId != '') && companyCoordinator == ''" 
              name="market_id"
              v-model="user.info.market_id"
              :class="{'is-invalid': errors?.market_id}"
              class="form-select fw-bold mb-3 mb-lg-0">
                <option v-for="market in markets" :value="market.id">{{ market.market_name }}</option>
              </select>
              <div 
              :v-if="errors?.market_id"
              class="invalid-feedback">{{ errors?.market_id?.[0] }}</div>
            </div>
          </div>

          <div class="row mb-5">
            <div class="col-md-6">
              <label class="required fw-semibold fs-6 mb-2">First Name</label>
              <input 
              :disabled="!editMode"
              :class="{'is-invalid': errors?.first_name}"
              class="form-control mb-3 mb-lg-0" name="first_name" v-model="user.first_name"/>
              <div 
              :v-if="errors?.first_name"
              class="invalid-feedback">{{ errors?.first_name?.[0] }}</div>
            </div>

            <div class="col-md-6">
              <label class="required fw-semibold fs-6 mb-2">Last Name</label>
              <input 
              :disabled="!editMode"
              :class="{'is-invalid': errors?.last_name}"
              class="form-control mb-3 mb-lg-0" name="last_name" v-model="user.last_name"/>
              <div 
              :v-if="errors?.last_name"
              class="invalid-feedback">{{ errors?.last_name?.[0] }}</div>  
            </div>
          </div>

          <div class="row mb-5">
            <div class="col-md-6">
              <label class="required fw-semibold fs-6 mb-2">Email</label>
              <input 
              :disabled="accessType != 'Add'"
              :class="{'is-invalid': errors?.email}"
              class="form-control mb-3 mb-lg-0" name="email" v-model="user.email"/>
              <div 
              :v-if="errors?.email"
              class="invalid-feedback">{{ errors?.email?.[0] }}</div>
            </div>
            <div class="col-md-6">
              <phone-number
              :value="user.info.phone"
              label="Phone Number"
              :disabled="!editMode"
              :errors="errors?.phone ? true: false"
              :error-message="errors?.phone?.[0]"
              length="14"
              @update:value="newValue => user.info.phone = newValue"
              ></phone-number>
            </div>
          </div>

          <div 
          v-if="!user.id"
          class="btn-toolbar mb-5" role="toolbar">
            <div class="btn-group me-2 col-12" role="group" aria-label="First group">
              <button
              @click="setNotify('sms')" 
              :class="{'btn-primary': !notifyByEmail, 'btn-secondary': notifyByEmail}"
              type="button" class="btn" style="width: 49%">Notify thru SMS</button>
              <button
              @click="setNotify('email')"                
              :class="{'btn-primary': notifyByEmail, 'btn-secondary': !notifyByEmail}"
              type="button" class="btn" style="width: 49%">Notify thru Email</button>
            </div>
          </div>
        </div>

        <div class="text-center pt-10">
          <button 
          :disabled="isLoading"          
          type="reset" 
          @click="close()"          
          class="btn btn-light me-3">Discard</button>
          
          <button
          v-if="editMode" 
          :disabled="isLoading"
          class="btn btn-primary">Submit</button>

          <button
          v-if="!editMode && !hideEdit"
          type="button"
          @click="setToEditMode()"
          class="btn btn-warning">Edit</button>          
        </div>
      </form>
    </template>
  </modal>
</template>
<script>

import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
  name: "user-view",

  props: {
    storeUrl: {
      type: String,
      default: ""
    },

    companiesFetchUrl: {
      type: String,
      default: ""
    },

    marketsFetchUrl: {
      type: String,
      default: ""
    },

    hideEdit: {
      type: Boolean,
      default: false
    },
    
    companyCoordinator: {
      type: Number,
      default: ""
		},

    companyId: {
      type: Number,
      default: ""
		},

    marketDirector: {
      type: Number,
      default: ""
		},

    marketId: {
      type: Number,
      default: ""
		},

    blankImage: {
      type: String,
      default: ""
    }
  },

  components: {
    PhoneNumber
  },  

  computed: {
    renderPreviewUrl() {
      if(this.previewUrl) {
        return this.previewUrl;
      } else if (this.user?.info?.avatar_url) {
        return this.user?.info?.avatar_url;
      } else {
        return this.blankImage;
      }
    },

    notifyByEmail() {
      return this.user.notify == "email";
    }
  },

  data: () => ({
    showModal: false,
    accessType: "View",
    isLoading: false,

    editMode: false,

    companies: [],
    markets: [],

    user: {
      first_name: "",
      last_name: "",
      email: "",
      info: {
        company_id: "",
        market_id: "",
        phone: "",
        website: "",
      },
      notify: "email"
    },

    submitUrl: "",
    previewUrl: ""
  }),

  mounted() {
    this.fetchCompanies();
    if(this.companyCoordinator || this.companyId) {
      this.user.info.company_id = this.companyId;
      this.fetchMarkets();
    }
    if(this.marketId) {
      this.user.info.market_id = this.marketId;
    } 
  },

  mixins: [ResponseMixins, FormatterMixins],

  methods: {
    
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
        const route = `${this.marketsFetchUrl}?company_id=${this.user.info.company_id}`
        axios.get(route)
        .then((res) => {
          this.markets = res.data.markets;
          if(this.markets.length === 1) {
            this.user.info.market_id = this.markets[0].id;
          }
        }).catch((err) => {
          console.log(err);
        });
      }
    },

    open(type, data) {
      if(type == "view") {
        this.editMode = false;
        this.accessType = "View";
      } else {
        this.editMode = true;
        this.accessType = type == "add" ? "Add" : "Update";
      }
      if(data) {
        this.user = data;
        this.user.info.phone = this.phoneFormat(data.info.phone); 
        this.submitUrl = data.update_url;
        this.fetchMarkets();
      } else {
        this.user = {
          first_name: "",
          last_name: "",
          email: "",
          info: {
            company_id: this.companyId ? this.companyId : "",
            market_id: this.marketId ? this.marketId : "",
            phone: "",
            website: ""        
          },
          notify: "email"
        }
        this.submitUrl = this.storeUrl;
        if(this.companyId) {
          this.fetchMarkets();
        }
      }
      this.showModal = true;
      this.previewUrl = "";
      this.resetErrors();
    },

    setModalStatus(status) {
      this.showModal = status;
    },

    setToEditMode() {
      this.editMode = true;
      this.accessType = "Update";
    },

    close() {
      this.showModal = false;
    },

    onSubmit(event) {
      let form = event.target;
      if (!form) {
        form = event;
      }
      const params = new FormData(form);
      params.append("phone", this.clean(this.user.info.phone));

      if(this.companyCoordinator || this.companyId) {
        params.append("company_id", this.companyId);
      }
      
      if(this.marketId) {
        params.append("market_id", this.marketId);
      }

      if(!this.user.id) {
        params.append("notify", this.user.notify);
      }

      this.isLoading = true;
      axios.post(this.submitUrl, params)
        .then((res) => {
          Swal.fire(`User ${this.accessType}`, `${res.data.message}`, "success");
          if(this.accessType == "Add") {
            this.close();
          }
          this.$emit("updated");
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.isLoading = false;          
        });
    },

    avatarUploaded(event) {
      const reader = new FileReader();
      reader.onload = () => {
        this.previewUrl = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);      
    },

    setNotify(type) {
      this.user.notify = type;
    }
  }
}
</script>