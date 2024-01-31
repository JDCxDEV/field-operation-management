<template>
<vue-google-autocomplete
    ref="address"
    :key="name"
    :id="name"
    :country="country"
    :classname="hasError ? 'form-control is-invalid': 'form-control'"
    :name="name"
    :placeholder="placeholder"
    :disabled="disabled"
    @placechanged="getAddressData"
    @inputChange="checkInput"
>
</vue-google-autocomplete>
<div :v-if="hasError" class="invalid-feedback">{{ errorMessage }}</div>
</template>
<script>
import VueGoogleAutocomplete from "vue-google-autocomplete";
export default {
    name: "auto-complete-address",

    props: {
        address: {
            type: String,
            default: ""
        },
        placeholder: {
            type: String,
            default: ""
        },
        name: {
            type: String,
            default: "address"
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
        country: {
            type: String,
            default: "us"
        },
    },

    watch: {
        address() {
            this.$refs.address.autocompleteText = this.address;    
        },
    },
    
    mounted() {
        this.$refs.address.autocompleteText = this.address;
    },
    
    components: { VueGoogleAutocomplete },  

    methods: {
        getAddressData(addressData, placeResultData, id) {
            const address = {
                city: addressData.locality,
                zip: addressData.postal_code,
                state: addressData.administrative_area_level_1,
                fullAddress: placeResultData.formatted_address,
                shortAddress: `${addressData.street_number ? `${addressData.street_number} `  : "" }${addressData.route}`          
            }
            this.$emit("autocomplete", address);
        },

        checkInput(data) {
            if(typeof data.newVal == "string") {
                this.$emit("valueChanged", data.newVal);
            }
        }
    }

}
</script>