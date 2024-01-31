<template>
<div
	id="kt_activities"
	class="bg-body"
	data-kt-drawer="true"
	data-kt-drawer-name="activities"
	data-kt-drawer-activate="true"
	data-kt-drawer-overlay="true"
	data-kt-drawer-width="{default:'300px', 'lg': '900px'}"
	:data-kt-drawer-direction="drawerDirection"
	data-kt-drawer-toggle="#kt_activities_toggle"
	data-kt-drawer-close="#kt_activities_close">

	<div class="card shadow-none border-0 rounded-0" style="width: 100%">
		<!--begin::Header-->
		<div class="card-header" id="kt_activities_header">
			<h3 class="card-title fw-bold text-dark">Activity Logs</h3>

			<div class="card-toolbar">
				<button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activities_close">
					<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
					<span class="svg-icon svg-icon-1">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
							<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
						</svg>
					</span>
					<!--end::Svg Icon-->
				</button>
			</div>
		</div>
		<!--end::Header-->

		<!--begin::Body-->
		<div class="card-body position-relative" id="kt_activities_body">
			<!--begin::Content-->
			<div id="kt_activities_scroll"
				class="position-relative scroll-y me-n5 pe-5"

				data-kt-scroll="true"
				data-kt-scroll-height="auto"
				data-kt-scroll-wrappers="#kt_activities_body"
				data-kt-scroll-dependencies="#kt_activities_header, #kt_activities_footer"
				data-kt-scroll-offset="5px">

				<!--begin::Timeline items-->
				<div class="timeline">
					<!--begin::Timeline item-->
					<div v-for="log in activities" class="timeline-item">
						<!--begin::Timeline line-->
						<div class="timeline-line w-40px"></div>
						<!--end::Timeline line-->

						<!--begin::Timeline icon-->
						<div class="timeline-icon symbol symbol-circle symbol-40px me-4">
							<div class="symbol-label" :class="log.event_type['bg']" v-html="log.subject_type_icon"></div>
						</div>
						<!--end::Timeline icon-->

						<!--begin::Timeline content-->
						<div class="timeline-content mb-10 mt-n1">
							<!--begin::Timeline heading-->
							<div class="pe-3 mb-5">
								<!--begin::Title-->
								<div class="fs-5 fw-semibold mb-2">{{ log.location }} #{{ log.subject_id }} — {{ log.event_type["label"] }}</div>
								<!--end::Title-->

								<!--begin::Description-->
								<div class="d-flex align-items-center mt-1 fs-6">
									<!--begin::Info-->
									<div class="text-muted me-2 fs-7">Logged at {{ log.logged_at }} by</div>
									<!--end::Info-->

									<!--begin::User-->
									<div 
									v-if="log.causer_profile_url"
									class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" :title="log.causer_name">
										<img :src="log.causer_profile_url" alt="img"/>
									</div>

									<div 
									v-if="!log.causer_profile_url"
									class="symbol symbol-circle symbol-25px">
										<div class="symbol-label fs-8 fw-semibold bg-success text-inverse-primary">{{ log.causer_name[0] }}</div>
									</div>
									<!--end::User-->
								</div>
								<!--end::Description-->
							</div>
							<!--end::Timeline heading-->
							<div class="overflow-auto">
								<div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-3 py-3 mb-2">
									<a class="fs-5 text-dark text-hover-primary fw-semibold">{{ log.description }}</a>
									<div 
									v-if="canView(log.event)"
									class="min-w-125px pe-2 text-right">
										<a 
										@click="view(log)"
										href="javascript:void(0)" class="btn btn-sm btn-light btn-active-light-primary float-right">View</a>
									</div>
								</div>
								<div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-750px px-3 py-3 mb-2">
									<a class="fs-5 text-dark text-hover-primary fw-semibold">
										User: <span :class="log.event_type['class']">{{ log.causer_name }}</span>
									</a>
								</div>
							</div>
						</div>
						<!--end::Timeline content-->
					</div>
					<!--end::Timeline item-->
				</div>
				<!--end::Timeline items-->
			</div>
			<!--end::Content-->
		</div>
		<!--end::Body-->

		<!--begin::Footer-->
		<div class="card-footer py-5 text-center" id="kt_activities_footer">
			<a 
			v-if="activityLogsUrl"
			:href="activityLogsUrl" class="btn btn-bg-body text-primary">
				View All Activities
				<span class="svg-icon svg-icon-3 svg-icon-primary">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor"></rect>
						<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor"></path>
					</svg>
				</span>
			</a>
		</div>
		<!--end::Footer-->
	</div>

	<user-view
	ref="userView"
	companies-fetch-url="/companies/fetch"
	markets-fetch-url="/markets/fetch"    
	@updated="fetch()"
	hide-edit
	></user-view>	

	<modal 
  :value="showCompanyModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event, 'company')"
  v-model="showCompanyModal">
    <template v-slot:title>
      Update Company
    </template>
    <template v-slot:body>
      <company-view
      ref="companyView"
			:child-component="true"
      @cancel="setModalStatus(false, 'company')"
      @updated="setModalStatus(false, 'company'); fetch()"      
      ></company-view>
    </template>
  </modal>

	<modal 
  :value="showMarketModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event, 'market')"
  v-model="showMarketModal">
    <template v-slot:title>
      Update Market
    </template>
    <template v-slot:body>
      <market-view
      ref="marketView"
      companies-fetch-url="/companies/fetch"
      :child-component="true"
      :company-id="subject?.company_id"
      @cancel="setModalStatus(false, 'market')"
      @updated="setModalStatus(false, 'market'); fetch()"      
      ></market-view>
    </template>
  </modal>

	<modal 
  :value="showRecruitModal"
  modalsize="modal-dialog-centered mw-650px"
  @change="setModalStatus($event, 'recruit')"
  v-model="showRecruitModal">
    <template v-slot:title>
      Update Recruit
    </template>
    <template v-slot:body>
      <recruit-view
      ref="recruitView"
      :child-component="true"
      companies-fetch-url="/companies/fetch"
      markets-fetch-url="/markets/fetch"
      @cancel="setModalStatus(false, 'recruit')"
      @updated="setModalStatus(false, 'recruit'); fetch()"
      ></recruit-view>
    </template>
  </modal>

</div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import UserView from "../../views/admin/users/UserView";
import CompanyView from "../../views/admin/companies/CompanyView";
import MarketView from "../../views/admin/markets/MarketView";
import RecruitView from "../../views/admin/recruits/RecruitView";

export default {
  name: "recent-logs",
	components: {
		UserView,
		CompanyView,
		MarketView,
		RecruitView
	},
	props: {
		fetchUrl: {
			type: String,
			default: ""
		},
		drawerDirection: {
			type: String,
			default: "end"	
		},
		mediaPathUrl: {
			type: String,
			default: ""
		},
		activityLogsUrl: {
			type: String,
			default: ""			
		}
	},

	data: () => ({
		activities: [],
		showCompanyModal: false,
		showMarketModal: false,
		showRecruitModal: false,
		subject: {}
	}),
	
	mounted() {
		this.fetch();
	},

	mixins: [ ResponseMixins ],

	methods: {
		fetch() {
			axios.get(this.fetchUrl)
				.then((res) => {
					this.activities = res.data;
				}).catch((err) => {
					this.parseError(err, {});
				});
		},

		canView(event) {
			if(event == "updated" || event == "created") {
				return true;
			}
			return false;
		},

		setModalStatus(status, type) {
			console.log(status);
			switch(type) {
				case "company":
					this.showCompanyModal = status;
					break;
				case "market":
					this.showMarketModal = status;
					break;
				case "recruit":
					this.showRecruitModal = status;
					break;
			}
		},

		view(log) {
			switch (log.subject_type) {
				case "App\\Models\\UserInfo":
				case "App\\Models\\User":
					this.$nextTick(() => {
						if(!log.subject_data?.info) {
							log.subject_data.info = log.subject_info; 
						}
						this.$refs.userView.open("edit", log.subject_data);
					});
				break;

				case "App\\Models\\Company":
					this.$nextTick(() => {
						this.$refs.companyView.setAccessType("edit");
						this.$refs.companyView.fetch(log.subject);
						this.showCompanyModal = true;				
					});
					break;
				case "App\\Models\\Market":
					this.$nextTick(() => {
						this.$refs.marketView.setAccessType("edit");
						this.$refs.marketView.fetch(log.subject);
						this.showMarketModal = true;				
					});
					break;
				case "App\\Models\\Recruit":
					this.$nextTick(() => {
						this.$refs.recruitView.setAccessType("edit");
						this.$refs.recruitView.fetch(log.subject);
						this.showRecruitModal = true;				
					});
					break;
				case "App\\Models\\Form":
					const viewType = log.subject.status ? "view" : "show";
					window.location.href = `/agent-submission-forms/${log.subject_id}/${viewType}`;
					break;		
			}
		}
	}
}
</script>