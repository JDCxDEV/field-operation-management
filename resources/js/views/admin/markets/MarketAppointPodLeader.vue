<template>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="d-flex flex-column flex-lg-row">
                <div class="flex-column flex-lg-row-auto w-100 w-lg-400px w-xl-500px mb-10 mb-lg-0">
                    <market-common-user-table
                    ref="commonUserTable"
                    appoint-type="Pod Leaders"
                    :agent-only="true"
                    @appointed="refresh()"
                    ></market-common-user-table>
                </div>
                <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                    <!--begin::Table widget 14-->
                    <div class="card card-flush h-md-100">
                        <!--begin::Header-->
                        <div class="card-header pt-7">
                            <!--begin::Title-->
                            <h3 class="card-title align-items-start flex-column">
                                <span class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">Assigned Pod Leaders</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body pt-6">
                            <div class="jumbotron bg-gray">
                                <!--begin::Table container-->
                                <div class="table-responsive">
                                    <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr class="fs-7 fw-bold text-gray-400 border-bottom-0">
                                                <th class="min-w-75px">Image</th>
                                                <th class="min-w-175px">Name</th>
                                                <th class="min-w-50px">Pod Users</th>
                                                <th class="min-w-175px">Action</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody>
                                            <tr class="text-center" v-if="!podLeaders.length" >
                                                <td colspan="4">
                                                    No users found
                                                </td>
                                            </tr>
                                            <tr v-for="user in podLeaders">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-45px symbol-circle">
                                                    <span v-if="!user.avatar_url" class="symbol-label bg-light-danger text-danger fs-6 fw-bolder ">{{ user.name[0] }}</span>
                                                    <img v-if="user.avatar_url" :src="user.avatar_url">
                                                </div>
                                                <!--end::Avatar-->
                                                <td class="text-start">
                                                    <span style="cursor: pointer" class="text-gray-600 fw-bold fs-6">{{ user.name }}</span>
                                                    <div class="fw-semibold text-muted">{{ user.email }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info">{{ user.pod_users_count || "N/A" }}</span>
                                                </td>
                                                <td>
                                                    <span 
                                                    @click="remove(user)"
                                                    style="cursor: pointer"
                                                    class="badge badge-danger">Remove</span>
                                                    
                                                    <span 
                                                    @click="addUser(user)"
                                                    style="cursor: pointer; margin-left: 10px"
                                                    class="badge badge-primary">Add User</span>                                                    
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

    <modal 
    :value="addUserModal"
    modalsize="modal-xl"
    modal-body-class="mx-2"
    @change="setPodModalStatus($event)"
    v-model="addUserModal">
    <template v-slot:title>
        Manage {{ selectedUser?.name }} Pod Users
    </template>
    <template v-slot:body>
        <market-pod-user
        v-if="addUserModal"
        :leader="selectedUser"
        :market="selectedMarket"
        @refresh="refreshList()"
        ></market-pod-user>
    </template>
    </modal>

</template>

<script>

import ResponseMixins from "Mixins/response.js";
import MarketView from "./MarketView";
import MarketCommonUserTable from "./MarketCommonUserTable";
import MarketPodUser from "./MarketPodUser";

export default {
    name: "market-appoint-pod-leader",
    
    components: {
        MarketView,
        MarketCommonUserTable,
        MarketPodUser
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

        markets: [],
        users: [],
        links: [],
        prev_page_url: null,
        next_page_url: null,
        current_url: null,
        search: null,

        selectedMarket: {},
        selectedUser: {},
        podLeaders: [],

        addUserModal: false

    }),

    mixins: [ResponseMixins],

    async mounted() {
        await this.fetch();
        if(this.selectFirst && this.markets.length) {
            this.selectMarket(this.markets[0]);
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
                this.markets = res.data.data;
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

        selectMarket(market) {
            this.selectedMarket = market;
            this.fetchPodLeaders();
            this.$refs.commonUserTable.loadUsers(market);
        },

        fetchPodLeaders() {
            const url = this.selectedMarket.pod_leaders_url;
            axios.get(url)
                .then((res) => {
                    this.podLeaders = res.data.pod_leaders;
                }).catch((err) => {
                    console.log(err);
                })
        },

        remove(user) {
            Swal.fire({
                title: "Remove from Pod Leaders?",
                html: `Are you sure to remove ${user.name} from market pod leaders?`,
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
                    axios.post(`/markets/${this.selectedMarket.id}/pod-leaders/remove`, data)
                        .then((response) => {
                            this.refresh();
                            this.$refs.commonUserTable.loadUsers(this.selectedMarket);                        
                            Swal.fire("User Removed", "User removed from pod leaders.", "success");
                        }).catch((error) => {
                            this.parseError(error, null, {});                            
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });
        },

        setPodModalStatus(status) {
            this.addUserModal = status;
        },

        addUser(user) {
            this.selectedUser = user;
            this.addUserModal = true;
        },

        refreshList() {
            this.fetchPodLeaders();
            this.$refs.commonUserTable.loadUsers(this.selectedMarket);
        },

        refresh() {
            this.fetchPodLeaders();
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