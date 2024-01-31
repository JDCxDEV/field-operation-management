<template>
<div id="kt_app_content_container">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="col-12 pt-3">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="text-left">
                            <input readonly class="form-control form-control-solid" placeholder="Pick date range" id="createdAtDateRange"/>
                        </div>
                    </div>
                    <div class="col-md-7"></div>
                    <div class="col-md-2">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle col-12" type="button" id="exportMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                Export
                            </button>
                            <ul class="dropdown-menu col-12" aria-labellezdby="exportMenu">
                                <li>
                                    <a 
                                    @click="exportForm('pdf')"
                                    class="dropdown-item" href="#">PDF</a>
                                </li>
                                <li>
                                    <a 
                                    @click="exportForm('csv')"
                                    class="dropdown-item" href="#">CSV</a>
                                </li>
                                <li>
                                    <a 
                                    @click="exportForm('xlsx')"
                                    class="dropdown-item" href="#">XLSX</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="flex-nowrap col-12">
                                <div class="overflow-hidden flex-grow-1">
                                    <select
                                    id="formStateSelect"
                                    v-model="state"
                                    class="form-select form-select-lg" data-control="select2" data-placeholder="Select state">
                                        <option value="null" disabled selected>Select state</option>
                                        <option value="all">All States</option>
                                        <option v-for="(state, index) in states" :value="state.abbreviation">{{ state.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="input-group">
                            <div class="flex-nowrap col-12">
                                <div class="overflow-hidden flex-grow-1">
                                    <select
                                    id="formStatusSelect"
                                    v-model="form_status"
                                    class="form-select form-select-lg" data-control="select2" data-placeholder="Select status">
                                        <option value="null" disabled selected>Select status</option>
                                        <option value="all">All Statuses</option>
                                        <template v-for="status in statuses">
                                            <option :value="status.id">{{ status.status }}</option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group">
                            <input 
                            v-model="searchQuery"
                            placeholder="Search forms"
                            type="text" class="form-control form-control-solid">
                            <div class="input-group-append">
                                <button @click="fetch('search')" type="button" class="btn btn-success">
                                Go
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="button" class="btn btn-light-primary me-3 col-12" data-kt-menu-static="true" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
                                </svg>
                            </span>
                            <span class="filter-text">Filter</span>
                        </button>
                        <div class="menu menu-sub menu-sub-dropdown w-400px w-md-425px" data-kt-menu="true">
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>
                            <div class="separator border-gray-200"></div>
                            <div class="px-7 py-5">
                                <div class="mb-10">
                                    <div class="mb-7">
                                        <div class="input-group">
                                            <div class="flex-nowrap col-12">
                                                <div class="overflow-hidden flex-grow-1">
                                                    <label>Company</label>
                                                    <select
                                                    id="formCompanySelect"
                                                    v-model="company"
                                                    class="form-select form-select-lg" data-control="select2" data-placeholder="Select company">
                                                        <option value="null" disabled selected>Select company</option>
                                                        <option value="all">All Companies</option>
                                                        <template v-for="company in companies">
                                                            <option :value="company.id">{{ company.company }}</option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-7">
                                        <div class="input-group">
                                            <div class="flex-nowrap col-12">
                                                <div class="overflow-hidden flex-grow-1">
                                                    <label>Market</label>
                                                    <select
                                                    id="formMarketSelect"
                                                    v-model="market"
                                                    class="form-select form-select-lg" data-control="select2" data-placeholder="Select market">
                                                        <option value="null" disabled selected>Select market</option>
                                                        <option value="all">All Markets</option>
                                                        <template v-for="market in markets">
                                                            <option :value="market.id">{{ market.market_name }}</option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                         
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button 
                                    type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Close</button>
                                    <button 
                                    @click="resetFilter()"
                                    type="reset" class="btn btn-warning btn-active-light-warning fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                    <button 
                                    @click="filter()"
                                    type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div 
            :class="{'table-min-height': forms.length}"
            class="table-responsive base-table">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-125px">Application ID</th>
                            <th class="min-w-125px">Created At</th>
                            <th class="min-w-125px">Agent Name</th>
                            <th class="min-w-125px">Client Name</th>
                            <th class="min-w-125px">Company</th>
                            <th class="min-w-125px">Client State</th>
                            <th class="min-w-125px">Member Count</th>                  
                            <th class="min-w-125px">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        <tr v-for="form in forms">
                            <td>
                                <a 
                                @click="openModal('update', form)"
                                type="button" class="text-primary">
                                    {{ form.application_id }}
                                </a>
                            </td>
                            <td>
                                {{ form.create_at_readable }}
                            </td>
                            <td>
                                {{ form.creator_name }}
                            </td>
                            <td>
                                {{ form.name }}
                            </td>
                            <td>
                                {{ form.company_name }}
                            </td>
                            <td>
                                {{ form.client_state ?? "—" }}
                            </td>
                            <td>
                                <span class="badge badge-success">{{ form.member_count }}</span>
                            </td>
                            <td>
                                <span v-if="!form.current_status" :class="form.statusInfo.class">{{ form.statusInfo.label }}</span>
                                <span v-if="form.current_status" class="badge badge-primary">{{ form.current_status }}</span>                                
                            </td>
                        </tr>
                        <tr v-if="!forms.length" class="odd my-10">
                            <td valign="top" colspan="9" class="text-center">No matching records found</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-5">
                <div class="col-sm-12 col-md-4 d-flex align-items-center justify-content-center justify-content-md-start">
                    <span class="text-gray-600">Total: <strong>{{ current_count || 0 }}</strong> / <strong>{{ total }}</strong></span>
                </div>
                <div class="mt-4 mb-mt-0 col-sm-12 col-md-8 d-flex align-items-center justify-content-center justify-content-md-end">
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

    <div
        id="formDrawer"
        class="bg-body"
        data-kt-drawer="true"
        data-kt-drawer-activate="true"
        data-kt-drawer-toggle="#formClose"
        data-kt-drawer-close="#formClose"
        data-kt-drawer-overlay="true"
        data-kt-drawer-width="{default:'100%', md: '100%', lg: '80%'}"
    >
        <div class="card shadow-none border-0 rounded-0" style="width: 100%">
            <div class="card-header" id="formHeader">
                <h3 class="card-title fw-bold text-dark">{{ renderAccessType }} - {{ formApplicationId }} </h3>
                <div class="card-toolbar">
                    <div 
                    v-if="selectedForm"
                    class="form-group mr-1">
                        <div class="flex-nowrap">
                            <div class="overflow-hidden flex-grow-1">
                                <select 
                                v-model="selectedForm.status_id"
                                @change="updateStatus"
                                class="form-select form-select-lg" data-control="select2" data-placeholder="Select status"  data-hide-search="true">
                                    <option value="null" disabled selected>Select status</option>
                                    <template v-for="status in statuses">
                                        <option :value="status.id">{{ status.status }}</option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button 
                    @click="resetForm()"
                    type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="formClose">
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
            <div class="card-body position-relative" id="kt_activities_body">
                <div class="position-relative me-n5 pe-5">
                    <FormAccordionView
                    v-if="selectedForm"
                    :fetch-url="`/agent-submission-forms/${selectedForm.id}/find`"
                    fetch-template-url="/form-templates/fetch-active"
                    :additional-files-url="`/agent-submission-forms/additional-files/${selectedForm.id}`"
                    :save-signature-url="`/agent-submission-forms/save-signature/${selectedForm.id}`"
                    :submit-url="`/agent-submission-forms/submit/${selectedForm.id}`"
                    @close="resetForm()"
                    />
                </div>
            </div>
        </div>
    </div>
    <!--end::View component-->
</div>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
import FormatterMixins from "Mixins/formatter.js";
import StatesMixins from "Mixins/states.js";
import AgentSubmissionForm from "./AgentSubmissionForm.vue";
import FormAccordionView from "./FormAccordionView.vue";

export default {
    name: "forms-table",

    components: {
        AgentSubmissionForm,
        FormAccordionView
    },

    props: {
        fetchUrl: {
            type: String,
            default: ""
        },

        createUrl: {
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
    },

    computed: {
        renderAccessType() {
            if(this.accessType == "view") {
                return "Viewing";
            } else {
                return "Updating";
            }
        },

        formApplicationId() {
            return this.selectedForm ? this.selectedForm.application_id : "";
        }
    },

    data: () => ({
        forms: [],
        
        links: [],
        prev_page_url: null,
        next_page_url: null,
        current_url: null,
        current_count: 0,
        total: 0,
        
        createdAt: "",
        searchQuery: "",
        state: "all",
        status: "all",
        form_status: "all",
        company: "all",
        market: "all",

        searchTypes: [
            { name: "Application ID", value: "application_id" },
            { name: "Address", value: "client_address_line_1" },
            { name: "Email", value: "client_email" },         
        ],

        selectedForm: "",
        accessType: "view",

        drawer: "",
        statuses: [],
        companies: [],
        markets: [],        
        setup: false,

        exporting: false,
    }),

    mounted() {
        KTDrawer.createInstances();
        var drawerElement = document.querySelector("#formDrawer");
        this.drawer = KTDrawer.getInstance(drawerElement);
        
        this.initializeDatePicker();
    
        let formStatusSelect = document.getElementById('formStatusSelect');
        formStatusSelect.onchange = (event) => {
            this.form_status = event.target.value;
            this.fetch();
        }

        let formStateSelect = document.getElementById('formStateSelect');
        formStateSelect.onchange = (event) => {
            this.state = event.target.value;
            this.fetch();
        }

        let formCompanySelect = document.getElementById('formCompanySelect');
        formCompanySelect.onchange = (event) => {
            this.company = event.target.value;
        }

        let formMarketSelect = document.getElementById('formMarketSelect');
        formMarketSelect.onchange = (event) => {
            this.market = event.target.value;
        }    

        this.fetch();
        this.fetchStatuses();
        this.fetchCompanies();
        this.fetchMarkets();
    },

    mixins: [ResponseMixins, FormatterMixins, StatesMixins],

    methods: {

        initializeDatePicker() {
            let $this = this;
            $("#createdAtDateRange").daterangepicker({
                startDate: moment(),
                endDate: moment().add(1, "month"),
            }, function (start, end, label) {
                $this.createdAt = `${start} - ${end}`;
                $this.fetch();
            });
        },

        fetchStatuses() {
            axios.get(`/statuses/fetch?nopaginate=true`)
                .then((res) => {
                    this.statuses = res.data;
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
                axios.get(this.marketsFetchUrl)
                .then((res) => {
                    this.markets = res.data.markets;
                }).catch((err) => {
                    console.log(err);
                });
            }
        },

        fetch(isRefresh = null, fetchUrl = null) {
            this.current_url = fetchUrl ? fetchUrl: this.fetchUrl;
            const params =  {
                params: { 
                    status: this.status,
                    created_at: this.createdAt, 
                    search: this.searchQuery,
                    state: this.state,
                    form_status: this.form_status,
                    company: this.company,
                    market: this.market
                }
            };

            axios.get(`${this.current_url}`, params)
            .then((res) => {
                this.forms = res.data.data.map((data) => {
                    return { ...data, client_phone_formatted: this.phoneFormat(data.client_phone), statusInfo: this.renderStatus(data.status) }
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

        filter() {
            this.fetch('search');
        },

        openModal(type, form) {
            this.selectedForm = form;
            this.accessType = type;
            this.$nextTick(() => {
                this.drawer.show();
            });
        },

        updateStatus() {
            const data = {
                status_id: this.selectedForm.status_id
            };
            axios.post(`agent-submission-forms/update-status/${this.selectedForm.id}`, data)
                .then((res) => {
                    this.fetch(false, this.current_url);
                })
                .catch((error) => {
                    console.log(err);
                });
        },

        exportForm(type) {
            if(this.exporting) {
                this.parseInfo("Exporting, please wait.");
                return;
            }
            this.exporting = true;
            this.parseInfo("Exporting, please wait.");
            const params =  {
                status: this.status,
                created_at: this.createdAt, 
                search: this.searchQuery,
                state: this.state,
                form_status: this.form_status
            };
            axios.post(`agent-submission-forms/export/${type}`, params, {responseType: 'blob'})
                .then((res) => {
                    const timeAndDate = moment().format("MMDDYYYY-hhmmss");
                    const filename = `forms-${timeAndDate}`;
                    var file = window.URL.createObjectURL(new Blob([res.data]));
                    var docUrl = document.createElement('a');
                    docUrl.href = file;
                    docUrl.setAttribute('download', `${filename}.${type}`);
                    docUrl.click();
                    console.log(docUrl);
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                    this.exporting = false;
                });
        },

        refresh() {
            this.resetFilter();
            this.fetch(true);
        },
        
        resetForm() {
            document.getElementById("formClose").click();
            this.selectedForm = "";
        },

        resetFilter() {
            this.initializeDatePicker();
            this.createdAt = "";
            this.state = "all";
            this.status = "all";
            this.market = "all";
            this.company = "all";
            $('#formStatusSelect').val("all").trigger("change");
            $('#formStateSelect').val("all").trigger("change");
            $('#formCompanySelect').val("all").trigger("change");
            $('#formMarketSelect').val("all").trigger("change");
        },

        renderStatus(status) {
            switch(status) {
                case 0:
                case "0":
                    return { class: "badge badge-info", label: "Draft" };
                case 1:
                case "1":
                    return { class: "badge badge-success", label: "Submitted" };
            }
        },

    }
}
</script>