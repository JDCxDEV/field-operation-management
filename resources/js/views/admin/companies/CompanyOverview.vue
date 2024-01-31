<template>
<!--begin::Navbar-->
<div class="app-container container-xxl">
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">
            <!--begin::Details-->
            <div
            v-if="isMobileScreen" 
            class="justify-content-end d-flex mb-4">
                <options
                @updateCompany="updateCompany()"
                @addMarket="addMarket()"
                @addUser="addUser()"
                />
            </div>
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin::Info-->
                <div 
                :class="{'w-100': isMobileScreen}"
                class="flex-grow-1">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::User-->
                        <div class="d-flex flex-column">
                            <!--begin::Name-->
                            <div class="d-flex align-items-center mb-2">
                                <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ company.company }}</a>
                            </div>
                            <!--end::Name-->

                            <!--begin::Info-->
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                <a :href="`tel:+${company.phone}`" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <span class="svg-icon svg-icon-4 me-1">
                                        <i class="fa-solid fa-phone"></i>&nbsp;
                                    </span>
                                    {{ company.phone }}
                                </a>
                            </div>

                            <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2">
                                <a 
                                :class="{'me-6': !isMobileScreen}"
                                :href="`https://maps.google.com/?q=${company.address_1} ${company.city},${company.state},${company.zip}`"
                                target="_blank"
                                class="text-gray-400 text-hover-primary mb-2">
                                    <i class="fa-solid fa-map-location-dot"></i>&nbsp;
                                    {{ company.address_1 }}{{ company.address_2 ? `, ${company.address_2 }` : "" }} {{ company.city }}, {{ company.state }}, {{ company.zip }}
                                </a>
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::User-->
                        <div
                        v-if="!isMobileScreen" 
                        class="d-flex my-4">
                            <options
                            @updateCompany="updateCompany()"
                            @addMarket="addMarket()"
                            @addUser="addUser()"
                            />
                        </div>
                        <!--end::Actions-->
                    </div>
                    <!--end::Title-->

                    <!--begin::Stats-->
                    <div v-if="isMobileScreen" class="row">
                        <div 
                        v-for="stat in stats"
                        class="col-6">
                            <div class="w-100 border border-gray-300 border-dashed rounded py-3 px-4 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="stats-text-wrapper fs-6 fw-bolder">{{ stat.name }}</div>
                                </div>
                                <!--end::Number-->

                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ company[stat.key] }}</div>
                                <!--end::Label-->
                            </div>
                        </div>
                    </div>
                    <!--begin::Stats-->
                    <div v-if="!isMobileScreen" class="d-flex flex-wrap flex-stack">
                        <!--begin::Wrapper-->
                        <div class="d-flex flex-column flex-grow-1 pe-8">
                            <!--begin::Stats-->
                            <div class="d-flex flex-wrap">
                                
                                <!--begin::Stat-->
                                <div 
                                v-for="stat in stats"
                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <!--begin::Number-->
                                    <div class="d-flex align-items-center">
                                        <div class="fs-6 fw-bolder">{{ stat.name }}</div>
                                    </div>
                                    <!--end::Number-->

                                    <!--begin::Label-->
                                    <div class="fw-bold fs-6 text-gray-400">{{ company[stat.key] }}</div>
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
<div v-show="currentTab == 'users'">
    <!--begin::details View-->
    <user-table 
    ref="userTable"
    :fetch-url="usersFetchUrl"
    :company-id="company.id"
    :enable-market-filter="false"
    :enable-company-filter="false"
    :disable-actions="true"
    :auto-fetch="false"
    :companies-fetch-url="companiesFetchUrl"
    markets-fetch-url="/markets/fetch"
    store-url="/users/store"
    @refresh="fetch()"        
    />
    <!--end::details View-->
</div>

<div v-if="currentTab == 'coordinators'">
    <company-appoint-coordinator 
        :fetch-url="companiesFetchUrl"
        :select-first="true"
        @refresh="fetch()"
    />
</div>

<div v-show="currentTab == 'markets'">
    <!--begin::details View-->
    <market-table
    ref="marketTable"
    :store-url="marketsStoreUrl"
    :fetch-url="marketsFetchUrl"
    :company-id="company.id"
    @refresh="fetch()"     
    ></market-table>
    <!--end::details View-->
</div>

<div v-show="currentTab == 'market-directors'">
    <!--begin::details View-->
    <user-table
    ref="marketDirectors"
    :fetch-url="usersFetchUrl"
    :company-id="company.id"
    :can-add="false"
    :enable-market-filter="false"
    :enable-company-filter="false"
    :disable-actions="true"
    :auto-fetch="false"
    :market-directors-only="true"
    @refresh="fetch()"
    />
    <!--end::details View-->
</div>

<modal 
    :value="showModal"
    modalsize="modal-dialog-centered mw-650px"
    @change="setModalStatus($event)"
    v-model="showModal">
    <template v-slot:title>
        Update Company
    </template>
    <template v-slot:body>
        <company-view
        ref="companyView"
        :child-component="true"
        @cancel="setModalStatus(false)"
        @updated="fetch()"  
        ></company-view>
    </template>
</modal>

</template>
<script>

import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import ScreenMixins from "Mixins/screen.js";
import BlueCheckMarkIcon from "Components/icons/BlueCheckMarkIcon";
import ChatRectangleIcon from "Components/icons/ChatRectangleIcon";
import MapIcon from "Components/icons/MapIcon";
import PhoneIcon from "Components/icons/PhoneIcon";
import CompanyView from "./CompanyView";
import Options from "./Options";

export default {
    name: "company-overview",

    components: {
        BlueCheckMarkIcon,
        ChatRectangleIcon,
        MapIcon,
        PhoneIcon,
        Options
    },

    props: {
        
        usersFetchUrl: {
            type: String,
            default: ""
        },
        
        companiesFetchUrl: {
            type: String,
            default: ""
        },

        marketsStoreUrl: {
            type: String,
            default: ""
        },

        marketsFetchUrl: {
            type: String,
            default: ""
        },

        companyFindUrl: {
            type: String,
            default: ""
        },

    },

    data: () => ({

        company: {},

        tabs: [
            { name: "Markets", key: "markets" },
            { name: "Coordinators", key: "coordinators" },
            { name: "Market Directors", key: "market-directors" },
            { name: "Users", key: "users" }
        ],
        currentTab: "markets",

        stats: [
            { name: "Markets", key: "markets_count" },
            { name: "Coordinators", key: "coordinators_count" },
            { name: "Market Directors", key: "market_directors_count" },
            { name: "Users", key: "users_count" },
        ],

        showModal: false,        
    }),

    mounted() {
        this.fetch();
    },

    mixins: [ResponseMixins, FormatterMixins, ScreenMixins],

    methods: {

        fetch() {
            axios.get(this.companyFindUrl)
                .then((res) => {
                    this.company = res.data.company;
                    this.company.phone = this.phoneFormat(res.data.company.phone);
                }).catch((error) => {
                    console.log(error);
                }).finally(() => {
                });
        },

        selectTab(tab) {
            this.$nextTick(() => {
                this.currentTab = tab.key
                let table;
                switch (tab.key) {
                    case "users":
                        table = this.$refs.userTable;
                        break;
                    case "market-directors":
                        table = this.$refs.marketDirectors;
                        break;
                
                }
                if(table) {
                    setTimeout(() => {
                        table.fetch();
                    }, 100);
                }
            });
        },

        setModalStatus(status) {
            this.showModal = status;
        },    

        updateCompany() {
            this.$refs.companyView.setAccessType("edit");
            this.$refs.companyView.fetch(this.company);
            this.showModal = true;
        },

        addMarket() {
            const tab = this.tabs.find((tab => tab.key == "markets"));
            this.selectTab(tab);
            this.$refs.marketTable.addMarket();
        },

        addUser() {
            const tab = this.tabs.find((tab => tab.key == "users"));
            this.selectTab(tab);
            this.$refs.userTable.addUser();
        }

    }
}
</script>