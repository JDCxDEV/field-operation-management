<template>
  <button
    v-if="type == 'button'" 
    class="btn"
    :class="btnClass"
    @click="confirm()"
    :disabled="isLoading"
  >
  <i
  v-if="icon"
  :class="icon"
  ></i>
  {{ btnText }}</button>

  <a
    v-if="type == 'link'" 
    :class="btnClass"
    @click="confirm()"
    :disabled="isLoading"
    style="cursor: pointer"
  >{{ btnText }}</a>
  
</template>
<script>
export default {
  name: "confirm-button",
  
  props: {
    
    type: {
      type: String,
      default: "button"
    },

    icon: {
      type: String,
      default: ""
    },

    btnText: {
      default: "Confirm",
      type: String
    },

    title: {
      default: "Are you sure?",
      type: String
    },

    description: {
      default: "You won't be able to revert this!",
      type: String
    },

    confirmButtonText: {
      default: "Yes",
      type: String
    },

    btnClass: {
      default: "btn-danger",
      type: String
    },

    route: {
      default: "",
      type: String
    }
  },

  data: () => ({
    isLoading: false
  }),
   
  methods: {
    async confirm() {
      Swal.fire({
        title: this.title,
        html: this.description,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: this.confirmButtonText
      }).then(async (result) => {
        if (result.isConfirmed) {
          if(this.route) {
            await this.submit();
          } else {
            this.$emit("confirmed");
          }
        }
      });
    },

    async submit() {
      this.isLoading = true;
      await axios.post(this.route)
        .then((response) => {
          this.$emit("confirmed");
        }).catch((err) => {
          console.log(err);
          Swal.fire("Something went wrong", "Something went wrong, please try again.", "info");
        }).finally(() => {
          this.isLoading = false;
        });
    }
  }
}
</script>