<template>
<form 
@submit.prevent="onSubmit($event)"
class="form w-100">
	<div class="text-center mb-11">
		<h1 class="text-dark fw-bolder mb-3">Sign In</h1>
	</div>

	<div class="fv-row mb-8 fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
		<input 
		v-model="email"
		:class="{'is-invalid': errors?.email}"
		type="text" placeholder="Email" name="email" autocomplete="off" class="form-control bg-transparent" autofocus>
		<div 
        :v-if="errors?.email"
        class="invalid-feedback">{{ errors?.email?.[0] }}</div>
	</div>
	<div class="fv-row mb-3 fv-plugins-icon-container fv-plugins-bootstrap5-row-valid">
		<input 
		v-model="password"
		:class="{'is-invalid': errors?.password}"
		type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent">
		<div 
        :v-if="errors?.password"
        class="invalid-feedback">{{ errors?.password?.[0] }}</div>
	</div>
	<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
		<div></div>
		<a :href="forgotPasswordUrl" class="link-primary">Forgot Password ?</a>
	</div>
	<div class="d-grid mb-10">
		<button 
		:disabled="isLoading"
		type="submit" class="btn btn-primary on">
			<span v-if="!isLoading">Continue</span>
			<span v-if="isLoading">Please wait... 
				<span class="spinner-border spinner-border-sm align-middle ms-2"></span>
			</span>
		</button>
	</div>
</form>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
export default {
	name: "login",
	props: {

		loginUrl: {
			type: String,
			default: ""
		},

		forgotPasswordUrl: {
			type: String,
			default: ""
		}
	},

	data: () => ({
		email: "",
		password: "",

		isLoading: false
	}),

	mounted() {
		// this.firebaseRequestPermission();
	},

	mixins: [ ResponseMixins ],

	methods: {

		onSubmit(event) {
            this.resetErrors();
            let form = event.target;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);

            this.isLoading = true;
            axios.post(this.loginUrl, params)
                .then((res) => {
					if(res.data.status === 200) {
						window.location.href = res.data.redirect;
					}
                }).catch((error) => {
                    this.parseError(error, null, {});
                }).finally(() => {
                    this.isLoading = false;          
                });
        },

		// firebaseRequestPermission() {
		// 	window.messaging.requestPermission()
		// 		.then(function () {
		// 			return messaging.getToken();
		// 		}).then(function (response) {
		// 			console.log(response);
		// 		});
		// }
	}
}
</script>