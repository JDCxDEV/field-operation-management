<template>
<a 
@click="onUpdateMarket()"
href="#" class="btn btn-sm btn-primary me-2">Update</a>

<!--begin::Menu-->
<div class="me-0">
	<button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary" data-kt-menu-trigger="click"
		data-kt-menu-placement="bottom-end">
		<i class="bi bi-three-dots fs-3"></i>
	</button>

	<!--begin::Menu 3-->
	<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
		data-kt-menu="true" style="">
		<!--begin::Heading-->
		<div class="menu-item px-3">
			<div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
				Options
			</div>
		</div>
		<!--end::Heading-->

		<!--begin::Menu item-->
		<div class="menu-item px-3">
			<a 
			@click="onAddUser()"
			href="#" class="menu-link px-3">
				Add User
			</a>
		</div>
		<!--end::Menu item-->
	</div>
	<!--end::Menu 3-->
</div>
<!--end::Menu-->
</template>
<script>
export default {
	name: "options",

	methods: {
		onUpdateMarket() {
			this.$emit("updateMarket");
		},
		
		onAddUser() {
			this.$emit("addUser");
		}		
	}
}
</script>