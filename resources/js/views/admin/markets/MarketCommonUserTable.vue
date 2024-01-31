<template>
<div class="card card-flush">
    <div class="card-header pt-7">
        <div class="d-flex align-items-center position-relative my-1" style="max-height: 500px">
            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                </svg>
            </span>
            <input 
            v-model="search"
            :disabled="!market.id"
            @input="reset()"
            @keyup.enter="fetch('search')"
            type="text" class="form-control form-control-solid w-250px px-15" placeholder="Search user" />
            <button 
            :disabled="!market.id"
            @click="fetch('search')" type="button" class="btn btn-success ms-20">
                Go
            </button>
            <button 
            :disabled="!selectedUsers.length || isLoading"
            @click="appoint()"
            class="btn btn-primary text-center ms-4">
                <i class="fa-solid fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body pt-6">
        <div class="scroll-y me-n5 pe-5 h-400px mb-12" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_toolbar, #kt_app_toolbar, #kt_footer, #kt_app_footer, #kt_chat_contacts_header" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_contacts_body" data-kt-scroll-offset="5px">
            <!--begin::User-->
            <div 
            v-for="user in currentUsers" 
            @click="select(user)"
            class="d-flex flex-stack py-4"
            style="cursor: pointer"
            >
                <!--begin::Details-->
                <div class="d-flex align-items-center">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-45px symbol-circle ">
                        <span v-if="!user.avatar_url" class="symbol-label bg-light-danger text-danger fs-6 fw-bolder ">{{ user.name[0] }}</span>
                        <img v-if="user.avatar_url" :src="user.avatar_url">
                    </div>
                    <!--end::Avatar-->
                    <!--begin::Details-->
                    <div class="ms-5">
                        <a href="javascript:void(0)" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">{{ user.name }}</a>
                        <div class="fw-semibold text-muted">{{ user.email }}</div>
                    </div>
                    <!--end::Details-->
                </div>
                <!--end::Details-->

                <div class="d-flex flex-column align-items-end ms-2">
                    <div 
                    v-if="user.selected"
                    class="symbol symbol-45px symbol-circle">
                        <span class="symbol-label bg-primary fs-6 fw-bolder">
                            <i class="text-white fa fa-check"></i>
                        </span>
                    </div>
                </div>
            </div>
            <!--end::User-->

            <div 
            v-if="!currentUsers.length"
            class="text-center mt-7">
                <span>No users found</span>
            </div>
        </div>

        <div 
        v-if="links.length"
        class="row mt-4 pagination-bottom">
            <div class="col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start"></div>
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
    </div>
</div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
export default {
    name: "market-common-user-table",

    props: {
        appointType: {
            type: String,
            default: "Directors",
        },

        customAppointUrl: {
            type: String,
            default: ""
        },

        agentOnly: {
            type: Boolean,
            default: false
        },

    },

    data: () => ({
        currentUsers: [],
        links: [],
        prev_page_url: null,
        next_page_url: null,
        current_url: null,        

        isLoading: false,

        selectedUsers: [],
        
        market: {},

        fetchUrl: "",
        appointUrl: ""
    }),

    mixins: [ ResponseMixins ],

    methods: {

        loadUsers(market) {
            this.market = market;
            this.selectedUsers = [];
            this.fetchUrl = `/markets/${this.market.id}/common-users`;
            if(this.appointType === "Directors") {
                this.appointUrl = `/markets/${this.market.id}/appoint`;
            } else if(this.appointType === "Pod Leaders") {
                this.appointUrl = `/markets/${this.market.id}/appoint-as-pod-leaders`;
            } else if (this.customAppointUrl) {
                this.appointUrl = this.customAppointUrl;
            }
            
            this.fetch();
        },

        fetch(isRefresh = null, fetchUrl = null) {
            this.current_url = fetchUrl ? fetchUrl : this.fetchUrl;
            const params =  {
                params: { search: this.search, agent_only: this.agentOnly }
            };

            axios.get(this.current_url, params)
                .then((res) => {
                this.currentUsers = res.data.users.data.map((user) => {
                    const selected = this.selectedUsers.indexOf(user.id) >= 0 ? true: false;
                    return { ...user, selected: selected };
                });
                this.links = res.data.users.links;

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

        appoint() {
            Swal.fire({
                title: `Assign Market ${this.appointType}?`,
                html: `Are you sure to assign the selected ${this.appointType}?
                    <br /> <br />
                    <i>Note: <b>${this.selectedUsers.length}</b> user(s) will be assigned as ${this.appointType} in <b>${this.market.market_name}</b>.</i>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Assign"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    this.isLoading = true;
                    const data = {
                        userIds: this.selectedUsers
                    }
                    axios.post(this.appointUrl, data)
                        .then((response) => {
                            this.fetch();
                            this.$emit("appointed");
                            Swal.fire("User(s) Appointed", `User(s) has been assigned as ${this.appointType}`, "success");
                            this.selectedUsers = [];
                        }).catch((error) => {
                            this.parseError(error, null, {});                            
                        }).finally(() => {
                            this.isLoading = false;
                        });
                }
            });
        },

        select(user) {
            const index = this.selectedUsers.indexOf(user.id);
            if(index < 0) {
                this.selectedUsers.push(user.id);
                user.selected = true;
            } else {
                this.selectedUsers.splice(index, 1);
                user.selected = false;
            }
        },
        
        reset() {
            if(this.search == "") {
                this.fetch();
            }
        }
    }
}
</script>