<template>
	<label
	v-if="label" 
	:class="{'required': required}"
	class="fw-semibold fs-6 mb-2">{{ label }}</label>
	<input
	v-model="fieldValue"
	@input="inputContactNumber"
	@blur="checkInput"
	:disabled="disabled"
	:class="[inputClass, {'is-invalid': errors}]"  
	:placeholder="placeholder || ''"
	:maxlength="length"
	autocomplete="off"
	/>
	<div 
	:v-if="errors"
	class="invalid-feedback">{{ errorMessage }}</div>
</template>
<script>
import FormatterMixin from "Mixins/formatter.js";

export default {
	name: "phone-number",

	props: {
		value: {
			type: String,
			default: ""
		},

		label: {
			type: String,
			default: ""
		},

		placeholder: {
			type: String,
			default: ""
		},

		length: {
			type: Number,
			default: 14			
		},

		disabled: {
			type: Boolean,
			default: false			
		},

		errors: {
			type: Boolean,
			default: false		
		},

		errorMessage: {
			type: String,
			default: "Phone Number is required"	
		},
		
		required: {
			type: Boolean,
			default: true
		},

		inputClass: {
			type: String,
			default: "form-control mb-3 mb-lg-0"
		}
		
	},

	watch: {
		value() {
			this.fieldValue = this.value;
		}
	},

	data: () => ({
		fieldValue: "",
	}),

	mixins: [ FormatterMixin ],

	mounted() {
		if(this.value) {
			this.fieldValue = this.value;
		}
	},

	methods: {
		inputContactNumber(event) {
			const value = event.target.value;
			const formattedValue = this.phoneFormat(value);
			this.fieldValue = formattedValue;
			this.$emit('update:value', formattedValue);
		},

		checkInput() {
			let value = this.fieldValue;
			value = value.replace(/\D/g, "");
			if(value.length > 10) {
				value = value.slice(0, 10);
			} 
			const formattedValue = this.phoneFormat(value);
			this.fieldValue = formattedValue;
			this.$emit('update:value', formattedValue);
		}

	}
}
</script>