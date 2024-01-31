<template>
<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-xxl">
        <div class="d-flex flex-column flex-lg-row">
            <div class="flex-column flex-lg-row-auto w-100 w-lg-400px w-xl-500px mb-10 mb-lg-0">
                <market-common-user-table
                ref="commonUserTable"
                appoint-type="Pod Users"
                :custom-appoint-url="`/users/${leader.id}/appoint-pod-users`"
                :agent-only="true"
                @appointed="refresh()"
                ></market-common-user-table>
            </div>
            <div class="flex-lg-row-fluid ms-lg-5 ms-xl-5">
                <!--begin::Table widget 14-->
                <div class="card card-flush h-md-100">
                    <!--begin::Header-->
                    <div class="card-header pt-7">
                        <!--begin::Title-->
                        <h3 class="card-title align-items-start flex-column">
                            <span class="fs-4 fw-bold text-gray-900 text-hover-primary me-1 mb-2 lh-1">{{ leader?.name }} — Pod Users</span>
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
                                            <th class="min-w-100px">Image</th>
                                            <th class="min-w-175px">Name</th>
                                            <th class="min-w-100px">Action</th>
                                        </tr>
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody>
                                        <tr class="text-center" v-if="!podUsers.length" >
                                            <td colspan="3">
                                                No users found
                                            </td>
                                        </tr>
                                        <tr v-for="user in podUsers">
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
import ResponseMixins from "Mixins/response.js";
import MarketCommonUserTable from "./MarketCommonUserTable";

export default {
    name: "market-pod-user",

    components: {
        MarketCommonUserTable
    },

    props: {
        leader: {
            type: Object,
            default: {}
        },

        market: {
            type: Object,
            default: {}           
        }
    },

    data: () => ({
        podUsers: [],
        isLoading: false,
    }),


    mounted() {
        this.fetchPodUsers();
        this.$refs.commonUserTable.loadUsers(this.market);
    },

    mixins: [ ResponseMixins ],

    methods: {

        fetchPodUsers() {
            const url = `/users/${this.leader?.id}/pod-users`;
            axios.get(url)
                .then((res) => {
                    this.podUsers = res.data.users;
                }).catch((err) => {
                    this.parseError(err);
                });
        },
        
        remove(user) {
            Swal.fire({
                title: `Remove from ${this.leader?.name} Pod Users?`,
                html: `Are you sure to remove ${user.name} ${this.leader?.name} pod users?`,
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
                    axios.post(`/users/${this.leader?.id}/pod-users/remove`, data)
                        .then((response) => {
                            this.refresh();
                            this.$refs.commonUserTable.loadUsers(this.market);                            
                            Swal.fire("User Removed", `User removed from ${this.leader?.name} pod users`, "success");
                        }).catch((error) => {
                            this.parseError(error, null, {});                            
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });
        },

        refresh() {
            this.$emit("refresh");
            this.fetchPodUsers();
        }

    }
}
</script>