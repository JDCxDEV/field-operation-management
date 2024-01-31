<template>
<div>
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-0 pb-0">

			<!--begin::Navs-->
            <div class="d-flex overflow-auto h-55px">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder flex-nowrap">
                    <!--begin::Nav item-->
                        <li 
                        v-for="tab in tabs"
                        class="nav-item">
                            <a 
                            @click="selectTab(tab)"
                            :class="{'active': tab.key == currentTab}"
                            href="javascript:void(0)"
                            class="tab-item-s nav-link text-active-primary me-6">
                                {{ tab.name }}
                            </a>
                        </li>
                    <!--end::Nav item-->
                </ul>
            </div>
            <!--begin::Navs-->
		</div>
	</div>
</div>
<div v-show="currentTab == 'blacklisted-address'">
	<blacklisted-address-table 
	:fetch-url="addressFetchUrl"
	:store-url="addressStoreUrl"	
	></blacklisted-address-table>
</div>

<div v-show="currentTab == 'statuses'">
	<status-table 
	:fetch-url="statusFetchUrl"
	:store-url="statusStoreUrl"	
	></status-table>
</div>

<div v-show="currentTab == 'templates'">
	<div>
		<div class="card mb-5 mb-xl-10">
			<div class="card-body pt-6 pb-6">	
				<form-template-view
				ref="formTemplate"
				@updated="fetch()"
				hide-discard
				></form-template-view>
			</div>
		</div>
	</div>
</div>
</template>

<script>
import ResponseMixins from "Mixins/response.js";

export default {
	name: "form-template-overview",
	
	props: {
		addressFetchUrl: {
			type: String,
			default: ""
		},
		
		addressStoreUrl: {
			type: String,
			default: ""
		},

		statusFetchUrl: {
			type: String,
			default: ""
		},
		
		statusStoreUrl: {
			type: String,
			default: ""
		},
	},

	data: () => ({
		tabs: [
            { name: "Templates", key: "templates" },
            { name: "Blacklisted Addresses", key: "blacklisted-address" },
            { name: "Statuses", key: "statuses" },			            
        ],
		currentTab: "templates",
		isLoading: false,
		template: "",
	}),

	mounted() {
		this.fetch();
	},

	mixins: [ ResponseMixins ],

	methods: {

		fetch() {
			this.isLoading = true;
			axios.get("/form-templates/fetch-active")
				.then((res) => {
					this.template = res.data.template;
					this.$refs.formTemplate.setAccessType("edit");      
					this.$refs.formTemplate.fetch(this.template);
				}).finally(() => {
					this.isLoading = false;
				});
		},
		
        selectTab(tab) {
            this.$nextTick(() => {
                this.currentTab = tab.key
            });
        },			
	}
}
</script>