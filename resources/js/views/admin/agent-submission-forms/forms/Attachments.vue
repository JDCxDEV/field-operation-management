<template>
<div class="row">
	<div class="mb-7" v-if="formData?.lead_form_pdf_path">
		<iframe style="width: 100%; height:400px" :src="formData?.lead_form_pdf_path_url"></iframe>
	</div>
	<div 
	v-for="uploaded in uploadedFiles"
	class="col-md-4">
		<label class="fw-semibold fs-6 mb-2"><strong>{{ renderIdType(uploaded.id_type) }}</strong></label>
		<div class="img-container">
			<img 
			class="img-thumbnail"
			style="width: 100%; height: auto"
			v-if="uploaded.file" :src="uploaded.file">
		</div>
	</div>

	<div v-if="!uploadedFiles.length" class="col-md-12 mt-5">
		<div class="text-center">
			<h3 class="text-gray-900">No attachments found.</h3>
		</div>
	</div>
</div>
</template>
<script>
export default {
	name: "attachments",

	props: {
		formData: {
            type: Object,
            default: {}
        },
	},

	data: () => ({
		uploadedFiles: [],
	}),

	mounted() {
		this.uploadedFiles = this.formData.files?.map((file) => {
			return {
				id: file.id,
				id_type: file.id_type,
				file: file.file_path
			};
		});
	},

	methods: {
		renderIdType(idType) {
			let splitted = idType.split("_");
			let type = "";
			if(splitted.length) {
				splitted.forEach((a) => {
					const concatText = a.charAt(0).toUpperCase() + a.slice(1);
					type = type.concat(` ${concatText}`);
				});
			} else {
				type = idType;
			}
			return type;
		}
	}
}
</script>