<template>
<div class="app-container container-xxl">
    <div class="card mb-5 mb-xl-10">
        <div class="card-body pt-9 pb-0">

            <div
            v-if="isMobileScreen" 
            class="justify-content-end d-flex mb-4">
                <options
                @updateMarket="updateMarket()"
                @addUser="addUser()"
                />
            </div>

            <!--begin::Details-->
            <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                <!--begin::Info-->
                <div 
                :class="{'w-100': isMobileScreen}"
                class="flex-grow-1">
                    <!--begin::Title-->
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                        <!--begin::User-->
                        <div :class="{'d-flex flex-column': !isMobileScreen, 'w-100': isMobileScreen}">
                            <!--begin::Name-->
                            <div class="d-flex align-items-center mb-2">
                                <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ market.market_name }}</a>
                            </div>
                            <!--end::Name-->

                            <!--begin::Info-->
                            <div 
                            class="d-flex flex-wrap fw-bold fs-6 pe-2">
                                <a 
                                :class="{'me-5 d-flex align-items-center ': !isMobileScreen}"
                                href="#" class="text-gray-400 text-hover-primary mb-4">
                                    <i class="fa-sharp fa-solid fa-landmark"></i>&nbsp;
                                    {{ market.company_name }}
                                </a>
                            </div>
                            <div 
                            :class="{'d-flex flex-wrap mb-4': !isMobileScreen}"
                            class="contact-details-container fw-bold fs-6 pe-2">
                                <label 
                                :class="{'d-flex': !isMobileScreen}"
                                @click="call(market.phone)"
                                class="text-overflow align-items-center text-gray-400 text-hover-primary me-5 mb-2">
                                    <i class="fa-solid fa-phone"></i>&nbsp;
                                    {{ market.phone }}
                                </label>
                                <label 
                                :class="{'d-flex': !isMobileScreen}"
                                @click="email(market.email)"
                                class="text-overflow align-items-center text-gray-400 text-hover-primary mb-2">
                                    <i class="fa-solid fa-envelope"></i>&nbsp;
                                    {{ market.email }}
                                </label>
                            </div>
                            <div class="d-flex flex-wrap fw-bold fs-6 mb-4 pe-2 min-w-175px">
                                <a 
                                :class="{'me-6': !isMobileScreen}"
                                :href="`https://maps.google.com/?q=${market.address_1} ${market.city},${market.state},${market.zip}`"
                                target="_blank"
                                class="text-gray-400 text-hover-primary mb-2">
                                    <i class="fa-solid fa-map-location-dot"></i>&nbsp;
                                    {{ market.address_1 }}{{ market.address_2 ? `, ${market.address_2 }` : "" }} {{ market.city }}, {{ market.state }}, {{ market.zip }}
                                </a>
                            </div>
                            <!--end::Info-->
                        </div>
                        <!--end::User-->
                        <div 
                        v-if="!isMobileScreen"
                        class="d-flex my-4">
                            <options 
                            @updateMarket="updateMarket()"
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
                        :class="stat.colWidth">
                            <div class="w-100 border border-gray-300 border-dashed rounded py-3 px-4 mb-3">
                                <!--begin::Number-->
                                <div class="d-flex align-items-center">
                                    <div class="stats-text-wrapper fs-6 fw-bolder">{{ stat.name }}</div>
                                </div>
                                <!--end::Number-->
                                <!--begin::Label-->
                                <div class="fw-bold fs-6 text-gray-400">{{ market[stat.key] }}</div>
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
                                    <div class="fw-bold fs-6 text-gray-400">{{ market[stat.key] }}</div>
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
                        :class="{'nav-item-width': isMobileScreen}"
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
<!--end::Navbar-->
</div>
<div v-show="currentTab == 'users'">
    <!--begin::details View-->
    <user-table
    ref="userTable" 
    :fetch-url="usersFetchUrl"
    :company-id="market.company_id"
    companies-fetch-url="/companies/fetch"
    :market-id="market.id"
    markets-fetch-url="/markets/fetch"
    :enable-market-filter="false"
    :enable-company-filter="false"
    :disable-actions="true"
    :auto-fetch="false"
    store-url="/users/store"
    @refresh="fetch()"
    />
    <!--end::details View-->
</div>

<div v-if="currentTab == 'directors'">
    <market-appoint-director
        ref="marketAppoint" 
        :fetch-url="marketsFetchUrl"
        :select-first="true"
        @refresh="fetch()"
    />
</div>

<div v-if="currentTab == 'pod-leaders'">
    <market-appoint-pod-leader
        ref="marketAppointPodLeader" 
        :fetch-url="marketsFetchUrl"
        :select-first="true"
        @refresh="fetch()"
    />
</div>

<modal 
    :value="showModal"
    modalsize="modal-dialog-centered mw-650px"
    @change="setModalStatus($event)"
    v-model="showModal">
    <template v-slot:title>
        {{ renderAccessType }} Market
    </template>
    <template v-slot:body>
        <market-view
        ref="marketView"
        :store-url="storeUrl"
        companies-fetch-url="/companies/fetch"
        :child-component="true"
        :company-id="companyId"
        @cancel="setModalStatus(false)"
        @updated="fetch()"      
        ></market-view>
    </template>
</modal>

</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import ScreenMixins from "Mixins/screen.js";
import BlueCheckMarkIcon from "Components/icons/BlueCheckMarkIcon";
import ChatRectangleIcon from "Components/icons/ChatRectangleIcon";
import EnvelopeIcon from "Components/icons/EnvelopeIcon";
import MapIcon from "Components/icons/MapIcon";
import PhoneIcon from "Components/icons/PhoneIcon";
import BuildingIcon from "Components/icons/BuildingIcon";
import MarketView from "./MarketView";
import MarketAppointPodLeader from "./MarketAppointPodLeader";
import Options from "./Options";

export default {
    name: "market-overview",
    
    components: {
        BlueCheckMarkIcon,
        ChatRectangleIcon,
        MapIcon,
        EnvelopeIcon,
        BuildingIcon,
        PhoneIcon,
        MarketView,
        MarketAppointPodLeader,
        Options
    },

    props: {
        usersFetchUrl: {
            type: String,
            default: ""
        },
        
        marketFindUrl: {
            type: String,
            default: ""
        },

        marketsFetchUrl: {
            type: String,
            default: ""            
        }
        
    },

    data: () => ({

        market: {},
        tabs: [
            { name: "Market Directors", key: "directors" },
            { name: "Market Pod Leaders", key: "pod-leaders" },            
            { name: "Market Users", key: "users" },
        ],

        stats: [
            { name: "Market Directors", key: "directors_count", colWidth: "col-6" },
            { name: "Market Pod Leaders", key: "pod_leaders_count", colWidth: "col-6" },            
            { name: "Market Users", key: "users_count", colWidth: "col-12" },
        ],

        currentTab: "directors",
        showModal: false,
    }),

    async mounted() {
        await this.fetch();
    },

    mixins: [ResponseMixins, FormatterMixins, ScreenMixins],

    methods: {
        async fetch() {
            await axios.get(this.marketFindUrl)
                .then((res) => {
                    this.market = res.data.market;
                    this.market.phone = this.phoneFormat(res.data.market.phone);
                    this.$nextTick(() => {
                        this.$refs.userTable.fetch();
                    });
                }).catch((error) => {
                    console.log(error);
                }).finally(() => {
                });
        },

        selectTab(tab) {
            this.$nextTick(() => {
                this.currentTab = tab.key
            });
        },

        setModalStatus(status) {
            this.showModal = status;
        },

        updateMarket() {
            this.$refs.marketView.setAccessType("edit");      
            this.$refs.marketView.fetch(this.market);
            this.showModal = true;
        },

        addUser() {
            const tab = this.tabs.find((tab => tab.key == "users"));
            this.selectTab(tab);
            this.$refs.userTable.addUser();    
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