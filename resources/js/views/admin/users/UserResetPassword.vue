<template>
  <modal
  v-if="displayModal"
  :value="showModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event)"
  v-model="showModal">
  <template v-slot:title>
    Reset Password
  </template>
  <template v-slot:body>
    <form @submit.prevent="onSubmit($event)" class="form">
      <div 
      class="d-flex flex-column scroll-y me-n7 pe-7">
        <div class="fv-row mb-7">
          <label class="required fw-semibold fs-6 mb-2">Password</label>
          <div class="input-group">
            <input
            :type="hide.password ? 'password': 'input'" 
            :class="{'is-invalid': errors?.password}"
            class="form-control form-control-solid mb-3 mb-lg-0" name="password" v-model="credentials.password"/>
            <div class="input-group-append">
              <span 
              @click="hide.password = !hide.password" 
              class="input-group-text bg-primary eye">
                <span
                :class="{'fa fa-eye': hide.password == true, 'fa-solid fa-eye-slash': hide.password == false}"
                class="text-white"></span>
              </span>
            </div>
            <div 
            :v-if="errors?.password"
            class="invalid-feedback">{{ errors?.password?.[0] }}</div>            
          </div>                
        </div>

        <div class="fv-row mb-7">
          <label class="required fw-semibold fs-6 mb-2">Confirm Password</label>
          <div class="input-group">
            <input
            :type="hide.password_confirmation ? 'password': 'input'" 
            :class="{'is-invalid': errors?.password_confirmation}"
            class="form-control form-control-solid mb-3 mb-lg-0" name="password" v-model="credentials.password_confirmation"/>
            <div class="input-group-append">
              <span
              @click="hide.password_confirmation = !hide.password_confirmation" 
              class="input-group-text bg-primary eye">
                <span
                :class="{'fa fa-eye': hide.password_confirmation == true, 'fa-solid fa-eye-slash': hide.password_confirmation == false}"
                class="text-white"></span>
              </span>
            </div>
            <div 
            :v-if="errors?.password_confirmation"
            class="invalid-feedback">{{ errors?.password_confirmation?.[0] }}</div>            
          </div>            
        </div>
      </div>
      <div class="text-center pt-15">
        <button 
        :disabled="isLoading"          
        type="reset" 
        @click="close()"      
        class="btn btn-light me-3">Discard</button>
        
        <button
        :disabled="isLoading || !canSubmit"
        class="btn btn-primary">Submit</button>
      </div>
    </form>
  </template>
</modal>
</template>
<script>

import ResponseMixins from "Mixins/response";

export default {
  name: "user-reset-password",
  
  computed: {
    canSubmit() {
      if(!this.credentials.password || !this.credentials.password_confirmation) {
        return false;
      }

      if(this.credentials.password != this.credentials.password_confirmation) {
        return false;
      }

      return true;
    }
  },

  data: () => ({
    user: {},
    credentials: {
      password: null,
      password_confirmation: null
    },
    showModal: false,
    isLoading: false,

    hide: {
      password: true,
      password_confirmation: true
    },

    displayModal: false
  }),

  mixins: [ ResponseMixins ],

  mounted() {
    setTimeout(() => {
      this.displayModal = true;
    }, 2000);
  },

  methods: {

    onSubmit(event) {
      this.isLoading = true;
      const url = `/users/${this.user.id}/reset-password`;
      axios.post(url, this.credentials)
        .then((res) => {
          Swal.fire("Password Reset", "Password has been reset", "success");
          this.close();
        }).catch((error) => {
          this.parseError(error, null, {});
        }).finally(() => {
          this.isLoading = false;
        });
    },

    setModalStatus(status) {
      this.showModal = status;
      this.hide = {
        password: true,
        password_confirmation: true
      };
    },

    cleanFields() {
      this.credentials = {
        password: null,
        password_confirmation: null
      }
    },
    
    openModal(data) {
      this.user = data;
      this.setModalStatus(true);
      this.cleanFields();
    },

    close() {
      this.user = {};
      this.setModalStatus(false);
      this.cleanFields();
    }    
  
  }
}
</script>
<style>
.eye {
  height: 45px; 
  width: 45px; 
  cursor: pointer;
}
</style>