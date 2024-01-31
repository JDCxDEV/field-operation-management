<template>
	<div id="kt_app_content_container">
			<!--begin::Card-->
			<div class="card">
					<!--begin::Card header-->
					<div class="card-header border-0 pt-6">
							<!--begin::Card title-->
							<div class="card-title">
                <div class="col-12 my-1">
                  <div class="input-group mb-3">
                    <input 
                    v-model="searchQuery"
                    placeholder="Search users"
                    type="text" class="form-control form-control-solid">
                    <div class="input-group-append">
                      <button @click="fetch('search')" type="button" class="btn btn-success">
                        Go
                      </button>
                    </div>
                  </div>
                </div>
              </div>

							<!--begin::Card toolbar-->
							<div class="card-toolbar">
									<!--begin::Toolbar-->
									<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
											<!--begin::Filter-->
											<button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
											<!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
											<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
													</svg>
											</span>
											<!--end::Svg Icon--><span class="filter-text">Filter</span></button>
											<!--begin::Menu 1-->
											<div class="menu menu-sub menu-sub-dropdown w-600px w-md-625px" data-kt-menu="true">
													<!--begin::Header-->
													<div class="px-7 py-5">
														<div class="fs-5 text-dark fw-bold">Filter Options</div>
													</div>
													<!--end::Header-->
													<!--begin::Separator-->
													<div class="separator border-gray-200"></div>
													<!--end::Separator-->
													<!--begin::Content-->
													<div class="px-7 py-5" data-kt-user-table-filter="form">
														<div class="row mb-7">
															<label class="form-label fs-6 fw-semibold">Status:</label>
															<div v-for="userStatus in userStatuses" class="col-3">
															<button 
															@click="filterStatus(userStatus.value)"
															:class="{'btn-primary': userStatus.value == status, 'btn-light': userStatus.value != status}" class="btn btn-100 mb-3">
																	{{ userStatus.label }}
															</button>
															</div>
														</div>
														<!--begin::Input group-->
														<div  v-if="(!companyCoordinator && !marketDirector) && enableCompanyFilter" class="mb-10">
															<label class="form-label fs-6 fw-semibold">Company:</label>
															<select
															@change="fetchMarkets()" 
															v-model="company"
															class="form-select form-select-solid fw-bold">
																	<option value="all">All</option>
																	<option v-for="company in companies" :value="company.id">{{ company.company }}</option>
															</select>
														</div>
														<!--end::Input group-->
															<!--begin::Input group-->
															<div 
															v-if="displayMarketFilter()"
															class="mb-10">
																	<label class="form-label fs-6 fw-semibold">Market:</label>
																	<select 
																	v-model="market"
																	class="form-select form-select-solid fw-bold">
																			<option value="all">All</option>
																			<option v-for="market in markets" :value="market.id">{{ market.market_name }}</option>
																	</select>
															</div>
															<!--end::Input group-->
															<div class="mb-10">
																	<label class="form-label fs-6 fw-semibold">User Types:</label>
																	<select 
																	v-model="userType"
																	class="form-select form-select-solid fw-bold">
																			<option v-for="user in userTypes" :value="user.value">{{ user.label }}</option>
																	</select>
															</div>
															<!--end::Input group-->
															<!--begin::Actions-->
															<div class="d-flex justify-content-end">
																	<button @click="resetFilter()" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
																	<button 
																	@click="fetch('search')"
																	type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
															</div>
															<!--end::Actions-->
													</div>
													<!--end::Content-->
											</div>
											<!--end::Menu 1-->
											<!--end::Filter-->
											
											<!--begin::Add user-->
											<button 
											v-if="!selectedDeactivateUsers.length && !selectedActivateUsers.length && canAdd"
											@click="addUser()"
											type="button" class="btn btn-primary">
											<!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
											<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
															<rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
													</svg>
											</span>
											<!--end::Svg Icon-->Add User</button>
											<!--end::Add user-->

											<!--begin::Batch activate -->
											<div 
											v-if="selectedActivateUsers.length"
											style="margin-right: 10px">
												<confirm-button
												:btnText="renderAlertProps().activateAlert.text"
												:title="renderAlertProps().activateAlert.title"
												:description="renderAlertProps().activateAlert.description"
												confirm-button-text="Confirm"
												btn-class="btn btn-primary"
												icon="fa-solid fa-unlock"
												@confirmed="batchActivate()"
												></confirm-button>	
											</div>

											<!--end::Batch activate-->

											<!--begin::Batch deactivate-->
											<confirm-button
											v-if="selectedDeactivateUsers.length"
											:btnText="renderAlertProps().deactivateAlert.text"
											:title="renderAlertProps().deactivateAlert.title"
											:description="renderAlertProps().deactivateAlert.description"
											confirm-button-text="Confirm"
											btn-class="btn btn-danger"
											icon="fa-solid fa-lock"
											@confirmed="batchDeactivate()"
											></confirm-button>											
											<!--end::Batch deactivate-->																			

									</div>
									<!--end::Toolbar-->
									<!--begin::Group actions-->
									<div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
											<div class="fw-bold me-5">
											<span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
											<button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
									</div>
									<!--end::Group actions-->
							</div>
							<!--end::Card toolbar-->
					</div>
					<!--end::Card header-->
					<!--begin::Card body-->
					<div class="card-body py-4">
							<!--begin::Table-->
							<div 
							:class="{'table-min-height': users.length}"
							class="table-responsive"> 
								<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
										<!--begin::Table head-->
										<thead>
												<!--begin::Table row-->
												<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
														<th class="w-10px pe-2">
																<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
																		<input
																		@change="selectAllUsers()"
																		:checked="selectAll"
																		class="form-check-input" type="checkbox" value="1" />
																</div>
														</th>
														<th class="min-w-125px">User</th>
														<th v-if="!companyCoordinator" class="min-w-125px">Company</th>
														<th class="min-w-125px">Market</th>
														<th class="min-w-125px">User Status</th>
														<th class="min-w-125px">Role</th>
														<th class="min-w-125px">Last Login</th>
														<th 
														v-if="!disableActions"
														class="text-end min-w-100px">Actions</th>
												</tr>
												<!--end::Table row-->
										</thead>
										<!--end::Table head-->
										<!--begin::Table body-->
										<tbody class="text-gray-600 fw-semibold">
												<!--begin::Table row-->
												<tr v-for="user in users">
														<!--begin::Checkbox-->
														<td>
																<div class="form-check form-check-sm form-check-custom form-check-solid">
																		<input 
																		v-model="user.selected"
																		@change="selectUser(user)"
																		:checked="user.selected"
																		:disabled="postLoading"
																		class="form-check-input" type="checkbox" value="1" />
																</div>
														</td>
														<!--end::Checkbox-->
														<!--begin::User=-->
														<td class="d-flex align-items-center">
																<!--begin::User details-->
																<div
																class="d-flex flex-column">
																	<a :href="`/account/settings?user_id=${user.id}`">
																		<span class="text-gray-800 mb-1">
																			{{ user.name }}
																		</span>
																	</a>
																	<span>{{ user.email }}</span>
																</div>
																<!--begin::User details-->
														</td>
														<!--end::User=-->
														<!--begin::Role=-->
														<!--end::Role=-->
														<!--begin::Company=-->
														<td v-if="!companyCoordinator">
																<div class="badge badge-light fw-bold">
																	<a 
																	v-if="user.info?.company_name"
																	:href="`/companies/${user.info.company_id}/overview`"
																	class="text-gray-600"
																	>
																		{{ user.info.company_name }}
																	</a>
																	<span v-else>
																		Not assigned
																	</span>
																</div>
														</td>
														<!--end::Company=-->
														<!--begin::Market=-->
														<td>
																<div class="badge badge-light fw-bold">
																	<a 
																	v-if="user.info?.market_name"
																	:href="`/markets/${user.info.market_id}/overview`"
																	class="text-gray-600"
																	>
																		{{ user.info.market_name }}
																	</a>
																	<span v-else>
																		Not assigned
																	</span>
																</div>
														</td>
														<!--end::Market=-->
														<!--begin::Last login=-->
														<td>
															<span v-if="user.blacklisted" class="badge badge-dark">Blacklisted</span>	
															<div v-if="!user.blacklisted" class="badge fw-bold" :class="{ 'badge-success': user.status, 'badge-danger': !user.status }">{{ user.status ? 'Active' : 'Deactivated' }}</div>
														</td>
														<td>
															<span 
															:class="user.role_class">
																{{ user.role_description }}
															</span>
														</td>
														<!--end::Last login=-->
														<!--begin::Joined-->
														<td>
															<div class="badge badge-light fw-bold">
																{{ user.last_login_readable != '-' ?  user.last_login_readable : 'Never'}}
															</div>
														</td>
														<!--begin::Joined-->
														<!--begin::Action=-->
														<td 
														v-if="!disableActions"
														class="text-end">
																<div class="dropdown">
																		<button 
																		:disabled="postLoading || user.selected"
																		class="btn btn-secondary dropdown-toggle " type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
																				Action
																		</button>
																		<ul class="dropdown-menu" aria-labellezdby="dropdownMenuButton1">
																				<li><a
																				@click="viewUser('view', user)" 
																				class="dropdown-item" href="#">View</a></li>
																				<li><a 
																				v-if="!user.blacklisted"
																				@click="viewUser('edit', user)"
																				class="dropdown-item" href="#">Edit</a></li>
																				<li>
																				<confirm-button
																				v-if="!user.blacklisted"
																				type="link"
																				:btnText="renderAlertProps(user).statusAlert.text"
																				:title="renderAlertProps(user).statusAlert.title"
																				:description="renderAlertProps(user).statusAlert.description"
																				btn-class="dropdown-item"
																				:route="renderAlertProps(user).statusAlert.route"
																				@confirmed="confirmedSuccess(user, 'status')"
																				></confirm-button>
																				</li>
																				<li>
																				<confirm-button
																				type="link"
																				:btnText="renderAlertProps(user).blacklistAlert.text"
																				:title="renderAlertProps(user).blacklistAlert.title"
																				:description="renderAlertProps(user).blacklistAlert.description"
																				btn-class="dropdown-item"
																				:route="renderAlertProps(user).blacklistAlert.route"
																				@confirmed="confirmedSuccess(user, 'blacklist')"
																				></confirm-button>
																				</li>
																				<li><a 
																				v-if="!user.blacklisted"
																				@click="resetPassword(user)"
																				class="dropdown-item" href="#">Reset Password</a>
																				</li>
																		</ul>
																</div>
														</td>
														<!--end::Action=-->
												</tr>
												<tr v-if="!users.length" class="odd my-10">
														<td valign="top" colspan="7" class="text-center">No matching records found</td>
												</tr>						
												<!--end::Table row-->
										</tbody>
										<!--end::Table body-->
								</table>
							</div>
							
							<div class="row">
									<div class="col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start">
										<span class="text-gray-600">Total: <strong>{{ current_count || 0 }}</strong> / <strong>{{ total }}</strong></span>
									</div>
									<div class="col-sm-12 col-md-8 d-flex align-items-center justify-content-center justify-content-md-end">
											<nav aria-label="Page navigation example">
													<ul class="pagination">
															<li class="page-item">
																	<button :disabled="!this.prev_page_url" @click="fetch(false, this.prev_page_url)" class="page-link" href="#" aria-label="Previous">
																			<span aria-hidden="true">&laquo;</span>
																	</button>
															</li>

															<li v-for="link in links" class="page-item" :class="{ active: link.active }">
																	<button :disabled="link.label == '...'" type="button" class="page-link" @click="fetch(false, link.url)">{{ link.label }}</button>
															</li>

															<li class="page-item">
																	<button :disabled="!this.next_page_url" @click="fetch(false, this.next_page_url)" class="page-link" href="#" aria-label="Next">
																			<span aria-hidden="true">&raquo;</span>
																	</button>
															</li>
													</ul>
											</nav>
									</div>
							</div>
							<!--end::Table-->
					</div>
					<!--end::Card body-->
			</div>
			<!--end::Card-->
	</div>
	<!--end::Content container-->
	
	<user-view
	:store-url="storeUrl"
	:companies-fetch-url="companiesFetchUrl"
	:markets-fetch-url="marketsFetchUrl"    
	@updated="updated()"
	hide-edit
	ref="userView"
	:company-coordinator="companyCoordinator"
	:company-id="companyId"
	:market-id="marketId"
	:market-director="marketDirector"
	:blank-image="blankImage"
	></user-view>    

	<user-reset-password
	ref="userResetPassword"
	></user-reset-password>

</template>

<script>
import UserView from "./UserView.vue";
import UserResetPassword from "./UserResetPassword.vue";
import ResponseMixins from "Mixins/response.js";

export default {
	name: 'user-table',

	mixins: [ResponseMixins],

	props: {
			fetchUrl: {
					type: String,
					default: ""
			},
			storeUrl: {
					type: String,
					default: ""
			},
			companiesFetchUrl: {
					type: String,
					default: ""            
			},
			marketsFetchUrl: {
					type: String,
					default: ""            
			},
			companyCoordinator: {
					type: Number,
					default: false
			},
			marketDirector: {
					type: Number,
					default: false
			},
			companyId: {
					type: Number,
					default: ""
			},
			marketId: {
					type: Number,
					default: ""
			},			
			canAdd: {
					type: Boolean,
					default: true
			},
			enableCompanyFilter: {
					type: Boolean,
					default: true
			},
			enableMarketFilter: {
					type: Boolean,
					default: true
			},
			disableActions: {
					type: Boolean,
					default: false
			},
			autoFetch: {
				type: Boolean,
				default: true
			},
			marketDirectorsOnly: {
				type: Boolean,
				default: false
			},
			blankImage: {
				type: String,
				default: ""
			}
	},

	components: {
			UserView,
			UserResetPassword
	},

	computed: {
			confirmTitle() {
					return `${this.user.status ? "Deactivate": "Activate"} ${this.fullName}?`;
			},

			confirmDescription() {
					return `Are you sure to ${this.user.status ? "deactivate": "activate"} <strong>${this.fullName}</strong>?`;
			},

			userStatus() {
					return this.user.status == 1 ? true: false;
			}
	},

	data: () => ({
			users: [],
			companies: [],
			markets: [],
			links: [],
			prev_page_url: null,
			next_page_url: null,
			current_url: null,
			current_count: 0,
			total: 0,

			status: "active",
			company: "all",
			market: "all",
			userType: "all",
      searchQuery: "",

			selectedUser: {},

			selectAll: false,
			selectedDeactivateUsers: [],
			selectedActivateUsers: [],

			postLoading: false,

			userStatuses: [
        { label: "Active", value: "active" },
				{ label: "Deactivated", value: "inactive" },
				{ label: "Blacklisted", value: "blacklisted" },		
				{ label: "All", value: "all" },		
			],

			userTypes: [
				{ label: "All", value: "all" },
				{ label: "Coordinator", value:  3 },
				{ label: "Market Director", value: 4 },
				{ label: "Pod Leader", value: 6 },				
				{ label: "Coordinator & Market Director", value: 5 },			
				{ label: "Agent", value: 0 },
			],			
	}),

	mounted() {
			if(this.autoFetch) {
				this.fetch();
			}

			this.fetchCompanies();
			this.fetchMarkets();

			if(this.companyCoordinator || this.companyId) {
				this.company = this.companyId;
			}
	},

	methods: {
			fetch(isRefresh = null, fetchUrl = null) {
					this.current_url = fetchUrl ? fetchUrl : this.fetchUrl;
					this.company = this.companyId ? this.companyId : this.company;
					if(!this.companyCoordinator) {
						this.market = this.marketId ? this.marketId: this.market;
					}
					const params =  {
							params: { search: this.searchQuery, status: this.status, company: this.company, market: this.market, market_directors_only: this.marketDirectorsOnly, user_type: this.userType  }
					};

					axios.get(this.current_url, params)
							.then((res) => {
							this.users = res.data.data.map((user) => {
								let status = false;
								if (this.selectedActivateUsers.indexOf(user.id) >= 0 || this.selectedDeactivateUsers.indexOf(user.id) >= 0) {
									status = true;
								}
								return { ...user, selected: status }
							});
							this.links = res.data.links;
							this.total = res.data.total;
							this.current_count = res.data.to;
							if(this.links) {
									this.links.shift();
									this.links.pop();    
							}

							this.next_page_url = res.data.next_page_url;
							this.prev_page_url = res.data.prev_page_url;

					}).catch((error) => {
							this.parseError(error, null, {});
					}).finally(() => {
							if(isRefresh) {
									if(isRefresh != 'search') {
											toastr.success('', 'Table Refresh!');
									}else {
											this.search = '';
									}
							}
					});
			},

			fetchCompanies() {
					if(this.companiesFetchUrl) {
							axios.get(this.companiesFetchUrl)
							.then((res) => {
									this.companies = res.data.companies;
							}).catch((err) => {
									console.log(err);
							});
					}
			},

			fetchMarkets() {
					if(this.marketsFetchUrl) {
							const company = this.company != "all" ? `?company_id=${this.company}` : "";
							const route = `${this.marketsFetchUrl}${company}`;
							axios.get(route)
							.then((res) => {
									this.markets = res.data.markets;
							}).catch((err) => {
									console.log(err);
							});
					}
			},

			viewUser(type, user) {
					this.selectedUser = user;
					this.$nextTick(() => {
							this.$refs.userView.open(type, user);            
					});
			},

			addUser() {
					this.$nextTick(() => {
							this.$refs.userView.open("add", "");            
					});
			},

			confirmedSuccess(user, type) {
				if(type == 'status') {
					const action = user.status ? "deactivated" : "activated";
					Swal.fire(`User ${action}`, `User has been successfully ${action}`, "success");
				} else if(type == 'blacklist') {
					const action = user.blacklisted ? "unblocked" : "blocked";
					Swal.fire(`User ${action}`, `User has been successfully ${action}`, "success");
				}

				this.fetch();
			},

			resetPassword(user) {
				this.$refs.userResetPassword.openModal(user);
			},

			selectAllUsers() {
				const status = this.selectAll = !this.selectAll;
				this.users.forEach((user) => {
					user.selected = status;
				});
				if(status) {
					this.selectedDeactivateUsers = this.users.filter(user => user.status).map((user) => {
						return user.id;
					});
					this.selectedActivateUsers = this.users.filter(user => !user.status).map((user) => {
						return user.id;
					});
				}	else {
					this.selectedDeactivateUsers = [];
					this.selectedActivateUsers = [];
				}
			},

			selectUser(user) {
				const key = user.id;
				if(user.status) {
					const index = this.selectedDeactivateUsers.indexOf(key);
					if(index < 0) {
						this.selectedDeactivateUsers.push(key);
					} else {
						this.selectedDeactivateUsers.splice(index, 1);
					}
				} else {
					const index = this.selectedActivateUsers.indexOf(key);
					if(index < 0) {
						this.selectedActivateUsers.push(key);
					} else {
						this.selectedActivateUsers.splice(index, 1);
					}
				}

			},

			batchActivate() {
				this.postLoading = true;
				const data = {
					ids: this.selectedActivateUsers
				};
				axios.post(`/users/batch/activate-users`, data)
					.then((res) => {
						this.selectedActivateUsers = [];
						this.selectAll = false;
						Swal.fire("Users Activated", "Selected users has been activated", "success");
						this.fetch();
					}).catch((error) => {
						this.parseError(error, null, {});
					}).finally(() => {
						this.postLoading = false;
					});
			},

			batchDeactivate() {
				this.postLoading = true;
				const data = {
					ids: this.selectedDeactivateUsers
				};
				axios.post(`/users/batch/deactivate-users`, data)
					.then((res) => {
						this.selectedDeactivateUsers = [];
						this.selectAll = false;
						Swal.fire("Users Deactivated", "Selected users has been deactivated", "success");
						this.fetch();
					}).catch((error) => {
						this.parseError(error, null, {});
					}).finally(() => {
						this.postLoading = false;
					});
			},

			updated() {
				this.fetch(false, this.current_url);
				this.$emit("refresh");
			},

      refresh() {
        this.searchQuery = "";
        this.fetch(true);
      },

			resetFilter() {
					this.search = null;
					this.status = "all";
					this.company = "all";
					this.market = "all";
					this.fetch(true);
			},

			renderAlertProps(user = null) {

					if(user) {
						return {
								statusAlert: {
									text: user.status ? "Deactivate": "Activate",
									title: `${user.status ? "Deactivate": "Activate"} ${user.name}?`,
									description: `Are you sure to ${user.status ? "deactivate" : "activate"} <strong>${user.name}</strong>?`,
									route: user.status ? user.deactivate_url: user.activate_url
								},

								blacklistAlert: {
									text: user.blacklisted ? "Unblock": "Block",
									title: `${user.blacklisted ? "Unblock": "Block"} ${user.name}?`,
									description: `Are you sure to ${user.blacklisted ? "unblock" : "block"} <strong>${user.name}</strong>?`,
									route: user.blacklisted ? user.unblock_url : user.block_url
								},
						};
					} else {
						return {
								deactivateAlert: {
									text: `Deactivate (${this.selectedDeactivateUsers.length}) Users`,
									title: `Deactivate Users?`,
									description: `
									Are you sure to deactivate selected users?
									<br/>
									<br/>
									<i>Note: ${this.selectedDeactivateUsers.length} users will be deactivated.</i>
									`,
								},
								activateAlert: {
									text: `Activate (${this.selectedActivateUsers.length}) Users`,
									title: `Activate Users?`,
									description: `
									Are you sure to activate selected users?
									<br/>
									<br/>
									<i>Note: ${this.selectedActivateUsers.length} users will be activated.</i>
									`,
								}
						};
					}
			},

			displayMarketFilter() {

				if(!this.enableMarketFilter) {
					return false;
				}
				
				if(this.companyCoordinator) {
					return true;
				}

				if(this.marketDirector) {
					return false;
				}
				return true;
			},

      filterStatus(status) {
        if(this.status != status) {
          this.status = status;
          this.fetch();
        }
      }

	}

}
</script>
<style>
.btn-100 {
  width: 100%;
}
</style>