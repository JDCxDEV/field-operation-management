<template>
	<label 
	:class="{'required': fieldRequired}"
	class="fw-semibold fs-6 mb-2">{{ label }}</label>
	<div class="input-group">
    <div class="input-group-prepend">
      <a type="button" style="border-radius: 5px 0px 0px 5px"
			class="btn btn-light">
        <i class="fa-solid fa-dollar-sign"></i>
      </a>
    </div>
    <input 
		v-model="fieldValue"
		@input="inputCurrency"
		@blur="checkInput"
		:disabled="disabled"
		:class="[ inputClass, {'is-invalid': errors} ]"
		:name="name"
		type="text"
		:placeholder="placeholder"
    maxlength="14"
		/>
		<div v-if="append" class="input-group-append">
      <a type="button" style="border-radius: 0px 5px 5px 0px"
			class="btn btn-light">
        {{ append }}
      </a>
    </div>
		<div 
		:v-if="errors"
		class="invalid-feedback">{{ errorMessage }}</div>
	</div>
</template>
<script>
import FormatterMixins from "Mixins/formatter.js";
export default {
	name: "money-field",
	
	props: {

		value: {
			type: String,
			default: ""
		},

		fieldRequired: {
			type: Boolean,
			default: true
		},

		label: {
			type: String,
			default: "Money"
		},

		name: {
			type: String,
			default: ""
		},

		inputClass: {
			type: String,
			default: "form-control"
		},

		errors: {
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

    placeholder: {
      type: String,
      default: ""
    },

		append: {
			type: String,
      default: ""
		}
	},

	watch: {
		value() {
			this.fieldValue = this.value;
		}
	},

	data: () => ({
		fieldValue: "",
		visible: false,
	}),

	mixins: [ FormatterMixins ],

	methods: {
		inputCurrency(event) {
			const value = event.target.value;
			const formattedValue = this.currencyFormat(value);
      this.fieldValue = formattedValue; 
			this.$emit('update:value', formattedValue);
		},

		checkInput() {
			let value = this.fieldValue;
			value = value.replace(/\D/g, "");
			const formattedValue = this.currencyFormat(value);
			this.fieldValue = formattedValue;
			this.$emit('update:value', formattedValue);
		}
	}
}
</script>