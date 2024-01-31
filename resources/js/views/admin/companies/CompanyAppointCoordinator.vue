<template>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="d-flex flex-column flex-lg-row">
                <div class="flex-column flex-lg-row-auto w-100 w-lg-400px w-xl-500px mb-10 mb-lg-0">
                    <company-common-user-table
                    ref="commonUserTable"
                    @appointed="refresh()"
                    ></company-common-user-table>                        
                </div>
                <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                    <!--begin::Table widget 14-->
                    <div class="card card-flush h-md-100">
                        <div class="card-header pt-7">
                            <div class="card-title">
                                <div class="d-flex justify-content-center flex-column me-3">
                                    <span class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">Assigned Coordinators</span>
                                </div>
                            </div>
                        </div>
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="scroll-y me-n5 pe-5 h-200px h-lg-auto mb-12" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_toolbar, #kt_app_toolbar, #kt_footer, #kt_app_footer, #kt_chat_contacts_header" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_contacts_body" data-kt-scroll-offset="5px">
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                                <th class="min-w-75px">Image</th>
                                                <th class="min-w-175px">Name</th>
                                                <th class="min-w-175px">Date Added</th>
                                                <th class="min-w-175px">Action</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody>
                                            <tr class="text-center" v-if="!coordinators.length" >
                                                <td colspan="4">
                                                    No users found
                                                </td>
                                            </tr>
                                            <tr v-for="user in coordinators">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-45px symbol-circle ">
                                                    <span v-if="!user.avatar_url" class="symbol-label bg-light-danger text-danger fs-6 fw-bolder ">{{ user.name[0] }}</span>
                                                    <img v-if="user.avatar_url" :src="user.avatar_url">
                                                </div>
                                                <!--end::Avatar-->
                                                <td class="text-start">
                                                    <span style="cursor: pointer" class="text-gray-600 fw-bold fs-6">{{ user.name }}</span>
                                                    <div class="fw-semibold text-muted">{{ user.email }}</div>
                                                </td>
                                                <td class="text-start">
                                                    <div class="fw-semibold text-muted">{{ user.date_added }}</div>
                                                </td>
                                                <td>
                                                    <span 
                                                    @click="remove(user)"
                                                    style="cursor: pointer"
                                                    class="badge badge-danger">Remove</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                </div>
                            </div>
                            
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end::Table widget 14-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CompanyView from "./CompanyView";
import CompanyCommonUserTable from "./CompanyCommonUserTable"
import ResponseMixins from "Mixins/response.js";

export default {
    name: "company-appoint-coordinator",
    
    components: {
        CompanyView,
        CompanyCommonUserTable
    },

    props: {
        fetchUrl: {
            type: String,
            default: ""
        },

        selectFirst: {
            type: Boolean,
            default: false
        }
    },

    data: () => ({
        isLoading: false,

        companies: [],
        users: [],
        links: [],
        prev_page_url: null,
        next_page_url: null,
        current_url: null,
        search: null,

        selectedCompany: {},
        coordinators: [],

    }),

    mixins: [ResponseMixins],

    async mounted() {
        await this.fetch();
        if(this.selectFirst && this.companies.length) {
            this.selectCompany(this.companies[0]);
        }
    },


     methods: {

        async fetch(isRefresh = null, fetchUrl = null) {
            this.current_url = fetchUrl ? `${fetchUrl}&paginate=true` : `${this.fetchUrl}?paginate=true` ;
            const params =  {
                params: { search: this.search, status: this.status }
            };

            await axios.get(`${this.current_url}`, params)
            .then((res) => {
                this.companies = res.data.data;
                this.links = res.data.links;

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

        selectCompany(company) {
            this.selectedCompany = company;
            this.fetchCoordinatorsAndUsers();
            this.$refs.commonUserTable.loadUsers(company);
        },

        fetchCoordinatorsAndUsers() {
            const url = this.selectedCompany.coordinators_and_users_url;
            axios.get(url)
                .then((res) => {
                    this.coordinators = res.data.coordinators;
                }).catch((err) => {
                    console.log(err);
                })
        },

        remove(user) {
            Swal.fire({
                title: "Remove from Coordinators?",
                html: `Are you sure to remove ${user.name} from company coordinators?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Remove"
            }).then(async (result) => {
                this.isLoading = true;
                if (result.isConfirmed) {
                    const data = {
                        userId: user.id
                    }
                    axios.post(`/companies/${this.selectedCompany.id}/coordinators/remove`, data)
                        .then((response) => {
                            this.refresh();
                            this.$refs.commonUserTable.loadUsers(this.selectedCompany);                            
                            Swal.fire("User Removed", "User removed from coordinators.", "success");
                        }).catch((error) => {
                            this.parseError(error, null, {});                            
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });
        },

        refresh() {
            this.fetchCoordinatorsAndUsers();
            this.$emit("refresh");
        }
    },
}
</script>

<style>
.pagination-bottom {
    position: absolute;
    bottom: 20px;
    width: 95%;
}
</style>