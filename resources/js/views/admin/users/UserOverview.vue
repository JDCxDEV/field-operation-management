<template>
<div id="kt_app_content_container">
	<div class="card mb-5 mb-xl-10">
		<div class="card-body pt-9 pb-0">
			<!--begin::Details-->
			<div class="d-flex flex-wrap flex-sm-nowrap mb-3">
				<!--begin: Pic-->
				<div 
                :class="{'me-7': !isMobileScreen}"
                class="user-avatar mb-4">
					<div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
						<img :src="avatarUrl" alt="image"/>
						<div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-white h-20px w-20px"></div>
					</div>
				</div>
				<!--end::Pic-->
                <div class="role-mobile mb-2 flex-grow-1 mb-1 text-center" style="display: none;">
                    <span 
                    :class="user.role_class"
                    class="fw-bolder ms-2 fs-8 py-1 px-3" >{{ user.role_description }}</span>
                </div>
				<!--begin::Info-->
				<div 
                :class="{'w-100': isMobileScreen}"
                class="flex-grow-1">
					<!--begin::Title-->
					<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
						<!--begin::User-->
						<div :class="{'d-flex flex-column': !isMobileScreen, 'w-100': isMobileScreen}">
							<!--begin::Name-->
							<div 
                            :class="{'d-flex': !isMobileScreen}"
                            class="user-name-container align-items-center mb-2">
								<a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ user.name }}</a>
								<a href="#">
									<blue-check-mark-icon />
								</a>
								<span 
								:class="user.role_class"
								class="role-lg fw-bolder ms-2 fs-8 py-1 px-3" >{{ user.role_description }}</span>
							</div>
							<!--end::Name-->

							<!--begin::Info-->
							<div 
                            :class="{'d-flex flex-wrap mb-4': !isMobileScreen}"
                            class="contact-details-container fw-bold fs-6 pe-2">
								<label 
                                @click="call(user.phone)"
                                :class="{'d-flex': !isMobileScreen}" class="text-overflow align-items-center text-gray-400 text-hover-primary me-5 mb-2">
									<span class="svg-icon svg-icon-4 me-1">
										<i class="fa-solid fa-phone"></i>&nbsp;
									</span>
									{{ info.formattedPhone }}
								</label>
								<label 
                                @click="email(user.email)"
                                :class="{'d-flex': !isMobileScreen}" class="text-overflow align-items-center text-gray-400 text-hover-primary mb-2">
									<envelope-icon />
									{{ user.email }}
								</label>
							</div>
							<!--end::Info-->
						</div>
						<!--end::User-->
					</div>
					<!--end::Title-->

					<!--begin::Stats-->
                    <div v-if="isMobileScreen" class="row">
                        <div class="col-6">
                            <div class="w-100 border border-gray-300 border-dashed rounded py-3 px-4 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-6 fw-bolder">Company</div>
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ info.company_name == "Not Assigned" ? "N/A" : info.company_name }}</div>
                                <!--end::Label-->
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="w-100 border border-gray-300 border-dashed rounded py-3 px-4 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-6 fw-bolder">Market</div>
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ info.market_name == "Not Assigned" ? "N/A" : info.market_name }}</div>
                                <!--end::Label-->
                            </div>
                        </div>
                        <div class="col-12">
                            <div class=" border border-gray-300 border-dashed rounded py-3 px-4 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="fs-6 fw-bolder">Last Login</div>
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ user.last_login_readable != '-' ?  user.last_login_readable : 'Never'}}</div>
                                <!--end::Label-->
                            </div>
                        </div>
                    </div>
					<div v-if="!isMobileScreen" class="d-flex flex-wrap flex-stack">
						<!--begin::Wrapper-->
						<div class="pe-8 d-flex flex-column flex-grow-1">
							<!--begin::Stats-->
							<div class="d-flex flex-wrap">
								<!--begin::Stat-->
								<div class="me-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
									<!--begin::Number-->
									<div class="d-flex align-items-center">
										<div class="fs-6 fw-bolder">Company</div>
									</div>
									<!--end::Number-->

									<!--begin::Label-->
									<div class="fw-bold fs-6 text-gray-400">{{ info.company_name }}</div>
									<!--end::Label-->
								</div>
								<!--end::Stat-->

								<!--begin::Stat-->
								<div class="me-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
									<!--begin::Number-->
									<div class="d-flex align-items-center">
										<div class="fs-6 fw-bolder">Market</div>
									</div>
									<!--end::Number-->

									<!--begin::Label-->
									<div class="fw-bold fs-6 text-gray-400">{{ info.market_name }}</div>
									<!--end::Label-->
								</div>
								<!--end::Stat-->

								
								<!--begin::Stat-->
								<div class="me-6 border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
									<!--begin::Number-->
									<div class="d-flex align-items-center">
										<div class="fs-6 fw-bolder">Last Login</div>
									</div>
									<!--end::Number-->

									<!--begin::Label-->
									<div class="fw-bold fs-6 text-gray-400">{{ user.last_login_readable != '-' ?  user.last_login_readable : 'Never'}}</div>
									<!--end::Label-->
								</div>
								<!--end::Stat-->
							</div>
							<!--end::Stats-->
						</div>
						<!--end::Wrapper-->
					</div>
					<!--end::Stats-->
				</div>
				<!--end::Info-->
			</div>
			<!--end::Details-->

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
                            class="tab-item nav-link text-active-primary me-6">
                                {{ tab.name }}
                            </a>
                        </li>
                    <!--end::Nav item-->
                </ul>
            </div>
            <!--begin::Navs-->
		</div>
	</div>
	<!--end::Navbar-->
</div>

<div v-show="currentTab == 'overview'">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container">  
            <div class="d-flex flex-column flex-lg-row">
                <div class="flex-column flex-lg-row-auto w-400 w-lg-400px w-xl-400px mb-10 mb-lg-0 align-items-stretch">
                    <div class="card card-flush h-md-100">
                        <div class="card-header align-items-center border-0 mt-4">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fw-bold mb-2 text-dark">Timeline</span>            
                                <span class="text-muted fw-semibold fs-7">Latest Activities</span>
                            </h3>
                        </div>
                        <div class="card-body pt-6">
                            <div 
                            class="timeline-label">
                                <div 
                                v-for="activity in activities"
                                class="timeline-item">
                                    <div class="timeline-label fw-bold text-gray-800 fs-6">{{ activity.time }}</div>
                                    <div class="timeline-badge">
                                        <i 
                                        :class="activity.event_type['text']"
                                        class="fa fa-genderless fs-1"></i>
                                    </div>
                                    <div 
                                    class="fw-mormal timeline-content ps-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800">
                                                {{ activity.description }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div 
                            v-if="!activities.length"
                            class="text-center mb-7">
                                <span>No activities found</span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10 align-items-stretch">
                    <!--begin::details View-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Card header-->
                        <div class="card-header cursor-pointer">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0"></h3>
                            </div>
                            <a 
                            @click="currentTab = 'settings'"
                            class="btn btn-primary align-self-center">Edit Profile</a>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body pt-6">
                            <!--begin::Row-->
                            <div class="row mb-7">
                                <!--begin::Label-->
                                <label class="col-lg-4 fw-bold text-muted">Full Name</label>
                                <!--end::Label-->

                                <!--begin::Col-->
                                <div class="col-lg-8">
                                    <span class="fw-bolder fs-6 text-dark">{{ user.name }}</span>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <!--begin::Label-->
                                <label class="col-lg-4 fw-bold text-muted">Company</label>
                                <!--end::Label-->

                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <span class="fw-bold fs-6">{{ companyName }}</span>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row mb-7">
                                <!--begin::Label-->
                                <label class="col-lg-4 fw-bold text-muted">Market</label>
                                <!--end::Label-->

                                <!--begin::Col-->
                                <div class="col-lg-8 fv-row">
                                    <span class="fw-bold fs-6">{{ marketName }}</span>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="row">
                                <!--begin::Label-->
                                <label class="col-lg-4 fw-bold text-muted">
                                    Contact Phone
                                    <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i>
                                </label>
                                <!--end::Label-->

                                <!--begin::Col-->
                                <div class="col-lg-8 d-flex align-items-center">
                                    <span class="fw-bolder fs-6 me-2">{{ info.formattedPhone }}</span>
                                    <span v-if="info.formattedPhone" class="badge badge-success">Verified</span>
                                </div>
                                <!--end::Col-->
                            </div>
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::details View-->
                </div>
            </div>
        </div>
    </div>
</div>

<div v-show="currentTab == 'settings'">
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container">  
            <div class="d-flex flex-column flex-lg-row">
                <div class="flex-column flex-lg-row-auto w-500 w-lg-500px w-xl-500px mb-10 mb-lg-0">
                    <div class="card card-flush h-md-100">
                        <div class="card mb-5 mb-xl-2">
                            <!--begin::Card header-->
                            <div class="card-header cursor-pointer">
                                <!--begin::Card title-->
                                <div class="card-title m-0">
                                    <h3 class="fw-bolder m-0">Edit Profile</h3>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--begin::Card header-->

                            <!--begin::Card body-->
                            <div id="kt_account_profile_details" class="collapse show">
                                <!--begin::Form-->
                                <form 
                                @submit.prevent="onUpdateProfile($event)"
                                class="form" method="POST" enctype="multipart/form-data">
                                <!--begin::Card body-->
                                    <div class="card-body border-top p-9">
                                        <!--begin::Input group-->
                                        <div class="row mb-6">
                                            <!--begin::Col-->
                                            <div class="col-lg-12 text-center">
                                                <!--begin::Image input-->
                                                <div class="image-input image-input-outline" 
                                                :class="{'image-input-empty': !avatarUrl}"
                                                data-kt-image-input="true" style="">
                                                    <!--begin::Preview existing avatar-->
                                                    <div class="image-input-wrapper w-125px h-125px" :style="{'background-image': 'url(' + renderPreviewUrl + ')'}"></div>
                                                    <!--end::Preview existing avatar-->

                                                    <!--begin::Label-->
                                                    <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                                        <i class="bi bi-pencil-fill fs-7"></i>

                                                        <!--begin::Inputs-->
                                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg"/>
                                                        <input type="hidden" name="avatar_remove"/>
                                                        <!--end::Inputs-->
                                                    </label>
                                                    <!--end::Label-->

                                                    <!--begin::Cancel-->
                                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                                        <i class="bi bi-x fs-2"></i>
                                                    </span>
                                                    <!--end::Cancel-->

                                                    <!--begin::Remove-->
                                                    <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                                        <i class="bi bi-x fs-2"></i>
                                                    </span>
                                                    <!--end::Remove-->
                                                </div>
                                                <!--end::Image input-->

                                                <!--begin::Hint-->
                                                <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                                <!--end::Hint-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Input group-->
                                        <div class="row mb-6">
                                            <!--begin::Col-->
                                            <div class="col-lg-12">
                                                <!--begin::Row-->
                                                <div class="row">
                                                    <label class="col-lg-12 col-form-label required fw-bold fs-6">Full Name</label>
                                                    <div class="col-lg-6 fv-row">
                                                        <input 
                                                        v-model="user.first_name"
                                                        :class="{'is-invalid': errors?.first_name}"
                                                        type="text" name="first_name" class="form-control form-control-lg form-control-solid mb-3 mb-lg-0" placeholder="First name"/>
                                                        <div :v-if="errors?.first_name" class="invalid-feedback">{{ errors?.first_name?.[0] }}</div>
                                                    </div>
                                                    <!--end::Col-->

                                                    <!--begin::Col-->
                                                    <div class="col-lg-6 fv-row">
                                                        <input 
                                                        v-model="user.last_name"
                                                        :class="{'is-invalid': errors?.last_name}"
                                                        type="text" name="last_name" class="form-control form-control-lg form-control-solid" placeholder="Last name"/>
                                                        <div :v-if="errors?.last_name" class="invalid-feedback">{{ errors?.last_name?.[0] }}</div>
                                                    </div>
                                                    <!--end::Col-->
                                                </div>
                                                <!--end::Row-->
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->

                                        <!--begin::Input group-->
                                        <div class="row mb-6">
                                            <!--begin::Label-->
                                            <label class="col-lg-12 col-form-label fw-bold fs-6">
                                                <span class="required">Contact Phone</span>
                                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Phone number must be active"></i>
                                            </label>
                                            <!--end::Label-->

                                            <!--begin::Col-->
                                            <div class="col-lg-12 fv-row">
                                                <phone-number
                                                :value="user.info.phone"
                                                :errors="errors?.phone ? true: false"
                                                :error-message="errors?.phone?.[0]"
                                                length="14"
                                                input-class="form-control form-control-lg form-control-solid"
                                                @update:value="newValue => user.info.phone = newValue"
                                                ></phone-number>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Input group-->
                                    </div>
                                    <!--end::Card body-->

                                    <!--begin::Actions-->
                                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                                        <button 
                                        :disabled="isLoading"
                                        type="submit" class="btn btn-primary">
                                            Save Changes
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Card body-->
                        </div>
                    </div>
                </div>
                <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                    <!--begin::details View-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer">
                            <div class="card-title m-0">
                                <h3 class="fw-bolder m-0">Sign-in Method</h3>
                            </div>
                        </div>
                        <!--end::Card header-->

                        <!--begin::Content-->
                        <div id="kt_account_signin_method" class="collapse show">
                            <!--begin::Card body-->
                            <div class="card-body border-top p-9">
                                <!--begin::Email Address-->
                                <div class="d-flex flex-wrap align-items-center">
                                    <!--begin::Label-->
                                    <div id="kt_signin_email">
                                        <div class="fs-6 fw-bolder mb-1">Email Address</div>
                                        <div class="fw-bold text-gray-600">{{ user.email }}</div>
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Edit-->
                                    <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form 
                                        @submit.prevent="onChangeEmail($event)"
                                        class="form" novalidate="novalidate" method="POST">
                                            <input type="hidden" name="current_email" :value="user.email"/>
                                            <div class="row mb-6">
                                                <div class="col-lg-6 mb-4 mb-lg-0">
                                                    <div class="fv-row mb-0">
                                                        <label for="email" class="form-label fs-6 fw-bolder mb-3">Enter New Email Address</label>
                                                        <input 
                                                        :class="{'is-invalid': errors?.email}"
                                                        type="email" class="form-control form-control-lg form-control-solid" placeholder="Email Address" name="email" />
                                                        <div :v-if="errors?.email" class="invalid-feedback">{{ errors?.email?.[0] }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="fv-row mb-0">
                                                        <label for="current_password" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                                                        <input 
                                                        :class="{'is-invalid': errors?.current_password}"
                                                        type="password" class="form-control form-control-lg form-control-solid" name="current_password" id="current_password"/>
                                                        <div :v-if="errors?.current_password" class="invalid-feedback">{{ errors?.current_password?.[0] }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <button 
                                                :disabled="isLoading"
                                                class="btn btn-primary  me-2 px-6">
                                                    Update Email
                                                </button>
                                                <button id="kt_signin_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->

                                    <!--begin::Action-->
                                    <div id="kt_signin_email_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Change Email</button>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Email Address-->

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>
                                <!--end::Separator-->

                                <!--begin::Password-->
                                <div class="d-flex flex-wrap align-items-center mb-10">
                                    <!--begin::Label-->
                                    <div id="kt_signin_password">
                                        <div class="fs-6 fw-bolder mb-1">Password</div>
                                        <div class="fw-bold text-gray-600">************</div>
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Edit-->
                                    <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form 
                                        @submit.prevent="onChangePassword($event)"
                                        class="form" novalidate="novalidate" method="POST">
                                            <input type="hidden" name="current_email" :value="user.email"/>
                                            <div class="row mb-1">
                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="current_password" class="form-label fs-6 fw-bolder mb-3">Current Password</label>
                                                        <input 
                                                        :class="{'is-invalid': errors?.current_password}"
                                                        type="password" class="form-control form-control-lg form-control-solid" name="current_password" id="current_password"/>
                                                        <div :v-if="errors?.current_password" class="invalid-feedback">{{ errors?.current_password?.[0] }}</div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="password" class="form-label fs-6 fw-bolder mb-3">New Password</label>
                                                        <input 
                                                        :class="{'is-invalid': errors?.password}"
                                                        type="password" class="form-control form-control-lg form-control-solid" name="password" id="password"/>
                                                        <div :v-if="errors?.password" class="invalid-feedback">{{ errors?.password?.[0] }}</div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="password_confirmation" class="form-label fs-6 fw-bolder mb-3">Confirm New Password</label>
                                                        <input 
                                                        :class="{'is-invalid': errors?.password_confirmation}"
                                                        type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation" id="password_confirmation"/>
                                                        <div :v-if="errors?.password_confirmation" class="invalid-feedback">{{ errors?.password_confirmation?.[0] }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-text mb-5">Password must be at least 8 character and contain symbols</div>

                                            <div class="d-flex">
                                                <button 
                                                :disabled="isLoading"
                                                class="btn btn-primary me-2 px-6">
                                                    Update Password
                                                </button>
                                                <button id="kt_password_cancel" type="button" class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->

                                    <!--begin::Action-->
                                    <div id="kt_signin_password_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Reset Password</button>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Password-->

                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>
                                <!--end::Separator-->

                                <!-- <div class="d-flex flex-wrap align-items-center mb-10">
                                    <div class="fs-6 fw-bolder mb-1">Enable 2 Factor Authentication</div>

                                    <div class="ms-auto">
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input 
                                            @change="check2Auth()"
                                            v-model="user.two_auth"
                                            :checked="user.two_auth == '1' || user.two_auth == 1 ? true: false"
                                            class="form-check-input" type="checkbox"/>
                                        </div>
                                    </div>
                                </div> -->

                                <div 
                                class="d-flex flex-wrap align-items-center mb-10">
                                    <!--begin::Label-->
                                    <div class="fs-6 fw-bolder mb-1">Send 2 Factor Authentication Code Via</div>

                                    <div class="ms-auto">

                                        <div class="form-check form-check-custom form-check-solid mb-3">
                                            <input
                                            :disabled="!user.two_auth ? true: false"
                                            @change="updateTwoAuth(false)" 
                                            v-model="user.two_auth_type"
                                            :checked="(user.two_auth == '1' || user.two_auth == 1) && user.two_auth_type == 'email' ? true: false"
                                            class="form-check-input" type="radio" :value="user.two_auth == '1' || user.two_auth == 1 ? 'email' : ''" id="email" checked="checked" />
                                            <label class="form-check-label" for="email">
                                                Email
                                            </label>
                                        </div>                                        
                                        
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input
                                            :disabled="!user.two_auth ? true: false"
                                            @change="updateTwoAuth(false)" 
                                            v-model="user.two_auth_type"
                                            :checked="(user.two_auth == '1' || user.two_auth == 1) && user.two_auth_type == 'sms' ? true: false"
                                            class="form-check-input" type="radio" :value="user.two_auth == '1' || user.two_auth == 1 ? 'sms': ''" id="sms"  />
                                            <label class="form-check-label" for="sms">
                                                SMS
                                            </label>
                                        </div>
                                    </div>
                                </div>                                

                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Content-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

<div v-show="currentTab == 'security'">
    <login-session-table
        :fetch-url="loginSessionUrl"
    ></login-session-table>
</div>

<div v-show="currentTab == 'activity-logs'">
    <activity-logs-table
        :fetchUrl="activityLogsFetchUrl"
        :currentMode="themeMode"
        :userId="userId"
    ></activity-logs-table>
</div>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import ScreenMixins from "Mixins/screen.js";
import BlueCheckMarkIcon from "Components/icons/BlueCheckMarkIcon";
import ChatRectangleIcon from "Components/icons/ChatRectangleIcon";
import EnvelopeIcon from "Components/icons/EnvelopeIcon";
import MapIcon from "Components/icons/MapIcon";
import BuildingIcon from "Components/icons/BuildingIcon";
import LoginSessionTable from "./LoginSessionTable.vue";
import PhoneNumber from "Components/inputs/PhoneNumber";

export default {
	name: "user-overview",
	
	props: {
		
		userFindUrl: {
			type: String,
			default: ""
		},
        
		updateUrl: {
            type: String,
            default: ""
		},

		changeEmailUrl: {
            type: String,
            default: ""
		},

		changePasswordUrl: {
            type: String,
            default: ""
		},

		updateTwoAuthUrl: {
            type: String,
            default: ""
		},

		loginSessionUrl: {
			type: String,
			default: ""
		},

        userId: {
			type: [String, Number],
			default: ""
        },
        
        activityLogsFetchUrl: {
            type: String,
			default: ""
        },
        
        themeMode: {
            type: String,
			default: ""
        }
	},

	components: {
        BlueCheckMarkIcon,
        ChatRectangleIcon,
        MapIcon,
        EnvelopeIcon,
        BuildingIcon,
        LoginSessionTable,
        PhoneNumber
	},

	computed: {
		info() {
			if(this.user?.info) {
				return this.user.info;
			}
			return {};
		},

		avatarUrl() {
			if(this.info.avatar_url) {
				return this.info.avatar_url;
			}
		},

        companyName() {
            if(this.info.company) {
                return `${this.info.company?.custom_id} — ${this.info.company_name}`;
            }
            return "—";
        },

        marketName() {
            if(this.info.market) {
                return `${this.info.market?.custom_id} — ${this.info.market_name}`;
            }
            return "—";
        },

		renderPreviewUrl() {
			if(this.previewUrl) {
				return this.previewUrl;
			}
			return this.avatarUrl;			
		},
	},

	data: () => ({
        activities: [],
        user: {
			info: {
				phone: ""
			},
            two_auth: true
		},
        tabs: [
            { name: "Overview", key: "overview" },
            { name: "Settings", key: "settings" },
            { name: "Security", key: "security" },
            { name: "Activity Logs", key: "activity-logs" },
        ],

        currentTab: "overview",
		isLoading: false,

		previewUrl: "",
    }),

	mounted() {
		this.fetch();
        this.fetchActivities();
	},

	mixins: [ ResponseMixins, FormatterMixins, ScreenMixins ],

	methods: {

		async fetch() {
			this.isLoading = true;
            await axios.get(this.userFindUrl)
                .then((res) => {
                    this.user = res.data.user;
					const formattedPhone = this.phoneFormat(res.data.user.info.phone);
                    this.user.info.formattedPhone = formattedPhone;
					this.user.info.phone = formattedPhone;
                    this.user.two_auth = true;
                }).catch((error) => {
					this.parseError(error, null, {});
                    console.log(error);
                }).finally(() => {
					this.isLoading = false;
                });
        },

        fetchActivities() {
            axios.get(`/activity-logs/recent?time=today&user=${this.userId}&limit=8`)
                .then((res) => {
                    this.activities = res.data;
                }).catch((error) => {
					this.parseError(error, null, {});
                }).finally(() => {
                });
        },

        selectTab(tab) {
            this.$nextTick(() => {
                this.currentTab = tab.key
            });
        },
		
        onUpdateProfile(event) {
            this.resetErrors();
            let form = event.target;
            this.isLoading = true;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);
            params.append("phone", this.clean(this.user.info.phone));

            this.isLoading = true;
            axios.post(this.updateUrl, params)
            .then((res) => {
                Swal.fire(`Profile Updated`, `Profile has been updated`, "success");
                this.fetch();
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.isLoading = false;
            });
        },

        onChangeEmail(event) {
            this.resetErrors();
            let form = event.target;
            this.isLoading = true;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);

            this.isLoading = true;
            axios.post(this.changeEmailUrl, params)
            .then((res) => {
                Swal.fire(`Email Updated`, `Email has been updated`, "success");
                document.getElementById("kt_signin_cancel").click();
                this.fetch();
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.isLoading = false;
            });
        },

        onChangePassword(event) {
            this.resetErrors();
            let form = event.target;
            this.isLoading = true;
            if (!form) {
                form = event;
            }
            const params = new FormData(form);

            this.isLoading = true;
            axios.post(this.changePasswordUrl, params)
            .then((res) => {
                Swal.fire(`Password Changed`, `Password has been changed`, "success");
                document.getElementById("kt_password_cancel").click();
                this.fetch();
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.isLoading = false;
            });
        },

        check2Auth() {
            if(this.user.two_auth) {
                Swal.fire({
                    title: "Enable 2 Factor Authentication",
                    html: `<span>Are you sure to enable 2 factor authentication?</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Confirm"
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        this.updateTwoAuth();
                    } else {
                        this.user.two_auth = false;
                    }
                });
            } else {
                this.updateTwoAuth();
            }
        },

        updateTwoAuth(showmessage = true) {
            this.isLoading = true;
            const params = {
                two_auth: true,
                two_auth_type: this.user.two_auth_type
            };
            axios.post(this.updateTwoAuthUrl, params)
            .then(() => {
                // if(showmessage) {
                    // this.parseSuccess(`2 Factor Authentication has been ${this.user.two_auth ? 'enabled' : 'disabled'}.`);
                // }
                this.parseSuccess(`Sending of 2 Factor Authentication has been set via ${this.user.two_auth_type == 'sms' ? "SMS": "Email"}.`);                
            }).catch((error) => {
                this.parseError(error, null, {});
            }).finally(() => {
                this.isLoading = false;
            });
        },

        call(phone) {
            window.location.href = `tel:+${phone}`;
        },

        email(email) {
            window.location.href = `mailto:${email}`;
        }
	}
}
</script>