<template>
<form 
@submit.prevent="onSubmit($event)"
class="form w-100">
	<div class="text-center mb-11">
		<h1 class="text-dark fw-bolder mb-3">Verify Code</h1>
		<span>Please input the code we sent from this {{ type == "sms" ? "phone number" : "email" }} <strong>{{ formattedAuthType }}</strong>.</span>
	</div>

	<div class="fv-row mb-8 fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
		<input 
		v-model="code"
		:class="{'is-invalid': errors?.code}"
        maxlength="6"
		type="text" placeholder="Code" name="code" autocomplete="off" class="form-control bg-transparent" autofocus>
		<div 
        :v-if="errors?.code"
        class="invalid-feedback">{{ errors?.code?.[0] }}</div>
	</div>
	<div class="d-grid mb-10">
		<button 
		:disabled="isLoading || !code"
		type="submit" class="btn btn-primary on">
			<span v-if="!isLoading">Verify</span>
			<span v-if="isLoading">Please wait... 
				<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
			</span>
		</button>
        <a 
		:disabled="isLoading"
        href="/login"
		type="button" class="btn btn-secondary mt-2">
            Back
        </a>
	</div>
</form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
export default {
	name: "verify-code",

	props: {
		validateUrl: {
			type: String,
			default: ""
		},
		type: {
			type: String,
			default: ""
		},
		email: {
			type: String,
			default: ""
		},
		phone: {
			type: String,
			default: ""
		},

		user: {
			type: [String, Number],
			default: ""
		},

		token: {
			type: String,
			default: ""
		}
	},

	computed: {
		formattedAuthType() {
			if(this.type == "sms") {
				const phoneLength = this.phone.length;
				let asterisk = "";
				const loopCount = phoneLength - 3;
				for (let index = 0; index < loopCount; index++) {
					asterisk = asterisk.concat("*");
				}
				const last3digits = this.phone.toString().slice(-3);
				return `${asterisk}${last3digits}`;
			} else {
				return this.email;
			}

		}
	},

	data: () => ({
		code: "",
        isLoading: false
	}),

    mixins: [ ResponseMixins ],

    methods: {
        onSubmit(event) {
            this.resetErrors();
            const data = {
                code: this.code,
                token: this.token,
                user: this.user
            };
            this.isLoading = true;
            axios.post(this.validateUrl, data)
                .then((res) => {
					if(res.data.status === 200) {
						window.location.href = res.data.redirect;
					} else {
                        Swal.fire(res.data.title, res.data.message, "warning");
                    }
                }).catch((error) => {
                    this.parseError(error, null, {});
                }).finally(() => {
                    this.isLoading = false;          
                });
        },
    }
}
</script>