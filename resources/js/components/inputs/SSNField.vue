<template>
	<label 
	:class="{'required': required}"	
	class="fw-semibold fs-6 mb-2">{{ label }}</label>
	<div class="input-group">
		<input 
		v-model="fieldValue"
		@input="inputSSN"
		@blur="checkInput"
		:disabled="disabled"
		:class="[ inputClass, {'is-invalid': errors} ]"
		:name="name"
		:maxlength="length"
		:type="visible ? 'text' : 'password'"
		placeholder="000-00-0000"
		/>
		<div class="input-group-append">
			<button
			@click="visible = !visible" 
			style="border-radius: 0px 5px 5px 0px"
			class="btn btn-light" type="button">
				<i v-if="visible" class="fa-solid fa-eye"></i>
				<i v-if="!visible" class="fa-solid fa-eye-slash"></i>
			</button>
		</div>
		<div 
		:v-if="errors"
		class="invalid-feedback">{{ errorMessage }}</div>
	</div>
</template>
<script>
import FormatterMixins from "Mixins/formatter.js";
export default {
	name: "ssn-field",
	
	props: {

		value: {
			type: String,
			default: ""
		},

		label: {
			type: String,
			default: "SSN"
		},

		name: {
			type: String,
			default: "ssn"
		},

		length: {
			type: Number,
			default: 11
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
			default: "Phone Number is required"	
		},

		disabled: {
			type: Boolean,
			default: false
		},

		required: {
			type: Boolean,
			default: true
		},
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
		inputSSN(event) {
			const value = event.target.value;
			const formattedValue = this.ssnFormat(value);
            this.fieldValue = formattedValue; 
			this.$emit('update:value', formattedValue);
		},

		checkInput() {
			let value = this.fieldValue;
			value = value.replace(/\D/g, "");
			if(value.length > 9) {
				value = value.slice(0, 9);
			} 
			const formattedValue = this.ssnFormat(value);
			this.fieldValue = formattedValue;
			this.$emit('update:value', formattedValue);
		}
	}
}
</script>